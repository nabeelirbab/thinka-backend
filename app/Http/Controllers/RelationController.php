<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Generic\GenericController;
use App;
use DB;
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
        'user_relation_bookmarks' => [],
        'relations' => $this->generateRecursiveRelationForeignTable(1), // sub relations
        'statement' => [
          "is_child" => false,
          "validation_required" => false,
          'foreign_tables' => [
            'statement_type' => []
          ]
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
  public function trending(){
    $result = DB::select('call statements_trending');
    $this->responseGenerator->setSuccess($result);
    return $this->responseGenerator->generate();
  }
}
