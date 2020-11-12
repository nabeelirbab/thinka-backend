<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App;
class RoleAccessListController extends GenericController
{
  function __construct(){
    $this->model = new App\Models\RoleAccessList();
    $this->tableStructure = [
      'columns' => [
      ],
      'foreign_tables' => [
      ]
    ];
    $this->initGenericController();
  }
}
