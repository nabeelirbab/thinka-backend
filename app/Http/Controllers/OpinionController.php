<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Generic\Core\GenericFormValidation;
use App\Generic\Core\GenericCreate;
use App\Generic\GenericController;
use Illuminate\Support\Facades\Validator;
use App;
use App\Http\Controllers\Relation\NotificationBuilder as NotificationBuilder;
use App\Http\Controllers\Relation\UserStatementLogicScore as UserStatementLogicScore;
use Mail;

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
            $latestOpinions = (new App\Models\Opinion())
            ->with('opinion_calculated_column')
            ->where('user_id', $this->userSession('id'))
            ->where('relation_id', $entry['relation_id'])
            ->orderBy('created_at', 'desc')
            ->limit(1)
            ->get();
            $entry['user_id'] = $this->userSession('id');
            // if(count($oldOpinions)){
            //     $entry['impact'] = $oldOpinions[0]['impact'];
            // }
            if(!isset($entry['confidence'])){
                $entry['confidence'] = $entry['type'] > 0 ? 1 : 0;
            }
            if(!isset($entry['type']) || $entry['type'] < 0){
                $entry['type'] = 0;
            }
            if(isset($entry['impact'])){
                $entry['impact'] = $entry['impact'];
            }
            $doCreateNew = true;
            if(count($latestOpinions)){
                $startDate = new \DateTime($latestOpinions[0]->created_at);
                $sinceStart = $startDate->diff(new \DateTime());
                if($sinceStart->h <= 12){ // update instead of creating
                    $doCreateNew = false;
                    $latestOpinions[0]->confidence = $entry['confidence'];
                    $latestOpinions[0]->type = $entry['type'];
                    $latestOpinions[0]->impact = $entry['impact'];
                    $latestOpinions[0]->save();
                    $resultObject['success'] = $latestOpinions[0]->toArray();   
                }
            }
            if($doCreateNew){
                $genericCreate = new GenericCreate($this->tableStructure, $this->model);
                $resultObject['success'] = $genericCreate->create($entry);      
                if(count($latestOpinions)){
                    $latestOpinions[0]->delete(); // delete latest opinion
                }
            }
            if($resultObject['success']){
                $isChangeOpinion = false;
                $newOpinion = (new App\Models\Opinion)->with([
                    'opinion_calculated_column', 
                    'relation' => function($query){
                        $query->select(['id', 'statement_id', 'virtual_relation_id']);
                    }, 
                    'relation.statement' => function($query){
                        $query->select(['id', 'text']);
                    },
                    'relation.virtual_relation.statement' => function($query){
                        $query->select(['id', 'text']);
                    }
                ])->find($resultObject['success']['id']);
                $resultObject['success'] = $newOpinion;
                $notificationMessage = json_encode($resultObject['success']);
                $notificationBuilder = new NotificationBuilder();
                $notificationBuilder->getUserToNotifyFromParentRelation($entry['relation_id']);
                $usersToNotify = $notificationBuilder->withNotificationUserExtraData(); // convert the value of each array element with extra data
                unset($usersToNotify[$this->userSession('id')]);
                if(isset($entry['get_user_statement_logic_scores'])){
                    $resultObject['success']['user_statement_logic_scores'] = (new UserStatementLogicScore())->calculateUserStatementLogicScore($entry['sub_relation_statement_id_list']);
                }
                $this->notifySubscribers($entry['relation_id'], $usersToNotify, $notificationMessage, $newOpinion);
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
    private function generateOpinionMessage($type, $confidence = 0, $impact = null){
        $typeDescriptions = [
            'I have no explicit opinion.',
            'I think the statement is false', // thumbs down
            'I think the statement is true', // but has no impact', // point
            'I think the statement is true' // and has Impact' // thumbs up
        ];
        if(!isset($typeDescriptions[$type])){
            return 'Unknown opinion';
        }
        $message = $typeDescriptions[$type];
        //impact
        if($impact !== null && $type > 0){
            if($impact === 0){
                if ($type == 2) $message .= ' though it has no impact';
            }else {
                $percentImpact = number_format($impact * 100, 0);
                $magnitudeMessage = '';
                if ($impact == 1 || $impact == -1){
                    $magnitudeMessage .= " and makes a critical";
                } else if ($impact * $impact >= 0.25){
                    $magnitudeMessage .= " and makes a strong";

                } else {
                    $magnitudeMessage .= 'and makes a <em class="font-weight-bold">' . $percentImpact . '%</em>';
                }
                if($impact < 0){
                    $message .= ` ${magnitudeMessage} counter impact`;
                }else if($impact > 0){
                    $message .= ` ${magnitudeMessage} supportive impact`;
                }
            } // impact == 0
        }
        // confidence
        if($type){
            $message .= ' with <em class="font-weight-bold"> '. (number_format($confidence * 100, 0)) .'%</em> confidence.';
        }
        return $message;
    }
    private function notifySubscribers($relationId, $usersToNotify, $notificationMessage, $opinion){
        (new App\Models\Notification())->createRelationUpdateNotification(
            $relationId,
            $usersToNotify,
            $notificationMessage,
            2
        );
        $userIdList = [];
        foreach($usersToNotify as $userId => $reason){
            $userIdList[] = $userId;
        }
        $users = (new App\Models\User())
            ->select(['id', 'email', 'username'])
            ->whereIn('id', $userIdList)
            ->where('id', '!=', $this->userSession('id'))
            ->get()->toArray();
        $statementText = $opinion['relation']['virtual_relation_id'] ? $opinion['relation']['virtual_relation']['statement']['text'] : $opinion['relation']['statement']['text'];
        $kebabStatement = preg_replace('/[[:space:]]+/', '-', strtolower($statementText));
        $opinionMessage = $this->generateOpinionMessage($opinion['type'], $opinion['confidence'], $opinion['impact']);
        $data = [
          'relationId' => $relationId,
          'statementText' => $statementText,
          'kebabStatement' => $kebabStatement,
          'notificationMessage' => $this->userSession('username') . ' made an opinion on a relation that you are following',
          'opinionMessage' => $opinionMessage
        ];
        
        $this->responseGenerator->addDebug('data', $data);
        if(config('app.MAIL_MAILER') === 'smtp'){
          foreach($users as $user){
            $data['username'] = $user['username'];
            Mail::send('opinion-created-notification', $data, function($message) use ($user) {
              $message->to($user['email'])
              ->subject('New Opinion on a Statement');
              $message->from('noreply@thinka.io','Thinka');
            });
          }
        }
      }
    public function changeImpact(Request $request){
        $requestParam = $request->all();
        $validator = Validator::make($requestParam, [
            "relation_id" => "required|exists:relations,id",
            "impact" => "required|numeric",
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
        $opinionModel->impact = $requestParam['impact'];
        $opinionModel->save();
        if($opinionModel->id){
            $opinionId = $opinionModel->id; 
            $updatedOpinion = $opinionModel->where('id', $opinionId)->with('opinion_calculated_column')->get()->toArray();
            $this->responseGenerator->setSuccess(count($updatedOpinion) > 0 ? $updatedOpinion[0] : null);
        }
        return $this->responseGenerator->generate(); 
    }
}
