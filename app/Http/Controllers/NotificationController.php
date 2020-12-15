<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Generic\GenericController;
class NotificationController extends GenericController
{
    function __construct(){
        $this->model = new App\Models\Relation();
        $this->tableStructure = [
          'columns' => [
          ],
          'foreign_tables' => [
                'notification_relation_update' => [
                    "true_table" => 'relations',
                    "is_child" => true, 
                    "validation_required" => false,
                    'foreign_tables' => [
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
                ],
          ]
        ];
        $this->retrieveCustomQueryModel = function($queryModel, &$leftJoinedTable){
            $queryModel = $queryModel->where('user_id', $this->userSession('id'));
            return $queryModel;
        };
        $this->initGenericController();
      }
}
