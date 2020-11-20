<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Generic\GenericModel;

class Relation extends GenericModel
{
    use HasFactory;
    protected $fillable = ['logic_tree_id'];
    protected $validationRuleNotRequired = ['user_id', 'impact', 'impact_amount', 'risk_plan_cost', 'residual_risk', 'relevance_row', 'is_public'];
    public function logic_tree(){
        return $this->belongsTo('App\Models\LogicTree');
    }
    public function relations(){
        return $this->hasMany('App\Models\Relation', 'parent_relation_id');
    }
    public function statement(){
        return $this->belongsTo('App\Models\Statement');
    }
    public function parent_relation(){ // statement_2
        return $this->belongsTo('App\Models\Relation', 'parent_relation_id');
    }
}
