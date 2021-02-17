<?php

namespace App\Http\Controllers\Relation;
use App;
class NotificationBuilder
{
  public $authorOfRelation = 1;
  public $authorOfParentRelation = 2;
  public $userWithBookmarkOfRelation = 3;
  public $userWithBookmarkOfParentRelation = 4;
  public $usersToNotify = [];
  public $messages = [];
  public $reasonExtraData = [
    '1' => [
      'is_author' => true
    ],
    '2' => [
      'is_author_of_parent_relation' => true
    ],
    '3' => [
      'has_bookmark_of_relation' => true
    ],
    '4' => [
      'has_bookmark_of_parent_relation' => true
    ],
  ];
  public function getUserToNotifyFromParentRelation($relationId){
    $this->getRelation($relationId);
    return $this->usersToNotify;
  }
  public function getRelation($relationId, $level = 0){
    $relation = (new App\Models\Relation())
    ->where('id', $relationId)
    ->with('all_user_relation_bookmarks')
    ->get()->toArray();
    if(count($relation)){
      $relation = $relation[0];
      if(!isset($this->usersToNotify[$relation['user_id']])){
        $this->usersToNotify[$relation['user_id']] = 1000;
      }
      if($this->usersToNotify[$relation['user_id']] > $this->authorOfParentRelation){
        $this->usersToNotify[$relation['user_id']] = $this->authorOfParentRelation;
      }

      $userRelationBookmarks = $relation['all_user_relation_bookmarks'];
      foreach($userRelationBookmarks as $userRelationBookmark){
        $userId = $userRelationBookmark['user_id'];
        if(!isset($this->usersToNotify[$userId])){
          $this->usersToNotify[$userId] = 1000;
        }
        if($this->usersToNotify[$userId] > $this->userWithBookmarkOfRelation && $level === 0){
          $this->usersToNotify[$userId] = $this->userWithBookmarkOfRelation;
        }
        if($this->usersToNotify[$userId] > $this->userWithBookmarkOfParentRelation){
          $this->usersToNotify[$userId] = $this->userWithBookmarkOfParentRelation;
        }
      }
      // $usersToNotify[$relation['user_id']] = $authorOfParentRelation;
      if($relation['parent_relation_id']){
        $this->getRelation($relation['parent_relation_id'], $level + 1);
      }
      if($level === 0){
        $this->usersToNotify[$relation['user_id']] = $this->authorOfRelation;
      }
    }
  }
  public function withNotificationUserExtraData(){
    foreach($this->usersToNotify as $userId => $reason){
      $this->usersToNotify[$userId] = $this->reasonExtraData[$reason];
    }
    return $this->usersToNotify;
  }
}