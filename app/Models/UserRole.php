<?php

namespace App;
namespace App\Models;

use App\Generic\GenericModel;
use Illuminate\Database\Eloquent\Model;

class UserRole extends GenericModel
{
    public function role(){
      return $this->belongsTo('App\Role');
    }
}
