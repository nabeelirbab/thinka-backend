<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Generic\GenericController;
use App;
use Illuminate\Support\Facades\Validator;

class NotificationUserController extends GenericController
{
    function __construct(){
        $this->model = new App\Models\NotificationUser();
        $userTemplate = [
            'validation_required' => false,
            'foreign_tables' => [
                "user_basic_information" => [
                    'validation_required' => false,
                    "is_child" => false,
                ]
            ]
        ];
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
                                "user" => $userTemplate,
                                "relation" => [
                                    "validation_required" => false,
                                    'foreign_tables' => [
                                        "statement" => [
                                            "is_child" => false
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
                                ]
                            ]
                        ],
                        'notification_sub_relation_update' => [
                            "is_child" => true,
                            "validation_required" => false,
                            'foreign_tables' => [
                                "user" => $userTemplate,
                                "sub_relation" => [
                                    "true_table" => "relations",
                                    "validation_required" => false,
                                    'foreign_tables' => [
                                        "statement" => [
                                            "is_child" => false
                                        ]
                                    ]
                                ],
                                'notification_sub_relation_update_user_relations' => [
                                    'foreign_tables' => [
                                        "user" => $userTemplate,
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
                        'notification_statement_update' => [
                            "is_child" => true, 
                            "validation_required" => false,
                            'foreign_tables' => [
                                "user" => $userTemplate,
                                "relation" => [
                                    "validation_required" => false
                                ],
                                "statement" => [
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
    public function changeStatus(Request $request){
        $entry = $request->all();
        $validator = Validator::make($entry, [
            'id_list' => 'required|array',
            'id_list.*' => 'required|numeric|exists:notification_users,id',
            'status' => 'required|in:0,1,2'
        ]);
        if($validator->fails()){
            $this->responseGenerator->setFail([
              "code" => 1,
              "message" => $validator->errors()->toArray()
            ]);
        }else{
            $updateResult = (new App\Models\NotificationUser())
                ->where('user_id', $this->userSession('id'))
                ->whereIn('id', $entry['id_list'])
                ->update(['status' => $entry['status']]);
            $this->responseGenerator->setSuccess(true);
        }
        return $this->responseGenerator->generate();
    }
}
