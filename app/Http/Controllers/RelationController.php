<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Generic\GenericController;
use App;
class RelationController extends GenericController
{
  function __construct(){
    $this->model = new App\Models\Relation();
    $this->tableStructure = [
      'columns' => [
      ],
      'foreign_tables' => [
        'parent_relation' => [
          "true_table" => 'relations',
          "is_child" => false, 
          "validation_required" => false,
          'foreign_tables' => [
            'statement' => [
              "is_child" => true,
            ]
          ]
        ],
        'relations' => $this->generateRecursiveRelationForeignTable(1),
        'statement' => [
          "is_child" => false,
          "validation_required" => false
        ],
        'logic_tree' => [
          "is_child" => true, 
          'validation_required' => false
        ],
      ]
    ];
    $this->initGenericController();
  }
  private function generateRecursiveRelationForeignTable($currentDeep, $deep = 10){
    $relations = [
      "foreign_column" => 'parent_relation_id',
      "validation_required" => false,
      'foreign_tables' => [
        'statement' => [
          "is_child" => true
        ]
      ]
    ];
    if($currentDeep <= $deep){
      $relations['foreign_tables']['relations'] = $this->generateRecursiveRelationForeignTable(++$currentDeep);
    }
    return $relations;
  }
}
