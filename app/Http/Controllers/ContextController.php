<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Generic\GenericController;
use App;

class ContextController extends GenericController
{
    function __construct(){
        $this->model = new App\Models\Context();
        $this->tableStructure = [
            'columns' => [
            ],
            'foreign_tables' => [
            ]
        ];
        $this->initGenericController();
    }
}
