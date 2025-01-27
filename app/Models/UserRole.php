<?php

namespace App;

namespace App\Models;

use App\Generic\GenericModel;
use Illuminate\Database\Eloquent\Model;

class UserRole extends GenericModel
{
  protected $fillable = [
    'user_id',
    'role_id',
    'company_id',
  ];
  public function role()
  {
    return $this->belongsTo('App\Role');
  }
}
