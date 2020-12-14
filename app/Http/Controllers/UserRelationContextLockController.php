<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Generic\GenericController;
use App;
class UserRelationContextLockController extends GenericController
{
    function __construct(){
        $this->model = new App\Models\UserRelationContextLock();
        $this->tableStructure = [
          'columns' => [
          ],
          'foreign_tables' => [
          ]
        ];
        $this->initGenericController();
      }
}
