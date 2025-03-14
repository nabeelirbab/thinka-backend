<?php

namespace App\Http\Controllers\Relation;

use App;

class UserStatementLogicScore
{
  // public function calculateUserStatementLogicScore($statementIds)
  // {
  //   $userStatementLogicScores = (new App\Models\UserStatementLogicScore())->whereIn('statement_id', array_unique($statementIds))->get()->toArray();
  //   // return $userStatementLogicScores;
  //   $userLogicScores = [];
  //   foreach ($userStatementLogicScores as $userStatementLogicScore) {
  //     $userId = $userStatementLogicScore['user_id'];
  //     if (!isset($userLogicScores[$userId])) {
  //       $userLogicScores[$userId] = [
  //         'user' => NULL,
  //         'opinion_count' => 0,
  //         'flag' => 0,
  //         'summed_opinion_score_truth' => 0,
  //         'max_opinion_confidence' => NULL,
  //         'max_opinion_score_truth' => NULL,
  //         'min_opinion_score_truth' => NULL,
  //         'final_score' => 0
  //       ];
  //     }
  //     $userLogicScores[$userId]['opinion_count'] += $userStatementLogicScore['opinion_count'];
  //     $userLogicScores[$userId]['summed_opinion_score_truth'] += $userStatementLogicScore['summed_opinion_score_truth'];
  //     if (
  //       $userLogicScores[$userId]['max_opinion_confidence'] == NULL
  //       || $userStatementLogicScore['max_opinion_confidence'] > $userLogicScores[$userId]['max_opinion_confidence']
  //     ) {
  //       $userLogicScores[$userId]['max_opinion_confidence'] = $userStatementLogicScore['max_opinion_confidence'];
  //     }
  //     if ($userLogicScores[$userId]['max_opinion_score_truth'] == NULL || $userStatementLogicScore['max_opinion_score_truth'] > $userLogicScores[$userId]['max_opinion_score_truth']) {
  //       $userLogicScores[$userId]['max_opinion_score_truth'] = $userStatementLogicScore['max_opinion_score_truth'];
  //     }
  //     if ($userLogicScores[$userId]['min_opinion_score_truth'] == NULL || $userStatementLogicScore['min_opinion_score_truth'] < $userLogicScores[$userId]['min_opinion_score_truth']) {
  //       $userLogicScores[$userId]['min_opinion_score_truth'] = $userStatementLogicScore['min_opinion_score_truth'];
  //     }
  //     if ($userLogicScores[$userId]['flag'] === 0 || $userLogicScores[$userId]['flag'] == $userStatementLogicScore['flag']) {
  //       // if flags are the same, keep it
  //       $userLogicScores[$userId]['flag'] = $userStatementLogicScore['flag'];
  //     } else if ($userLogicScores[$userId]['flag'] != $userStatementLogicScore['flag'] && $userStatementLogicScore['flag'] !== 0) {
  //       // if flags are contradicting
  //       $userLogicScores[$userId]['flag'] = 3;
  //     }
  //   }
  //   $userIdList = [];
  //   foreach ($userLogicScores as $userId => $userLogicScore) {
  //     $userIdList[] = $userId;
  //     if ($userLogicScore['flag'] == 0) { // if white flag, max score_truth
  //       $userLogicScores[$userId]['final_score'] = $userLogicScore['max_opinion_confidence'];
  //     } else if ($userLogicScore['flag'] == 1) { // if blue flag, max score_truth
  //       $userLogicScores[$userId]['final_score'] = $userLogicScore['max_opinion_score_truth'] > 1 ? 1 : $userLogicScore['max_opinion_score_truth'];
  //     } else if ($userLogicScore['flag'] == 2) { // if black flag, min score_truth
  //       $userLogicScores[$userId]['final_score'] = $userLogicScore['min_opinion_score_truth'] < -1 ? -1 : $userLogicScore['min_opinion_score_truth'];
  //     } else { // if contradicting
  //       $userLogicScores[$userId]['final_score'] = 0;
  //     }
  //   }
  //   $users = (new App\Models\User())->select(['id', 'email', 'username'])
  //     ->whereIn('id', $userIdList)->get()->toArray();
  //   foreach ($users as $user) {
  //     $userLogicScores[$user['id']]['user'] = $user;
  //   }
  //   return $userLogicScores;
  // }

