<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App;
use App\Generic\GenericController;
use App\Generic\Core\GenericFormValidation;
use App\Generic\Core\GenericCreate;

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
      $entry['user_id'] = $this->userSession('id');
      $genericCreate = new GenericCreate($this->tableStructure, $this->model);
      $resultObject['success'] = $genericCreate->create($entry);
      if($resultObject['success']){
        if($relation){
          $relation['statement_id_2'] = $resultObject['success']['id'];
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
  public function retrieveTree(Request $request){

  }
}
