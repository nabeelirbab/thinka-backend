<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App;
use App\Generic\GenericController;

class UserRelationBookmarkController extends GenericController
{
  function __construct()
  {
    $this->model = new App\Models\UserRelationBookmark();
    $this->tableStructure = [
      'columns' => [],
      'foreign_tables' => [
        'relation' => [
          "validation_required" => false,
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
            'statement' => [
              "validation_required" => false,
              'foreign_tables' => [
                'statement_type' => []
              ]
            ],
            'logic_tree' => [
              "is_child" => true,
              'validation_required' => false
            ],
            'user' => [
              'validation_required' => false,
              'foreign_tables' => [
                "user_profile_photo" => [

                  'validation_required' => false,
                  "is_child" => false,
                ]
              ]
            ],
          ]
        ]
      ]
    ];
    $this->retrieveCustomQueryModel = function ($queryModel, &$leftJoinedTable) {
      $queryModel = $queryModel->where('user_relation_bookmarks.user_id', $this->userSession('id'))->orderBy('user_relation_bookmarks.created_at', 'desc');
      return $queryModel;
    };
    $this->initGenericController();
  }
}
