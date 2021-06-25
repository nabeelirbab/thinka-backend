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
use App\Http\Controllers\Relation\UserStatementLogicScore as UserStatementLogicScore;
use Mail;

class RelationController extends GenericController
{
  private $preFormattedSelect = [
    'statement' => [
      'select' => ['id', 'text', 'synopsis', 'comment', 'scope', 'scope_id', 'statement_type_id']
    ],
    'user' => [
      'select' => [
        'id', 'username',
        'user_basic_information' => [
          'select' => ['user_id', 'first_name', 'last_name']
        ]
      ]
    ],
    'user_opinion' => [
      'select' => [
        'opinion_calculated_column' => [
          'select' => ['id', 'score_relation', 'score_statement']
        ],
        'id', 'user_id', 'relation_id', 'confidence', 'type'
      ]
    ],
    'parent_relation_id', 'logic_tree_id', 'statement_id', 'relation_type_id', 'relevance_window', 'user_id', 'published_at', 'logic_tree_id', 'impact', 'impact_amount', 'created_at', 'virtual_relation_id'
  ];
  function __construct(){
    $this->model = new App\Models\Relation();
    $recursiveRelationForeignTable = $this->generateRecursiveRelationForeignTable(1);
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
          ]
        ],
        'user_opinion' => [
          "true_table" => 'opinions',
          "is_child" => true, 
          'foreign_tables' => [
            'opinion_calculated_column' => [
              "is_child" => true,
            ]
          ]
        ],
        'user_relation_bookmarks' => [],
        'relations' => $recursiveRelationForeignTable, // sub relations
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
        ],
        "virtual_relation" => [
          "is_child" => false,
          'validation_required' => false,
          "true_table" => 'relations',
          'foreign_tables' => [
            'relations' => $recursiveRelationForeignTable, // sub relations
            'statement' => [
              "is_child" => false,
              "validation_required" => false,
              'foreign_tables' => [
                'statement_type' => []
              ]
            ],
          ]
        ],
        'root_parent_relation' => [
          "true_table" => 'relations',
          "is_child" => false, 
          "validation_required" => false,
          'foreign_tables' => [
            'statement' => [
              "is_child" => false,
            ]
          ]
        ],
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
  public function retrieveTree(Request $request){
    $requestArray = $request->all();
    $validator = Validator::make($requestArray, [
      "relation_id" => "required|exists:relations,id"
    ]);
    if($validator->fails()){
      $this->responseGenerator->setFail([
        "code" => 1,
        "message" => $validator->errors()->toArray()
      ]); 
      return $this->responseGenerator->generate();
    }
    $relationId = $requestArray['relation_id'];
    $userId = $this->userSession('id');
    $relationModel = (new App\Models\Relation())->find($relationId);
    if($relationModel->published_at !== null || $relationModel->user_id * 1 === $userId * 1){
      $relation = $this->recursiveRetrieveTree([$relationId]);
      if($relation[0]['statement_id']){
        $relation[0]['sub_relation_statement_id_list'][] = $relation[0]['statement_id'];
      }
      $relation[0]['user_statement_logic_scores'] = (new UserStatementLogicScore())->calculateUserStatementLogicScore($relation[0]['sub_relation_statement_id_list']);
      $parentRelationUserFollowings = $relation[0]['parent_relation_id'] ? $this->getParentRelationUserFollowing($relation[0]['parent_relation_id']) : [];
      $relation[0]['parent_relation_user_following'] = [];
      if(count($parentRelationUserFollowings)){
        $userIdList = [];
        foreach($parentRelationUserFollowings as $userId => $value){
          $userIdList[] = $userId * 1;
        }
        $relation[0]['parent_relation_user_following'] = (new App\Models\User())->with(['user_basic_information'])->whereIn('id', $userIdList)->get()->toArray();
      }
      $this->responseGenerator->addDebug('parentRelationUserFollowings', $parentRelationUserFollowings);
      $this->responseGenerator->setSuccess($relation);
      return $this->responseGenerator->generate();
    }else{
      $this->responseGenerator->setFail([
        "code" => 1,
        "message" => 'Statement is not published'
      ]);
      return $this->responseGenerator->generate();
    }
  }
  private function getParentRelationUserFollowing($relationId){
    if($relationId){
      $relation = (new App\Models\Relation())->find($relationId)->with(['all_user_relation_bookmarks' => function($query){
        $query->with(['user']);
      }])->get()->toArray();
      if(count($relation)){
        $relation = $relation[0];
        $users = [];
        $users[$relation['user_id']] = true;
        foreach($relation['all_user_relation_bookmarks'] as $userRelationBookmark){
          $users[$userRelationBookmark['user_id']] = true;
        }
        $parentRelationUserFollowing = [];
        if($relation['parent_relation_id']){
          $parentRelationUserFollowing = $this->getParentRelationUserFollowing($relation['parent_relation_id']);
        }
        foreach($parentRelationUserFollowing as $userIdKey => $value){
          $users[$userIdKey] = $value;
        }
        return $users; // array_merge($users, $parentRelationUserFollowing);
      }
    }else{
      return [];
    }
  }
  public function recursiveRetrieveTree($relationIds, $currentDeep = 0, $deep = 20){ // if deep is 0, relationsIds containes the parent relation ids
    if(count($relationIds) == 0){
      return [];
    }
    $with = [
      'user_relation_bookmarks',
      'statement',
      'user',
      'user.user_basic_information',
      'user_opinion',
      'user_opinion.opinion_calculated_column',
      'user_opinions',
      'user_opinions.opinion_calculated_column',
      'all_user_relation_bookmarks' => function($query){
        $query->with(['user']);
      },
      'all_user_sub_relation_bookmarks' => function($query){
        $query->with(['user']);
      },
      'all_user_sub_relation_bookmarks' => function($query){
        $query->with(['user']);
      },
      'user_relation_context_locks' => function($query){
        $query->where('user_id', $this->userSession('id'));
      }
    ];
    $relationModel = (new App\Models\Relation());
    if($currentDeep == 0){ // main relation if the current deep is zero
      $with = array_merge($with, [
        'logic_tree',
        'parent_relation',
        'parent_relation.statement'
      ]);
      $relationModel = $relationModel->whereIn('id', $relationIds);
    }else{
      $relationModel = $relationModel->whereIn('parent_relation_id', $relationIds);
    }
    $relationModel = $relationModel->where(function($query){
      $query->where('published_at', '!=', NULL);
      $query->orWhere('user_id', $this->userSession('id'));
    });
    $relationModel = $relationModel->with($with);
    $relations = $relationModel->get()->toArray();
    if($currentDeep < $deep){
      ++$currentDeep;
      $relationIdLookUp = [];
      $relationIdList = [];
      $virtualRelationParentRelationLookUp = []; // container object where value is array of relation index where virtual_relation belongs
      $virtualRelationIdList = [];
      foreach($relations as $relationKey => $relation){
        $relationIdList[] = $relation['id'] * 1;
        $relationIdLookUp[$relation['id']] = $relationKey;
        $relations[$relationKey]['relations'] = [];
        $relations[$relationKey]['sub_relation_statement_id_list'] = array(); // object of statement ids, contains all the statement ids of the sub relations
        $relations[$relationKey]['virtual_relation'] = null;
        if($relation['virtual_relation_id'] != null){
          if(!isset($virtualRelationParentRelationLookUp[$relation['virtual_relation_id']])){
            $virtualRelationParentRelationLookUp[$relation['virtual_relation_id']] = [];
          }
          $virtualRelationIdList[] = $relation['virtual_relation_id'] * 1;
          $virtualRelationParentRelationLookUp[$relation['virtual_relation_id']][] = $relationKey;
        }
      }
      $virtualRelations = $this->recursiveRetrieveTree($virtualRelationIdList, 0, $deep - $currentDeep);
      foreach($virtualRelations as $virtualRelation){
        foreach($virtualRelationParentRelationLookUp[$virtualRelation['id']] as $relationIndex){
          if($virtualRelation['statement_id']){
            $relations[$relationIndex]['sub_relation_statement_id_list'][] = $virtualRelation['statement_id'];
          }
          $relations[$relationIndex]['sub_relation_statement_id_list'] = array_merge($relations[$relationIndex]['sub_relation_statement_id_list'], $virtualRelation['sub_relation_statement_id_list']);
          // unset($virtualRelation['sub_relation_statement_id_list']);
          $relations[$relationIndex]['virtual_relation'] = $virtualRelation;
        }
      }
      $subRelations = $this->recursiveRetrieveTree($relationIdList, $currentDeep);
      foreach($subRelations as $subRelation){
        $parentRelationIndex = $relationIdLookUp[$subRelation['parent_relation_id']];
        if($subRelation['statement_id']){
          $relations[$parentRelationIndex]['sub_relation_statement_id_list'][] = $subRelation['statement_id'];
        }
        $relations[$parentRelationIndex]['sub_relation_statement_id_list'] = array_merge($relations[$parentRelationIndex]['sub_relation_statement_id_list'], $subRelation['sub_relation_statement_id_list']);
        // unset($subRelation['sub_relation_statement_id_list']);
        $relations[$parentRelationIndex]['relations'][] = $subRelation;
      }
    }
    return $relations;
  }
  public function retrieve(Request $request){
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
        ],
        'user_opinion' => [
          "true_table" => 'opinions',
          "is_child" => true, 
          'foreign_tables' => [
            'opinion_calculated_column' => [
              "is_child" => true,
            ]
          ]
        ],
        "virtual_relation" => [
          "is_child" => false,
          'validation_required' => false,
          "true_table" => 'relations',
          'foreign_tables' => [
            'statement' => [
              "is_child" => false,
              "validation_required" => false,
              'foreign_tables' => [
                'statement_type' => []
              ]
            ],
          ]
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
      $relationModel = (new App\Models\Relation())->with(['statement'])->find($entry['id']);
      if($relationModel->user_id === $this->userSession('id')){
        $publishedAt = $entry['published_at'] ? date('Y-m-d H:i:s') : null;
        $relationToNotifyList = array(); // contains object where key is the published/unpublished statements and the value is an array of parent ids
        
        $this->recursivePublish($entry['id'], $publishedAt, $relationToNotifyList, 0);
        $this->responseGenerator->addDebug('$relationToNotifyList', $relationToNotifyList);
        if($relationModel->parent_relation_id === null){
          $logicTreeModel = (new App\Models\LogicTree())->find($relationModel->logic_tree_id);
          $logicTreeModel->published_at = $publishedAt;
          $logicTreeModel->save();
        }
        $userToNotify = array(); // object - key is user id
        $relationIds = array();
        // get the deepest
        $parentRelations = [];
        if($relationModel->parent_relation_id){
          $parentRelations = $this->getParentRelations($relationModel->id);
          foreach($parentRelations as $parentRelation){
            $userToNotify[$parentRelation['user_id']] = 1; // 1- co-author, 2 - bookmarkers
            $relationIds[] = $parentRelation['id'];
          }
        }
        
        foreach($relationToNotifyList as $relationToNotifyId => $relationToNotifyList){
          $userToNotify[$relationToNotifyList['user_id']] = 1; // 1- co-author, 2 - bookmarkers
          $relationIds[] = $relationToNotifyId;
        }
        $userRelationBookmarks = (new App\Models\UserRelationBookmark())->whereIn('relation_id', $relationIds)->get()->toArray();
        foreach($userRelationBookmarks as $userRelationBookmark){
          if(!isset($userToNotify[$userRelationBookmark['user_id']])){
            $userToNotify[$userRelationBookmark['user_id']] = 2;// 1- co-author, 2 - bookmarkers
          }
        }
        $coAuthors = [];
        $bookmarkers = []; // users who bookmark
        foreach($userToNotify as $userId => $type){
          if($userId * 1 != $this->userSession('id') * 1){
            if($type === 1){
              $coAuthors[] = $userId;
            }else{
              $bookmarkers[] = $userId;
            }
          }
        }
        $this->responseGenerator->addDebug('bookmarkers', $bookmarkers);
        $this->notifySubscribers(
          2, // publish co author
          $entry['id'], // relation id of head relation being published
          $this->userSession('id'),
          $coAuthors,
          'A sub statement has been ' . ($publishedAt ? 'added or published' : 'unpublished'),
          $relationModel->parent_relation_id,
          $relationModel->statement->text
        );
        $this->notifySubscribers(
          3, // publish bookmarkers
          $entry['id'], // relation id of head relation being published
          $this->userSession('id'),
          $bookmarkers,
          'A sub statement has been ' . ($publishedAt ? 'added or published' : 'unpublished') . ' on a statement that you bookmarked',
          $relationModel->parent_relation_id,
          $relationModel->statement->text
        );
        
        // $this->notifyPublishedStatementParents($relationToNotifyId, $notificationMessage, $toNotifyRelationArray, $relationToNotifyList['users']); // notify parents authors and bookmarkers
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
  private function notifySubscribers($type, $subRelationId, $userId, $subscriberUserIds, $message, $parentRelationId, $statementText){
    (new App\Models\Notification())->createSubRelationUpdateNotification(
      $type,
      $subRelationId,
      $userId,
      $subscriberUserIds, 
      $message
    );
    $users = (new App\Models\User())->select(['id', 'email', 'username'])->whereIn('id', $subscriberUserIds)->get()->toArray();
    $kebabStatement = preg_replace('/[[:space:]]+/', '-', strtolower($statementText));
    $data = [
      'message' => $message,
      'parentRelationId' => $parentRelationId,
      'statement' => $statementText,
      'kebabStatement' => $kebabStatement
    ];
    $this->responseGenerator->addDebug('mail_data', $data);
    if(config('app.MAIL_MAILER') === 'smtp'){
      $this->responseGenerator->addDebug('MAIL_MAILERPass', config('app.MAIL_MAILER'));
      foreach($users as $user){
        $data['username'] = $user['username'];
        Mail::send('sub-relation-published.php', $data, function($message) use ($user) {
          $message->to($user['email'])
          ->subject('Statement Tree Update');
          $message->from('noreply@thinka.io','Thinka');
        });
      }
    }
  }
  private function getParentRelations($relationId){
    $parentIds = [];
    $hasParent = true;
    $currentRelationId = $relationId;
    do{
      $relationModel = (new App\Models\Relation())->find($currentRelationId);
      if($relationModel->id && $relationModel->parent_relation_id){
        $parentIds[] = [
          'id' => $relationModel->parent_relation_id,
          'user_id' => $relationModel->user_id
        ];
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
    $notificationUsers = (new App\Models\Notification())->createSubRelationUpdateNotification($publishedRelationId, $this->userSession('id'), $userRelationBookmarks, $message);
    $userIds = [];
    foreach($notificationUsers as $notificationUser){
      $userIds[] = $notificationUser['user_id'];
    }
    
  }
  public function join(Request $request){
    $validator = Validator::make($request->all(), [
      'parent_relation_id' => 'required|exists:relations,id',
      'relation_id' => 'required|exists:relations,id',
      'relation_type_id' => 'required|exists:relation_types,id',
      'impact_amount' => 'required',
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
        $relationModel->relation_type_id = $entry['relation_type_id'];
        $relationModel->impact_amount = $entry['impact_amount'];
        $relationModel->published_at = date('Y-m-d H:i:s');
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
  public function link(Request $request){
    $validator = Validator::make($request->all(), [
      'parent_relation_id' => 'required|exists:relations,id',
      'virtual_relation_id' => 'required|exists:relations,id',
      'relevance_window' => 'required',
      'relation_type_id' => 'required|exists:relation_types,id',
      'logic_tree_id' => 'required|exists:logic_trees,id',
      'is_published' => 'required',
    ]);
    if($validator->fails()){
      $this->responseGenerator->setFail([
        "code" => 1,
        "message" => $validator->errors()->toArray()
      ]);
    }else{
      $entry = $request->all();
      $virtualRelationModel = (new App\Models\Relation())->find($entry['virtual_relation_id']);
      $parentRelationModel = (new App\Models\Relation())->find($entry['parent_relation_id']);
      if($virtualRelationModel->published_at || $virtualRelationModel->user_id == $this->userSession('id')){
        $relationModel = (new App\Models\Relation());
        $relationModel->virtual_relation_id = $entry['virtual_relation_id'];
        $relationModel->parent_relation_id = $entry['parent_relation_id'];
        $relationModel->relevance_window = $entry['relevance_window'];
        $relationModel->relation_type_id = $entry['relation_type_id'];
        $relationModel->logic_tree_id = $entry['logic_tree_id'];
        $relationModel->published_at = isset($entry['is_published']) && $entry['is_published'] ? date('Y-m-d H:i:s') : null;
        $relationModel->user_id = $this->userSession('id');
        $relationModel->save();
        $this->responseGenerator->setSuccess([
          'id' => $relationModel->id,
        ]);
      }else{
        $this->responseGenerator->setFail([
          "code" => 2,
          "message" => 'Cannot link unpublished relations'
        ]);
      }
    }
    return $this->responseGenerator->generate();
  }
  private function recursivePublish($relationId, $publishedAt, &$relationToNotifyList, $deep = 0){
    $relation = (new App\Models\Relation())->with(['relations' => function($query){
      $query->where('user_id', $this->userSession('id'));
    }, 'virtual_relation'])->find($relationId);
    $relationResult = $relation->get()->toArray();
    if($publishedAt && $relation['virtual_relation'] && !$relation['virtual_relation']['published_at']){ // the virtual relation is not yet published
      return false;
    }
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
      $relationToNotifyList[$relationId] = [
        'user_id' => $relationResult[0]['user_id'],
        'deep' => $deep
      ];
    }
    if(count($subRelations)){
      foreach($subRelations as $subRelation){
        $this->recursivePublish($subRelation['id'], $publishedAt, $relationToNotifyList, $deep + 1);
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
    //   DB::raw("SET @p0='" . $this->userSession('id') . "';")
    // );
    // $result = DB::select(
    //   DB::raw("call statement_my_list(@p0)")
    // );
    $relations = (new Relation())
      ->with([
        'statement',
        'root_parent_relation' => function($query){
          return $query->where('root_parent_relation.logic_tree_id', 'relations.logic_tree_id');
        }
      ])
      ->where('user_id',  $this->userSession('id'))
      ->get()->toArray();
    $this->responseGenerator->setSuccess($relations);
    return $this->responseGenerator->generate();
  }
}
