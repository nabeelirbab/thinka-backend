<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Generic\GenericController;
use App;
class NotificationUserController extends GenericController
{
    function __construct(){
        $this->model = new App\Models\NotificationUser();
        $this->tableStructure = [
            'columns' => [
            ],
            'foreign_tables' => [
                'notification' => [
                    "is_child" => false,
                    'foreign_tables' => [
                        'notification_relation_update' => [
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
                                ],
                                "relation" => [
                                    "validation_required" => false,
                                    'foreign_tables' => [
                                        "statement" => [
                                            "is_child" => false
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
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
            $queryModel = $queryModel->where('user_id', $this->userSession('id'));
            return $queryModel;
        };
        $this->initGenericController();
      }
}
