<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App;
use App\Generic\GenericController;
use App\Generic\Core\GenericFormValidation;
use App\Generic\Core\GenericCreate;
use Illuminate\Support\Facades\Validator;

class StatementController extends GenericController
{
  function __construct(){
    $this->model = new App\Models\Statement();
    $this->tableStructure = [
      'columns' => [
      ],
      'foreign_tables' => [
        'logic_tree' => [
          "is_child" => true, 
          'validation_required' => false
        ],
        'relation' => [
          "is_child" => true,
          'validation_required' => false,
          'foreign_tables' => [
            'statement_1' => [
              'true_table' => 'Statement',
              'validation_required' => false,
            ]
          ]
        ],
        'recursive_down_relations' => [
          "true_table" => 'Relation',
          "is_child" => true,
          'validation_required' => false
        ],
      ]
    ];
    $this->initGenericController();
  }
  public function create(Request $request){
    $entry = $request->all();
    $resultObject = [
      "success" => false,
      "fail" => false
    ];
    $validation = new GenericFormValidation($this->tableStructure, 'create');
    if($validation->isValid($entry)){
      $relation = isset($entry['relation']) ? $entry['relation'] : null;
      unset($entry['relation']);
      $entry['user_id'] = $this->userSession('id');
      if(!isset($entry['id']) || !$entry['id']){
        $genericCreate = new GenericCreate($this->tableStructure, $this->model);
        $resultObject['success'] = $genericCreate->create($entry);
      }else{ // create from existing statement
        $logicTreeId = $this->createLogicTree($entry);
        $resultObject['success'] = [
          "id" => $entry['id'],
          "logic_tree" => [
            "id" => $logicTreeId
          ]
        ];
      }
      if($resultObject['success']){
        if($relation){
          $relation['statement_id'] = $resultObject['success']['id'];
          $relation['is_public'] = $entry['is_public'];
          if(!isset($relation['logic_tree_id'])){
            $relation['logic_tree_id'] = $resultObject['success']['logic_tree']['id'];
          }
          $relation['user_id'] = $this->userSession('id');
          $relationModel = new App\Models\Relation();
          foreach($relation as $key => $value){
            $relationModel->$key = $value;
          }
          $relationModel->save();
          $resultObject['success']['relation']['id'] = $relationModel->id;
        }
      }
    }else{
      $resultObject['fail'] = [
        "code" => 1,
        "message" => $validation->validationErrors
      ];

    }
    $this->responseGenerator->setSuccess($resultObject['success']);
    $this->responseGenerator->setFail($resultObject['fail']);
    return $this->responseGenerator->generate();
  }
  public function updateRelation(Request $request){
    $entry = $request->all();
    $validator = Validator::make($entry, [
      'id' => 'exists:statements,id',
      'relation' => 'required',
      'relation.id' => 'exists:relations,id',
    ]);
    if($validator->fails()){
      $this->responseGenerator->setFail([
        "code" => 1,
        "message" => $validator->errors()->toArray()
      ]);
    }else{
      $updatedRelation = $entry['relation'];
      $relation = (new App\Models\Relation())->where('id', $updatedRelation['id'])->where('user_id', $this->userSession('id'))->get();
      if(!count($relation)){
        $this->responseGenerator->setFail([
          "code" => 2,
          "message" => "Statement not found or you do not own it"
        ]);
      }else if(count($relation) && $relation[0]->is_public){
        $this->responseGenerator->setFail([
          "code" => 3,
          "message" => 'You cannot modify published statements'
        ]);
      }else{
        $relation = $relation[0];
        unset($entry['relation']);
        if(isset($entry['id']) && !isset($entry['old_statement_id'])){ // if old_statement_id exists, then the user choose a statement from suggestion
          $statementModel = (new App\Models\Statement())->find($entry['id']);
          $statementModel->text = $entry['text'];
          $statementModel->statement_type_id = $entry['statement_type_id'];
          $statementModel->save();
          $relation->statement_id = $statementModel->id;
        }else{
          $relation->statement_id = $entry['id'];
        }
        unset($updatedRelation['relevance_window']);
        foreach($updatedRelation as $column => $value){
          $relation->$column = $value;
        }
        $relation->save();
        $this->responseGenerator->setSuccess([
          'id' => $relation->statement_id,
          'relation' => [
            'id' => $relation->id
          ]
        ]);
      }
    }
    
    return $this->responseGenerator->generate();
  }
  private function createLogicTree($statement){
    $logicTreeModel = new App\Models\LogicTree();
    $logicTreeModel->user_id = $this->userSession('id');
    $logicTreeModel->name = $statement['text'];
    $logicTreeModel->statement_id = $statement['id'];
    $logicTreeModel->is_public = $statement['is_public'];
    $logicTreeModel->save();
    return $logicTreeModel->id;
  }
  public function retrieveTree(Request $request){

  }
}
