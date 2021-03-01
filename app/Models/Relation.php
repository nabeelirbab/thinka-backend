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
        return $this->hasMany('App\Models\Relation', 'parent_relation_id')
            ->where(function($query){
                $query->where('user_id', $this->userSession('id'));
                $query->orWhereNotNull('published_at');
            });
    }
    public function all_relations(){
        return $this->hasMany('App\Models\Relation', 'parent_relation_id');
    }
    public function statement(){
        return $this->belongsTo('App\Models\Statement');
    }
    public function user_relation_bookmarks(){
        return $this->hasMany('App\Models\UserRelationBookmark')->where('user_id', $this->userSession('id'));
    }
    public function all_user_relation_bookmarks(){
        return $this->hasMany('App\Models\UserRelationBookmark');
    }
    public function user_relation_context_locks(){
        return $this->hasMany('App\Models\UserRelationContextLock')->where('user_id', $this->userSession('id'));
    }
    public function parent_relation(){ // statement_2
        return $this->belongsTo('App\Models\Relation', 'parent_relation_id');
    }
    public function user(){
        return $this->belongsTo('App\Models\User');
    }
    public function user_opinion(){
        return $this->hasOne('App\Models\Opinion', 'relation_id')->where('user_id', $this->userSession('id'));
    }
    public function user_opinions(){
        return $this->hasMany('App\Models\Opinion', 'relation_id'); // ->where('user_id', $this->userSession('id'));
    }
    public function virtual_relation(){
        return $this->belongsTo('App\Models\Relation', 'parent_relation_id');
    }
}
