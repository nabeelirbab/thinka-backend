<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Generic\GenericModel;

class Notification extends GenericModel
{
    use HasFactory;
    public function createNotification(){
    }
    public function createSubRelationUpdateNotification($subRelationId, $userId, $userRelations, $message){
        $this->type = 4;
        $this->save();
        $notificationId = $this->id;
        $createdAt = $this->created_at;
        $notifcationSubRelationUpdateModel = new NotificationSubRelationUpdate();
        $notifcationSubRelationUpdateModel->notification_id = $notificationId;
        $notifcationSubRelationUpdateModel->user_id = $userId;
        $notifcationSubRelationUpdateModel->sub_relation_id = $subRelationId;
        $notifcationSubRelationUpdateModel->message = $message;
        $notifcationSubRelationUpdateModel->save();
        $notificationRelationUpdateModelId = $notifcationSubRelationUpdateModel->id;
        $notificationUsers = [];
        foreach($userRelations as $key => $userRelation){
            $userRelations[$key]['notification_sub_relation_update_id'] = $notificationRelationUpdateModelId;
            $notificationUsers[] = [
                'notification_id' => $notificationId,
                'user_id' => $userRelation['user_id'],
                'status' => 0,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];
        }
        $notificationSubRelationUpdateUserRelationModel = new NotificationSubRelationUpdateUserRelation();
        $notificationSubRelationUpdateUserRelationModel->insert($userRelations);
        (new NotificationUser())->insert($notificationUsers);
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
            $usersToNotify = $userId;
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
        return $notificationId;
    }
    public function createStatementUpdateNotification($statementId, $relationId, $userId, $message){
        $this->type = 3;
        $this->save();
        $notificationId = $this->id;
        $createdAt = $this->created_at;
        $statementModel = (new Statement())->find($statementId)->with(['relations.all_user_relation_bookmarks']);
        $notifcationStatementUpdateModel = new NotificationStatementUpdate();
        $notifcationStatementUpdateModel->notification_id = $notificationId;
        $notifcationStatementUpdateModel->statement_id = $statementId;
        $notifcationStatementUpdateModel->relation_id = $relationId;
        $notifcationStatementUpdateModel->user_id = $userId;
        $notifcationStatementUpdateModel->message = $message;
        $notifcationStatementUpdateModel->save();
        // retrieve bookmarks
        $bookmarks = (new UserRelationBookmark())->where('relation_id', $relationId)->orWhere('sub_relation_id', $relationId)->whereNotIn('user_id', [$userId])->get()->toArray();
        $notificationUsers = [];
        for($x = 0; $x < count($bookmarks); $x++){
            $notificationUsers[] = [
                'notification_id' => $notificationId,
                'user_id' => $bookmarks[$x]['user_id'],
                'status' => 0,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];
        }
        $notificationUserModel = new NotificationUser();
        $notificationUserModel->insert($notificationUsers);
        return $notificationId;
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
