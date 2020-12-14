<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Generic\GenericModel;

class Relation extends GenericModel
{
    use HasFactory;
    protected $fillable = ['logic_tree_id'];
    protected $validationRuleNotRequired = ['user_id', 'impact', 'impact_amount', 'risk_plan_cost', 'residual_risk', 'relevance_row', 'published_at'];
    public function logic_tree(){
        return $this->belongsTo('App\Models\LogicTree');
    }
    public function relations(){
        return $this->hasMany('App\Models\Relation', 'parent_relation_id')->where(function($query){
            $query->where('user_id', $this->userSession('id'));
            $query->orWhereNotNull('published_at');
        });
    }
    public function statement(){
        return $this->belongsTo('App\Models\Statement');
    }
    public function user_relation_bookmarks(){
        return $this->hasMany('App\Models\UserRelationBookmark')->where('user_id', $this->userSession('id'));
    }
    public function user_relation_context_locks(){
        return $this->hasMany('App\Models\UserRelationContextLock')->where('user_id', $this->userSession('id'));
    }
    public function parent_relation(){ // statement_2
        return $this->belongsTo('App\Models\Relation', 'parent_relation_id');
    }
    public function user(){ // statement_2
        return $this->belongsTo('App\Models\User');
    }
}
