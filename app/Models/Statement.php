<?php

namespace App\Models;

use App\Generic\GenericModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Statement extends GenericModel
{
    use HasFactory;
    protected $validationRuleNotRequired = ['user_id', 'scope_id', 'statement_certainty_id', 'scope', 'statement_certainty', 'synopsis', 'context', 'published_at'];
    public function logic_tree(){
        return $this->hasOne('App\Models\LogicTree');
    }
    public function statement_type(){
        return $this->belongsTo('App\Models\StatementType');
    }
    public function relation(){
        return $this->hasOne('App\Models\Relation');
    }
}
