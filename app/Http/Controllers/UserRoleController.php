<?php

namespace App\Http\Controllers;
use App;

use Illuminate\Http\Request;
use App\Generic\GenericController;

class UserRoleController extends GenericController
{
  function __construct(){
    $this->model = new App\UserRole();
    $this->tableStructure = [
      'columns' => [
      ],
      'foreign_tables' => [
      ]
    ];
    $this->initGenericController();
  }
}
