<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Generic\GenericModel;

class UserReport extends GenericModel
{
    use HasFactory;
    protected $validationRuleNotRequired = ['user_id', 'resolved_by_user_id', 'status'];
}
