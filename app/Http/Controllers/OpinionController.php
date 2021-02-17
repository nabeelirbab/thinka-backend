<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Generic\Core\GenericFormValidation;
use App\Generic\Core\GenericCreate;
use App\Generic\GenericController;
use App;
use App\Http\Controllers\Relation\NotificationBuilder as NotificationBuilder;

class OpinionController extends GenericController
{
    function __construct(){
        $this->model = new App\Models\Opinion();
        $this->tableStructure = [
            'columns' => [
            ],
            'foreign_tables' => [
            // 'opinion_' => [
            //     "is_child" => true, 
            //     "validation_required" => false,
            //     'foreign_tables' => [
            //         "user" => [
            //             'validation_required' => false,
            //                 'foreign_tables' => [
            //                     "user_basic_information" => 
            //                     'validation_required' => false,
            //                     "is_child" => false,
            //                 ]
            //             ]
            //         ]
            //     ]
            // ]
            ]
        ];
        $this->retrieveCustomQueryModel = function($queryModel, &$leftJoinedTable){
            $queryModel = $queryModel->where('user_id', $this->userSession('id'));
            return $queryModel;
        };
        $this->initGenericController();
    }
    public function create(Request $request){
        $entry = $request->all();
        $resultObject = [
          "success" => false,
          "fail" => false
        ];
        $validation = new GenericFormValidation($this->tableStructure, "create");
        if($validation->isValid($entry)){
            $entry['user_id'] = $this->userSession('id');
            $genericCreate = new GenericCreate($this->tableStructure, $this->model);
            if(!isset($entry['confidence'])){
                $entry['confidence'] = $entry['type'] > 0 ? 1 : 0;
            }
            $resultObject['success'] = $genericCreate->create($entry);
            if($resultObject['success']){
                $oldOpinions = (new App\Models\Opinion())
                    ->with('opinion_calculated_column')
                    ->where('user_id', $this->userSession('id'))
                    ->where('relation_id', $entry['relation_id'])
                    ->get();
                $this->responseGenerator->adddebug('id', $oldOpinions->toArray());
                $isChangeOpinion = false;
                foreach($oldOpinions as $opinion){
                    if($opinion->id != $resultObject['success']['id']){
                        $opinion->delete();
                        $isChangeOpinion = true;
                    }else{
                        $resultObject['success']['opinion_calculated_column'] = $opinion['opinion_calculated_column'];
                    }
                }
                $resultObject['success']['confidence'] = $entry['confidence'];
                $resultObject['success']['type'] = $entry['type'];
                $notificationMessage = json_encode($resultObject['success']);
                $notificationBuilder = new NotificationBuilder();
                $notificationBuilder->getUserToNotifyFromParentRelation($entry['relation_id']);
                $usersToNotify = $notificationBuilder->withNotificationUserExtraData(); // convert the value of each array element with extra data
                unset($usersToNotify[$this->userSession('id')]);
                $this->responseGenerator->adddebug('user_id', $this->userSession('id'));
                $this->responseGenerator->adddebug('users_notify', $usersToNotify);
                (new App\Models\Notification())->createRelationUpdateNotification($entry['relation_id'], $usersToNotify, $notificationMessage, 2);
            }
        }else{
          $resultObject['fail'] = [
            "code" => 1,
            "message" => $validation->validationErrors
          ];
        }
        $this->responseGenerator->setSuccess($resultObject['success']);
        $this->responseGenerator->setFail($resultObject['fail']);
        return $this->responseGenerator->generate();
    }
}
