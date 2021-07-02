<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Generic\GenericModel;

class Notification extends GenericModel
{
    use HasFactory;
    private $authorOfRelation = 1;
    private $authorOfParentRelation = 2;
    private $userWithBookmarkOfRelation = 3;
    private $userWithBookmarkOfParentRelation = 4;
    public function createNotification(){
    }
    public function createSubRelationUpdateNotification($type, $subRelationId, $userId, $subscriberUserIds, $message){// type: 1 - general, 2 - publish co author, 3 - publish bookmarker
        $this->type = 4;
        $this->save();
        $notificationId = $this->id;
        $createdAt = $this->created_at;
        $notificationSubRelationUpdateModel = new NotificationSubRelationUpdate();
        $notificationSubRelationUpdateModel->notification_id = $notificationId;
        $notificationSubRelationUpdateModel->type = $type;
        $notificationSubRelationUpdateModel->user_id = $userId;
        $notificationSubRelationUpdateModel->sub_relation_id = $subRelationId;
        $notificationSubRelationUpdateModel->message = $message;
        $notificationSubRelationUpdateModel->save();
        $notificationRelationUpdateModelId = $notificationSubRelationUpdateModel->id;
        $notificationUsers = [];    
        foreach($subscriberUserIds as $subscriberUserId){
            $notificationUsers[] = [
                'notification_id' => $notificationId,
                'user_id' => $subscriberUserId,
                'status' => 0,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];
        }
        (new NotificationUser())->insert($notificationUsers);
        return $notificationUsers;
    }
    public function createRelationUpdateNotification($relationId, $userId, $message, $type = null){ // user id of the user who made the update
        $this->type = 2;
        $this->save();
        $notificationId = $this->id;
        $createdAt = $this->created_at;
        $notificationRelationUpdateModel = new NotificationRelationUpdate();
        $notificationRelationUpdateModel->notification_id = $notificationId;
        $notificationRelationUpdateModel->relation_id = $relationId;
        $notificationRelationUpdateModel->user_id = $this->userSession('id'); // the user who made the changes
        $notificationRelationUpdateModel->message = $message;
        $notificationRelationUpdateModel->type = $type; // null is default, 2 - opinion
        $notificationRelationUpdateModel->save();
        $notificationUsers = [];
        if(gettype($userId) !== 'array'){
            $relation = (new Relation())->find($relationId);
            if($userId != $relation['user_id']){
                $notificationUsers[] = [
                    'notification_id' => $notificationId,
                    'user_id' => $relation['user_id'],
                    'status' => 0,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ];
            }
            // retrieve bookmarks
            $bookmarks = (new UserRelationBookmark())->where('relation_id', $relationId)->orWhere('sub_relation_id', $relationId)->whereNotIn('user_id', [$userId])->get()->toArray();
            for($x = 0; $x < count($bookmarks); $x++){
                if($bookmarks[$x]['user_id'] != $userId){
                    $notificationUsers[] = [
                        'notification_id' => $notificationId,
                        'user_id' => $bookmarks[$x]['user_id'],
                        'status' => 0,
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ];
                }
            }
        }else{
            $usersToNotify = $userId; // $userId can be an array
            foreach($usersToNotify as $userIdKey => $reason){
                $notificationUsers[] = [
                    'notification_id' => $notificationId,
                    'user_id' => $userIdKey,
                    'extra_data' => json_encode($reason),
                    'status' => 0,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ];
            }
        }
        $notificationUserModel = new NotificationUser();
        $notificationUserModel->insert($notificationUsers);
        return $notificationUsers;
    }
    public function createStatementUpdateNotification($statementId, $relationId, $userId, $message){
        $this->type = 3;
        $this->save();
        $notificationId = $this->id;
        $createdAt = $this->created_at;
        
        $notificationStatementUpdateModel = new NotificationStatementUpdate();
        $notificationStatementUpdateModel->notification_id = $notificationId;
        $notificationStatementUpdateModel->statement_id = $statementId;
        $notificationStatementUpdateModel->relation_id = $relationId;
        $notificationStatementUpdateModel->user_id = $userId;
        $notificationStatementUpdateModel->message = $message;
        $notificationStatementUpdateModel->save();
        
        $statement = (new Statement())->find($statementId)->with([
            'relations' => function($query){
                $query->select(['id', 'statement_id']);
                $query->with(['statement', 'virtual_relation', 'virtual_relation.statement']);
            }
        ])->get()->toArray();
        $relations = [];
        $subscribers = []; // key: user_id, value: relations
        $subscriberUserIdList = [];
        foreach($statement['relations'] as $relation){
            $usersToNotify = $this->getRelation($relation['id']);
            foreach($usersToNotify as $userId => $reason){
                if(!isset($subscribers[$userId])){
                    $subscriberUserIdList[] = $userId;
                    $subscribers[$userId] = [
                        'relations' => []
                    ];
                }
                $subscribers[$userId]['relations'][] = $relation;
            }
        }
        // retrieve bookmarks
        // $bookmarks = (new UserRelationBookmark())
        //     ->where('relation_id', $relationId)
        //     ->orWhere('sub_relation_id', $relationId)
        //     ->whereNotIn('user_id', [$userId])
        //     ->get()->toArray();
        $notificationUsers = [];
        foreach($subscribers as $userId => $details){
            $notificationUsers[] = [
                'notification_id' => $notificationId,
                'user_id' => $userId,
                'status' => 0,
                'extra_data' => json_encode($details),
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];
        }
        $notificationUserModel = new NotificationUser();
        $notificationUserModel->insert($notificationUsers);
        return $notificationUsers;
    }
    public function getRelation($relationId, $level = 0, $usersToNotify = []){
        $relation = (new App\Models\Relation())
        ->where('id', $relationId)
        ->with('all_user_relation_bookmarks')
        ->get()->toArray();
        if(count($relation)){
          $relation = $relation[0];
          if(!isset($usersToNotify[$relation['user_id']])){
            $usersToNotify[$relation['user_id']] = 1000;
          }
          if($usersToNotify[$relation['user_id']] > $this->authorOfParentRelation){
            $usersToNotify[$relation['user_id']] = $this->authorOfParentRelation;
          }
    
          $userRelationBookmarks = $relation['all_user_relation_bookmarks'];
          foreach($userRelationBookmarks as $userRelationBookmark){
            $userId = $userRelationBookmark['user_id'];
            if(!isset($usersToNotify[$userId])){
              $usersToNotify[$userId] = 1000;
            }
            if($usersToNotify[$userId] > $this->userWithBookmarkOfRelation && $level === 0){
              $usersToNotify[$userId] = $this->userWithBookmarkOfRelation;
            }
            if($usersToNotify[$userId] > $this->userWithBookmarkOfParentRelation){
              $usersToNotify[$userId] = $this->userWithBookmarkOfParentRelation;
            }
          }
          // $usersToNotify[$relation['user_id']] = $authorOfParentRelation;
          if($relation['parent_relation_id']){
            $this->getRelation($relation['parent_relation_id'], $level + 1, $usersToNotify);
          }
          if($level === 0){
            $usersToNotify[$relation['user_id']] = $this->authorOfRelation;
          }
        }
        return $usersToNotify;
    }
    public function notification_relation_update(){
        return $this->hasOne('App\Models\NotificationRelationUpdate');
    }
    public function notification_sub_relation_update(){
        return $this->hasOne('App\Models\NotificationSubRelationUpdate');
    }
    public function notification_statement_update(){
        return $this->hasOne('App\Models\NotificationStatementUpdate');
    }
}
