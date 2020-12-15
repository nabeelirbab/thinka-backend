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
    public function createStatementUpdateNotification($relationId, $userId, $message){
        $this->type = 2;
        $this->save();
        $notificationId = $this->id;
        $createdAt = $this->created_at;
        $notifcationRelationUpdateModel = new NotificationRelationUpdate();
        $notifcationRelationUpdateModel->notification_id = $notificationId;
        $notifcationRelationUpdateModel->relation_id = $relationId;
        $notifcationRelationUpdateModel->user_id = $userId;
        $notifcationRelationUpdateModel->message = $message;
        $notifcationRelationUpdateModel->save();
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
}
