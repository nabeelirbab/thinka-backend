<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App;

class RelationTypeController extends GenericController
{
    function __construct(){
        $this->model = new App\Models\RelationType();
        $this->tableStructure = [
          'columns' => [
          ],
          'foreign_tables' => [
          ]
        ];
        $this->initGenericController();
    }
}
