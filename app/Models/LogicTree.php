<?php

namespace App\Models;

use App\Generic\GenericModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogicTree extends GenericModel
{
    use HasFactory;
    protected $validationRuleNotRequired = ['user_id'];
}