  public function calculateUserStatementLogicScore($statementIds)
  {
    $userStatementLogicScores = (new App\Models\UserStatementLogicScore())
      ->whereIn('statement_id', array_unique($statementIds))
      ->get()
      ->toArray();

    $userLogicScores = [];

    foreach ($userStatementLogicScores as $userStatementLogicScore) {
      $userId = $userStatementLogicScore['user_id'];

      if (!isset($userLogicScores[$userId])) {
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

      // Aggregate values
      $userLogicScores[$userId]['opinion_count'] += $userStatementLogicScore['opinion_count'];
      $userLogicScores[$userId]['summed_opinion_score_truth'] += $userStatementLogicScore['summed_opinion_score_truth'];

      // Max opinion confidence
      if (
        $userLogicScores[$userId]['max_opinion_confidence'] === NULL ||
        $userStatementLogicScore['max_opinion_confidence'] > $userLogicScores[$userId]['max_opinion_confidence']
      ) {
        $userLogicScores[$userId]['max_opinion_confidence'] = $userStatementLogicScore['max_opinion_confidence'];
      }

      // Max opinion score truth
      if (
        $userLogicScores[$userId]['max_opinion_score_truth'] === NULL ||
        $userStatementLogicScore['max_opinion_score_truth'] > $userLogicScores[$userId]['max_opinion_score_truth']
      ) {
        $userLogicScores[$userId]['max_opinion_score_truth'] = $userStatementLogicScore['max_opinion_score_truth'];
      }

      // Min opinion score truth
      if (
        $userLogicScores[$userId]['min_opinion_score_truth'] === NULL ||
        $userStatementLogicScore['min_opinion_score_truth'] < $userLogicScores[$userId]['min_opinion_score_truth']
      ) {
        $userLogicScores[$userId]['min_opinion_score_truth'] = $userStatementLogicScore['min_opinion_score_truth'];
      }

      // Flag Handling
      if ($userLogicScores[$userId]['flag'] === 0 || $userLogicScores[$userId]['flag'] == $userStatementLogicScore['flag']) {
        // Keep flag if same
        $userLogicScores[$userId]['flag'] = $userStatementLogicScore['flag'];
      } else if ($userLogicScores[$userId]['flag'] != $userStatementLogicScore['flag'] && $userStatementLogicScore['flag'] !== 0) {
        // Contradicting flags
        $userLogicScores[$userId]['flag'] = 3;
      }
    }

    // Identify Contradictions
    foreach ($userLogicScores as $userId => $userLogicScore) {
      if (
        $userLogicScore['max_opinion_score_truth'] > 0 &&
        $userLogicScore['min_opinion_score_truth'] > 0
      ) {
        $userLogicScores[$userId]['flag'] = 3; // Contradiction (Red Flag)
      }
    }

    $userIdList = array_keys($userLogicScores);

    // Final Score Calculation
    foreach ($userLogicScores as $userId => $userLogicScore) {
      if ($userLogicScore['flag'] == 0) { // White flag → Use max confidence
        $userLogicScores[$userId]['final_score'] = $userLogicScore['max_opinion_confidence'];
      } else if ($userLogicScore['flag'] == 1) { // Blue flag → Use max score truth
        $userLogicScores[$userId]['final_score'] = $userLogicScore['max_opinion_score_truth'];
      } else if ($userLogicScore['flag'] == 2) { // Black flag → Use min score truth
        $userLogicScores[$userId]['final_score'] = $userLogicScore['min_opinion_score_truth'];
      } else { // Contradiction (Red Flag)
        $userLogicScores[$userId]['final_score'] = 0;
      }
    }

    // Fetch user details
    $users = (new App\Models\User())->select(['id', 'email', 'username'])
      ->whereIn('id', $userIdList)
      ->get()
      ->toArray();

    // Assign users to results
    foreach ($users as $user) {
      $userLogicScores[$user['id']]['user'] = $user;
    }

    return $userLogicScores;
  }
}
