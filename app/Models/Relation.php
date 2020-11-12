<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\GenericModel;

class Relation extends GenericModel
{
    use HasFactory;
    protected $fillable = ['logic_tree_id'];
    protected $validationRuleNotRequired = ['user_id', 'impact', 'impact_amount', 'risk_plan_cost', 'residual_risk', 'relevance_row', 'is_public', 'statement_id_1'];
    public function recursive_down_statement(){
        return $this->belongsTo('App\Models\Statement', 'statement_id_2', 'id')->with('recursive_down_relations');
    }
    public function statement_1(){
        return $this->belongsTo('App\Models\Statement', 'statement_id_1');
    }
}
