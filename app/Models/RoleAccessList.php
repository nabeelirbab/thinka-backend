<?php
namespace App\Models;

use App\Generic\GenericModel;
use Illuminate\Database\Eloquent\Model;

class RoleAccessList extends GenericModel
{
  public function service_action(){
    return $this->belongsTo('App\Models\ServiceAction', 'service_action_registry_id', 'id');
  }
}
