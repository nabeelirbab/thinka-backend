<?php

namespace App\Http\Controllers\Relation;
use App;
class UserStatementLogicScore
{
  public function calculateUserStatementLogicScore($statementIds){
    $userStatementLogicScores = (new App\Models\UserStatementLogicScore())->whereIn('statement_id', array_unique($statementIds))->get()->toArray();
    $userLogicScores = [];
    foreach($userStatementLogicScores as $userStatementLogicScore){
      $userId = $userStatementLogicScore['user_id'];
      if(!isset($userLogicScores[$userId])){
        $userLogicScores[$userId] = [
          'user' => NULL,
          'opinion_count' => 0,
          'flag' => 0,
          'summed_opinion_score_truth' => 0,
          'max_opinion_confidence' => NULL,
          'max_opinion_score_truth' => NULL,
          'min_opinion_score_truth' => NULL,
          'final_score' => 0
        ];
      }
      $userLogicScores[$userId]['opinion_count'] += $userStatementLogicScore['opinion_count'];
      $userLogicScores[$userId]['summed_opinion_score_truth'] += $userStatementLogicScore['summed_opinion_score_truth'];
      if(
        $userLogicScores[$userId]['max_opinion_confidence'] == NULL 
        || 
        $userStatementLogicScore['max_opinion_confidence'] 
        > 
        $userLogicScores[$userId]['max_opinion_confidence']){
        $userLogicScores[$userId]['max_opinion_confidence'] = $userStatementLogicScore['max_opinion_confidence'];
      }
      if($userLogicScores[$userId]['max_opinion_score_truth'] == NULL || $userStatementLogicScore['max_opinion_score_truth'] > $userLogicScores[$userId]['max_opinion_score_truth']){
        $userLogicScores[$userId]['max_opinion_score_truth'] = $userStatementLogicScore['max_opinion_score_truth'];
      }
      if($userLogicScores[$userId]['min_opinion_score_truth'] == NULL || $userStatementLogicScore['min_opinion_score_truth'] < $userLogicScores[$userId]['min_opinion_score_truth']){
        $userLogicScores[$userId]['min_opinion_score_truth'] = $userStatementLogicScore['min_opinion_score_truth'];
      }
      if($userLogicScores[$userId]['flag'] === 0 || $userLogicScores[$userId]['flag'] == $userStatementLogicScore['flag']){ // if flags are the same
        $userLogicScores[$userId]['flag'] = $userStatementLogicScore['flag'];
      }else if($userLogicScores[$userId]['flag'] != $userStatementLogicScore['flag']){ // if flags are contradicting
        $userLogicScores[$userId]['flag'] = 3;
      }
    }
    $userIdList = [];
    foreach($userLogicScores as $key => $userLogicScore){
      $userIdList[] = $key;
      if($userLogicScore['flag'] == 0 ){ // if white flag, max score_truth
        $userLogicScores[$key]['final_score'] = $userLogicScore['max_opinion_confidence'];
      }else if($userLogicScore['flag'] == 1){ // if blue flag, max score_truth
        $userLogicScores[$key]['final_score'] = $userLogicScore['max_opinion_score_truth'] > 1 ? 1 : $userLogicScore['max_opinion_score_truth'];
      }else if($userLogicScore['flag'] == 2){ // if black flag, min score_truth
        $userLogicScores[$key]['final_score'] = $userLogicScore['min_opinion_score_truth'] < -1 ? -1 : $userLogicScore['min_opinion_score_truth'];
      }else{ // if contradicting
        $userLogicScores[$key]['final_score'] = 0;
      }
    }
    $users = (new App\Models\User())->select(['id', 'email', 'username'])
      ->whereIn('id', $userIdList)->get()->toArray();
    foreach($users as $user){
      $userLogicScores[$user['id']]['user'] = $user;
    }
    return $userLogicScores;
  }
}