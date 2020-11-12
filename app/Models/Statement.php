<?php

namespace App\Models;

use App\GenericModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Statement extends GenericModel
{
    use HasFactory;
    protected $validationRuleNotRequired = ['user_id', 'scope_id', 'statement_certainty_id', 'synopsis', 'comment'];
    public function logic_tree(){
        return $this->hasOne('App\Models\LogicTree');
    }
    public function statement_type(){
        return $this->belongsTo('App\Models\StatementType');
    }
    public function relation(){
        return $this->hasOne('App\Models\Relation', 'statement_id_2', 'id');
    }
    public function recursive_down_relations(){
        return $this->hasMany('App\Models\Relation', 'statement_id_1', 'id')->with('recursive_down_statement');
    }
}
