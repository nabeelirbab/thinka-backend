<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Generic\GenericModel;

class NotificationSubRelationUpdate extends GenericModel
{
    use HasFactory;
    public function user(){
        return $this->belongsTo('App\Models\User');
    }
    public function relation(){
        return $this->belongsTo('App\Models\Relation');
    }
    public function sub_relation(){
        return $this->belongsTo('App\Models\Relation', 'sub_relation_id');
    }
    public function notification_sub_relation_update_user_relations(){
        return $this->hasMany('App\Models\NotificationSubRelationUpdateUserRelation');
    }
}
