<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Generic\Core\GenericFormValidation;
use App\Generic\Core\GenericCreate;
use App\Generic\GenericController;
use Illuminate\Support\Facades\Validator;
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
            $oldOpinions = (new App\Models\Opinion())
            ->with('opinion_calculated_column')
            ->where('user_id', $this->userSession('id'))
            ->where('relation_id', $entry['relation_id'])
            ->get();
            $entry['user_id'] = $this->userSession('id');
            // if(count($oldOpinions)){
            //     $entry['impact_amount'] = $oldOpinions[0]['impact_amount'];
            // }
            $genericCreate = new GenericCreate($this->tableStructure, $this->model);
            if(!isset($entry['confidence'])){
                $entry['confidence'] = $entry['type'] > 0 ? 1 : 0;
            }
            if(!isset($entry['type']) || $entry['type'] < 0){
                $entry['type'] = 0;
            }
            if(isset($entry['impact_amount'])){
                $entry['impact_amount'] = $entry['impact_amount'];
            }
            $resultObject['success'] = $genericCreate->create($entry);
            if($resultObject['success']){
                $this->responseGenerator->adddebug('id', $oldOpinions->toArray());
                $isChangeOpinion = false;
                foreach($oldOpinions as $opinion){
                    $opinion->delete();
                    $isChangeOpinion = true;
                }
                $newOpinion = (new App\Models\Opinion)->with('opinion_calculated_column')->find($resultObject['success']['id']);
                $resultObject['success'] = $newOpinion;
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
    public function changeImpact(Request $request){
        $requestParam = $request->all();
        $validator = Validator::make($requestParam, [
            "relation_id" => "required|exists:relations,id",
            "impact_amount" => "required|numeric",
        ]);
        if($validator->fails()){
            $this->responseGenerator->setFail([
                "code" => 1,
                "message" => $validator->errors()->toArray()
            ]);
            return $this->responseGenerator->generate();
        }
        $relationId = $requestParam['relation_id'];
        $opinion = (new App\Models\Opinion())->where('relation_id', $relationId)->where('user_id', $this->userSession('id'))->get()->toArray();
        $opinionModel = new App\Models\Opinion();
        if(count($opinion)){
            $opinionModel = $opinionModel->find($opinion[0]['id']);
        }else{
            $opinionModel->relation_id = $relationId;
            $opinionModel->user_id = $this->userSession('id');
        }
        $opinionModel->impact_amount = $requestParam['impact_amount'];
        $opinionModel->save();
        if($opinionModel->id){
            $opinionId = $opinionModel->id;
            $updatedOpinion = $opinionModel->where('id', $opinionId)->with('opinion_calculated_column')->get()->toArray();
            $this->responseGenerator->setSuccess(count($updatedOpinion) > 0 ? $updatedOpinion[0] : null);
        }
        return $this->responseGenerator->generate(); 
    }
}
