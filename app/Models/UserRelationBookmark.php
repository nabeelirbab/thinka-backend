<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Generic\GenericModel;

class UserRelationBookmark extends GenericModel
{
    protected $validationRuleNotRequired = ['user_id', 'sub_relation_id'];
    use HasFactory;
    public function relation(){
        return $this->belongsTo('App\Models\Relation');
    }
    public function user(){
        return $this->belongsTo('App\Models\User');
    }
}
