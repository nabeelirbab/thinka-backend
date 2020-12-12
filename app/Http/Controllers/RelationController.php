<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Generic\GenericController;
use Illuminate\Support\Facades\Validator;
use App;
use DB;
use App\Generic\Core\GenericRetrieve as GenericRetrieve;

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
        "user" => [
          'validation_required' => false,
          'foreign_tables' => [
            "user_basic_information" => [
              'validation_required' => false,
              "is_child" => false,
            ]
          ]
        ]
      ]
    ];
    $this->retrieveCustomQueryModel = function($queryModel, &$leftJoinedTable){
      $queryModel = $queryModel->where(function($query){
        $query->where('relations.user_id', $this->userSession('id')); // ->where('is_public', 1)
        $query->orWhere('relations.is_public', 1);
      });
      // $queryModel = $queryModel->leftJoin('user_relation_settings', function($join){
      //   $join->on('relations.id', '=', 'user_relation_settings.relation_id');
      //   $join->on(function($query) use ($param1, $param2) {
      //     $query->on('bookings.arrival', '=', $param1);
      //     $query->orOn('departure', '=',$param2);
      //   });
      // });
      return $queryModel;
    };
    $this->initGenericController();
  }
  public function retrieve(Request $request){
    // printR($request->all());
    $requestArray = $this->systemGenerateRetrieveParameter($request->all());
    $validator = Validator::make($requestArray, ["select" => "required|array|min:1"]);
    if($validator->fails()){
      $this->responseGenerator->setFail([
        "code" => 1,
        "message" => $validator->errors()->toArray()
      ]);
      return $this->responseGenerator->generate();
    }
    if(!$this->checkAuthenticationRequirement($this->basicOperationAuthRequired["retrieve"])){
      return $this->responseGenerator->generate();
    }
    // if(isset($requestArray['condition']) && count($requestArray['condition']) && $requestArray['condition'][0]['column'] === 'id'){
    //   $rootRelationId = $requestArray['condition'][0]['value'];
    //   $this->retrieveCustomQueryModel = function($queryModel, &$leftJoinedTable){
    //     $queryModel = $queryModel->where(function($query){
    //       $query->where('relations.user_id', $this->userSession('id')); // ->where('is_public', 1)
    //       $query->orWhere('relations.is_public', 1);
    //     });
    //     $queryModel = $queryModel->leftJoin('user_relation_settings', function($join){
    //       $join->on('relations.id', '=', 'user_relation_settings.relation_id')->where('user_relation_settings.user_id', $this->userSession('id'));

    //       // $join->on($this->userSession('id'), '=', 'user_relation_settings.user_id');
    //     });
    //     return $queryModel;
    //   };
    // }
    $genericRetrieve = new GenericRetrieve($this->tableStructure, $this->model, $requestArray, $this->retrieveCustomQueryModel);
    $this->responseGenerator->setSuccess($genericRetrieve->executeQuery());
    if($genericRetrieve->totalResult != null){
      $this->responseGenerator->setTotalResult($genericRetrieve->totalResult);
    }
    return $this->responseGenerator->generate();
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
        $publishedAt = $entry['is_public'] ? date('Y-m-d H:i:s') : null;
        $relationModel->is_public = $entry['is_public'];
        $relationModel->published_at = $publishedAt;
        $relationModel->save();
        foreach($entry['sub_relations'] as $subRelation){
          $relationModel = (new App\Models\Relation())->find($subRelation['id']);
          if($relationModel){
            if($relationModel->user_id === $this->userSession('id')){
              $relationModel->is_public = $entry['is_public'];
              $relationModel->published_at = $publishedAt;
              $relationModel->save();
            }
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
    return $this->responseGenerator->generate();
  }
  public function deletePartial(Request $request){
    $validator = Validator::make($request->all(), [
      'id' => 'required|exists:relations,id',
    ]);
    if($validator->fails()){
      $this->responseGenerator->setFail([
        "code" => 1,
        "message" => $validator->errors()->toArray()
      ]);
    }else{
      $entry = $request->all();
      $with = [ 
        'relations' => function($query){
          $query->with('statement');
        }
      ];
      $relationModel = ((new App\Models\Relation())->with($with)->where('id', $entry['id'])->where('user_id', $this->userSession('id'))->get());
      if(count($relationModel)){
        $relationModel = $relationModel[0];
        $subRelations = ($relationModel->toArray())['relations'];
        foreach($subRelations as $subRelation){
          $subRelationModel = (new App\Models\Relation())->find($subRelation['id']);
          if($subRelationModel){
            $subRelationModel->former_parent_relation_id = $subRelationModel->parent_relation_id;
            $subRelationModel->parent_relation_id = null;
            $logicTreeId = $this->createLogicTree($subRelation);
            $subRelationModel->logic_tree_id = $logicTreeId;
            $subRelationModel->save();
          }
        }
        $relationModel->delete();
        $this->responseGenerator->setSuccess(true);
      }else{
        $this->responseGenerator->setFail([
          "code" => 2,
          "message" => "Statement not found or you are not the author"
        ]);
      }
    }
    return $this->responseGenerator->generate();
  }
  public function deleteClip(Request $request){
    $validator = Validator::make($request->all(), [
      'id' => 'required|exists:relations,id',
    ]);
    if($validator->fails()){
      $this->responseGenerator->setFail([
        "code" => 1,
        "message" => $validator->errors()->toArray()
      ]);
    }else{
      $entry = $request->all();
      $relationModel = ((new App\Models\Relation())->with(['statement'])->where('id', $entry['id'])->where('user_id', $this->userSession('id'))->get());
      // TODO create new logic tree?
      if(count($relationModel)){
        $relationModel[0]->former_parent_relation_id = $relationModel[0]->parent_relation_id;
        $relationModel[0]->parent_relation_id = null;
        $updateResult = $relationModel[0]->save();
        $logicTreeId = $this->createLogicTree($relationModel[0]);
        $this->recursiveUpdate($entry['id'], $logicTreeId);
        $this->responseGenerator->setSuccess($updateResult);
      }else{
        $this->responseGenerator->setFail([
          "code" => 2,
          "message" => "Statement not found or you are not the author"
        ]);
      }
    }
    return $this->responseGenerator->generate();
  }
  private function recursiveUpdate($relationId, $newLogicTreeId){
    $relation = (new App\Models\Relation())->with(['relations'])->find($relationId);
    $subRelations = ($relation->toArray())['relations'];
    $relation->logic_tree_id = $newLogicTreeId;
    $relation->save();
    foreach($subRelations as $subRelation){
      $this->recursiveUpdate($subRelation['id'], $newLogicTreeId);
    }
    return true;
  }
  private function createLogicTree($relation){
    $logicTreeModel = new App\Models\LogicTree();
    $logicTreeModel->user_id = $relation['user_id'];
    $logicTreeModel->name = $relation['statement']['text'];
    $logicTreeModel->statement_id = $relation['statement']['id'];
    $logicTreeModel->is_public = $relation['is_public'];
    $logicTreeModel->save();
    return $logicTreeModel->id;
  }
  public function deleteAll(Request $request){
    $validator = Validator::make($request->all(), [
      'id' => 'required|exists:relations,id',
    ]);
    if($validator->fails()){
      $this->responseGenerator->setFail([
        "code" => 1,
        "message" => $validator->errors()->toArray()
      ]);
    }else{
      $entry = $request->all();
      $relationModel = ((new App\Models\Relation())->where('id', $entry['id'])->where('user_id', $this->userSession('id'))->get())->toArray();
      if(count($relationModel)){
        $this->recursiveDeleteAll($entry['id']);
        $this->responseGenerator->setSuccess(true);
      }else{
        $this->responseGenerator->setFail([
          "code" => 2,
          "message" => "Statement not found or you are not the author"
        ]);
      }
    }
    return $this->responseGenerator->generate();
  }
  private function recursiveDeleteAll($relationId){
    $relation = (new App\Models\Relation())->with(['relations'])->find($relationId);
    $subRelations = ($relation->toArray())['relations'];
    $userRelationBookmarks = (new App\Models\UserRelationBookmark())->where('relation_id', $relationId)->orWhere('sub_relation_id', $relationId)->delete();
    // if(count($userRelationBookmarks)){
    //   $userRelationBookmarks[0]->delete(); // delete bookmarks
    // }
    $relation->delete();
    foreach($subRelations as $subRelation){
      $this->recursiveDeleteAll($subRelation['id']);
    }
    return true;
  }
  public function trending(){
    $result = DB::select('call statements_trending');
    $this->responseGenerator->setSuccess($result);
    return $this->responseGenerator->generate();
  }
  public function myList(){
    // $result = DB::select(
    //   'call statement_my_list( ? )', 
    //   array('75')
    // );
    $result = DB::select(
      DB::raw("SET @p0='" . $this->userSession('id') . "';")
    );
    $result = DB::select(
      DB::raw("call statement_my_list(@p0)")
    );
    $this->responseGenerator->setSuccess($result);
    return $this->responseGenerator->generate();
  }
}
