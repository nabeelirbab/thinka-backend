<?php

namespace App;
namespace App\Models;

use App\Generic\GenericModel;
use Illuminate\Database\Eloquent\Model;

class Service extends GenericModel
{
  protected $fillable = ['description', 'link', 'auth_required'];
  public $validationRules = [
    'description' => 'required|unique:services,description,except,id'
  ];
  public function service_actions(){
    return $this->hasMany('App\Models\ServiceAction');
  }
}
