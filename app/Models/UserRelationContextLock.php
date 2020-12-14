<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Generic\GenericModel;
class UserRelationContextLock extends GenericModel
{
    protected $validationRuleNotRequired = ['user_id'];
    use HasFactory;
}
