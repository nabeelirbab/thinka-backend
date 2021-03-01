<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Generic\GenericModel;

class Opinion extends GenericModel
{
    use HasFactory;
    protected $validationRules = [
        "relation_id" => "required|exists:relations,id",
        "type" => "in:0,1,2,3"
    ];
    protected $validationRuleNotRequired = ['user_id', 'type', 'confidence', 'residual_risk', 'risk_plan_cost', 'impact_amount', 'impact'];
    public function opinion_calculated_column(){
        return $this->hasOne('App\Models\OpinionCalculatedColumn', 'id');
    }
}
