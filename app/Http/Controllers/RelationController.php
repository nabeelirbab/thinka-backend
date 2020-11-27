<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Generic\GenericController;
use Illuminate\Support\Facades\Validator;
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
    $this->retrieveCustomQueryModel = function($queryModel, &$leftJoinedTable){
      $queryModel = $queryModel->where(function($query){
        $query->where('user_id', $this->userSession('id')); // ->where('is_public', 1)
        $query->orWhere('is_public', 1);
      });
      
      return $queryModel;
    };
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
  public function publish(Request $request){
    $validator = Validator::make($request->all(), [
      'id' => 'required|exists:relations,id',
      'is_public' => 'required',
      'sub_relations' => 'array',
      'sub_relations.*' => 'required|exists:relations,id'
    ]);
    if($validator->fails()){
      $this->responseGenerator->setFail([
        "code" => 1,
        "message" => $validator->errors()->toArray()
      ]);
    }else{
      $entry = $request->all();
      $relationModel = (new App\Models\Relation())->find($entry['id']);
      if($relationModel->user_id === $this->userSession('id')){
        $publishedAt = date('Y-m-d H:i:s');
        $relationModel->is_public = $entry['is_public'];
        $relationModel->published_at = $publishedAt;
        $relationModel->save();
        foreach($entry['sub_relations'] as $subRelation){
          $relationModel = (new App\Models\Relation())->find($subRelation['id']);
          if($relationModel->user_id === $this->userSession('id')){
            $relationModel->is_public = $entry['is_public'];
            $relationModel->published_at = $publishedAt;
            $relationModel->save();
          }
        }
        $this->responseGenerator->setSuccess(true);
      }else{
        $this->responseGenerator->setFail([
          "code" => 2,
          "message" => 'Not owner'
        ]);
      }
    }
    return $this->responseGenerator->generate();;
  }
  public function trending(){
    $result = DB::select('call statements_trending');
    $this->responseGenerator->setSuccess($result);
    return $this->responseGenerator->generate();
  }
}
