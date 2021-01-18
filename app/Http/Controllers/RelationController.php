<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Generic\GenericController;
use Illuminate\Support\Facades\Validator;
use App;
use DB;
use App\Generic\Core\GenericFormValidation;
use App\Generic\Core\GenericUpdate;
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
              "is_child" => false,
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
          "is_child" => false, 
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
        $query->where('relations.user_id', $this->userSession('id'));
        $query->orWhereNotNull('relations.published_at');
      });
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
        ],
        "user" => [
          'validation_required' => false,
          'foreign_tables' => [
            "user_basic_information" => [
              'validation_required' => false,
              "is_child" => false,
            ]
          ]
        ],
        "user_relation_context_locks" => [
          'validation_required' => false,
        ]
      ]
    ];
    if($currentDeep <= $deep){
      $relations['foreign_tables']['relations'] = $this->generateRecursiveRelationForeignTable(++$currentDeep);
    }
    return $relations;
  }
  public function update(Request $request){
    if(!$this->checkAuthenticationRequirement($this->basicOperationAuthRequired["update"])){
      return $this->responseGenerator->generate();
    }
    $entry = $request->all();
    $resultObject = [
      "success" => false,
      "fail" => false
    ];
    $validation = new App\Generic\Core\GenericFormValidation($this->tableStructure, "update");
    if($validation->isValid($entry)){
      $genericUpdate = new App\Generic\Core\GenericUpdate($this->tableStructure, $this->model);
      $resultObject['success'] = $genericUpdate->update($entry);
      if(isset($entry['impact_amount'])){
        $notification = new App\Models\Notification();
        $notification->createRelationUpdateNotification($entry['id'], $this->userSession('id'), "Updated impact amount to ". $entry['impact_amount'] . "%");
      }
      $this->responseGenerator->addDebug("relation id", $entry['id']);
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
  public function publish(Request $request){
    $validator = Validator::make($request->all(), [
      'id' => 'required|exists:relations,id',
      'published_at' => 'required',
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
        $publishedAt = $entry['published_at'] ? date('Y-m-d H:i:s') : null;
        $relationToNotifyList = array(); // contains object where key is the published/unpublished statements and the value is an array of parent ids
        $this->recursivePublish($entry['id'], $publishedAt, $relationToNotifyList, [], []);
        $this->responseGenerator->addDebug('$relationToNotifyList', $relationToNotifyList);
        if($relationModel->parent_relation_id === null){
          $logicTreeModel = (new App\Models\LogicTree())->find($relationModel->logic_tree_id);
          $logicTreeModel->published_at = $publishedAt;
          $logicTreeModel->save();
        }
        $parentIds = [];
        if($relationModel->parent_relation_id){
          $parentIds = $this->getParentIds($relationModel->id);
        }
        $this->responseGenerator->addDebug('$parentIds'. $relationModel->parent_relation_id, $parentIds);
        $notificationMessage = $publishedAt ? 'A sub statement has been added or published in your bookmarked statement' : 'A statement has been unpublished in your bookmarked statement';
        foreach($relationToNotifyList as $relationToNotifyId => $relationToNotifyList){
          $toNotifyRelationArray = array_merge($parentIds, $relationToNotifyList['parents']);
          $this->notifyPublishedStatementParents($relationToNotifyId, $notificationMessage, $toNotifyRelationArray, $relationToNotifyList['users']); // notify parents authors and bookmarkers
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
  private function getParentIds($relationId){
    $parentIds = [];
    $hasParent = true;
    $currentRelationId = $relationId;
    do{
      $relationModel = (new App\Models\Relation())->find($currentRelationId);
      if($relationModel->id && $relationModel->parent_relation_id){
        $parentIds[] = $relationModel->parent_relation_id;
        $currentRelationId = $relationModel->parent_relation_id;
      }else{
        $currentRelationId = null;
      }
    }while($currentRelationId);
    return $parentIds;
  }
  private function notifyPublishedStatementParents($publishedRelationId, $message, $parentIds, $userIds){ // publish and unpublish
    $userRelationBookmarks = (new App\Models\UserRelationBookmark())
      ->select(['user_id', 'relation_id'])
      ->whereIn('relation_id', $parentIds)
      ->get()->toArray();
    $this->responseGenerator->addDebug('$userRelationBookmarks'.$publishedRelationId, $userRelationBookmarks);
    $usersToNotify = array_merge($userRelationBookmarks, $userIds);
    (new App\Models\Notification())->createSubRelationUpdateNotification($publishedRelationId, $this->userSession('id'), $userRelationBookmarks, $message);
  }
  public function join(Request $request){
    $validator = Validator::make($request->all(), [
      'parent_relation_id' => 'required|exists:relations,id',
      'relation_id' => 'required|exists:relations,id',
    ]);
    if($validator->fails()){
      $this->responseGenerator->setFail([
        "code" => 1,
        "message" => $validator->errors()->toArray()
      ]);
    }else{
      $entry = $request->all();
      $relationModel = (new App\Models\Relation())->find($entry['relation_id']);
      $parentRelationModel = (new App\Models\Relation())->find($entry['parent_relation_id']);
      if($relationModel->logic_tree_id === $parentRelationModel->logic_tree_id){
        $this->responseGenerator->setFail([
          "code" => 3,
          "message" => 'Circular relationss not allowed'
        ]);
      }else if($relationModel->user_id === $this->userSession('id')){
        $relationModel->parent_relation_id = $entry['parent_relation_id'];
        $relationModel->relevance_window = $entry['relevance_window'];
        $relationModel->save();
        $subRelations = $this->recursiveUpdate($entry['relation_id'], $parentRelationModel->logic_tree_id);
        $logicTreeId = (new App\Models\LogicTree())->find($relationModel->logic_tree_id);
        $this->responseGenerator->setSuccess([
          'id' => $entry['relation_id'],
          'parent_relation_id' => $entry['relation_id'],
        ]);
      }else{
        $this->responseGenerator->setFail([
          "code" => 2,
          "message" => 'Not owner'
        ]);
      }
    }
    return $this->responseGenerator->generate();
  }
  private function recursivePublish($relationId, $publishedAt, &$relationToNotifyList, $parentIds, $parentUserIds){
    $relation = (new App\Models\Relation())->with(['relations' => function($query){
      $query->where('user_id', $this->userSession('id'));
    }])->find($relationId);
    $subRelations = ($relation->toArray())['relations'];
    $message = null;
    if($publishedAt && !$relation->published_at){ // publish and relation not yet published
      $relation->published_at = $publishedAt;
      $message = 'Statement that you bookmarked has been published';
    }else if(!$publishedAt && $relation->published_at){ // unpublish and relation is already published
      $relation->published_at = null;
      $message = 'Statement that you bookmarked has been unpublished';
    }
    if($message){
      $relation->save();
      (new App\Models\Notification())->createRelationUpdateNotification($relationId, $this->userSession('id'), $message);
      $relationToNotifyList[$relationId] = [
        'parents' => $parentIds,
        'users' => $parentUserIds
      ];
    }
    if(count($subRelations)){
      $parentIds[] = $relationId;
      if($this->userSession('user_id') * 1 !== $relation->user_id * 1){
        $parentUserIds[] = $relation->user_id;
      }
      foreach($subRelations as $subRelation){
        $this->recursivePublish($subRelation['id'], $publishedAt, $relationToNotifyList, $parentIds, $parentUserIds);
      }
    }
    return true;
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
    $relation = (new App\Models\Relation())->with(['all_relations'])->find($relationId);
    $subRelations = ($relation->toArray())['all_relations'];
    $relation->logic_tree_id = $newLogicTreeId;
    $relation->save();
    foreach($subRelations as $key => $subRelation){
      $subRelations[$key] = $this->recursiveUpdate($subRelation['id'], $newLogicTreeId);
    }
    return $subRelations;
  }
  private function createLogicTree($relation){
    $logicTreeModel = new App\Models\LogicTree();
    $logicTreeModel->user_id = $relation['user_id'];
    $logicTreeModel->name = $relation['statement']['text'];
    $logicTreeModel->statement_id = $relation['statement']['id'];
    $logicTreeModel->published_at = $relation['published_at'];
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
      (new App\Models\Notification())->createRelationUpdateNotification($entry['id'], $this->userSession('id'), 'Statement has been deleted together with its supporting and counter statements');
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
    (new App\Models\Notification())->createRelationUpdateNotification($relationId, $this->userSession('id'), 'Statement has been deleted');
    $userRelationBookmarks = (new App\Models\UserRelationBookmark())->where('relation_id', $relationId)->orWhere('sub_relation_id', $relationId)->delete();
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
