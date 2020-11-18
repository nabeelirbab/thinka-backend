<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App;
use App\Generic\GenericController;

class ScopeController extends GenericController
{
    function __construct(){
        $this->model = new App\Models\Scope();
        $this->tableStructure = [
          'columns' => [
          ],
          'foreign_tables' => [
          ]
        ];
        $this->initGenericController();
    }
}
