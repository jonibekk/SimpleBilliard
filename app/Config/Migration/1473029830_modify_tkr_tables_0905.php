<?php

class ModifyTkrTables0905 extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'modify_tkr_tables_0905';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = [
        'up'   => [
            'create_table' => [
                'goal_change_logs'     => [
                    'id'              => [
                        'type'     => 'biginteger',
                        'null'     => false,
                        'default'  => null,
                        'unsigned' => true,
                        'key'      => 'primary',
                        'comment'  => 'ID'
                    ],
                    'team_id'         => [
                        'type'     => 'biginteger',
                        'null'     => false,
                        'default'  => null,
                        'unsigned' => true,
                        'key'      => 'index',
                        'comment'  => 'チームID(belongsToでTeamモデルに関連)'
                    ],
                    'goal_id'         => [
                        'type'     => 'biginteger',
                        'null'     => false,
                        'default'  => null,
                        'unsigned' => true,
                        'key'      => 'index',
                        'comment'  => 'ゴールID(belongsToでGoalモデルに関連)'
                    ],
                    'user_id'         => [
                        'type'     => 'biginteger',
                        'null'     => false,
                        'default'  => null,
                        'unsigned' => true,
                        'key'      => 'index',
                        'comment'  => '作成者ID(belongsToでUserモデルに関連)'
                    ],
                    'data'            => [
                        'type'    => 'text',
                        'null'    => false,
                        'default' => null,
                        'collate' => 'utf8mb4_general_ci',
                        'comment' => 'データ(現時点のゴールのスナップショット)MessagePackで圧縮',
                        'charset' => 'utf8mb4'
                    ],
                    'del_flg'         => ['type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'],
                    'deleted'         => [
                        'type'     => 'integer',
                        'null'     => true,
                        'default'  => null,
                        'unsigned' => true,
                        'comment'  => '削除した日付時刻'
                    ],
                    'created'         => [
                        'type'     => 'integer',
                        'null'     => true,
                        'default'  => null,
                        'unsigned' => true,
                        'comment'  => '追加した日付時刻'
                    ],
                    'modified'        => [
                        'type'     => 'integer',
                        'null'     => true,
                        'default'  => null,
                        'unsigned' => true,
                        'key'      => 'index',
                        'comment'  => '更新した日付時刻'
                    ],
                    'indexes'         => [
                        'PRIMARY'  => ['column' => 'id', 'unique' => 1],
                        'team_id'  => ['column' => 'team_id', 'unique' => 0],
                        'goal_id'  => ['column' => 'goal_id', 'unique' => 0],
                        'modified' => ['column' => 'modified', 'unique' => 0],
                        'user_id'  => ['column' => 'user_id', 'unique' => 0],
                    ],
                    'tableParameters' => [
                        'charset' => 'utf8mb4',
                        'collate' => 'utf8mb4_general_ci',
                        'engine'  => 'InnoDB'
                    ],
                ],
                'goal_clear_evaluates' => [
                    'id'              => [
                        'type'     => 'biginteger',
                        'null'     => false,
                        'default'  => null,
                        'unsigned' => true,
                        'key'      => 'primary',
                        'comment'  => 'フォロワーID'
                    ],
                    'team_id'         => [
                        'type'     => 'biginteger',
                        'null'     => false,
                        'default'  => null,
                        'unsigned' => true,
                        'key'      => 'index',
                        'comment'  => 'チームID(belongsToでTeamモデルに関連)'
                    ],
                    'goal_id'         => [
                        'type'     => 'biginteger',
                        'null'     => false,
                        'default'  => null,
                        'unsigned' => true,
                        'key'      => 'index',
                        'comment'  => 'ゴールID(belongsToでGoalモデルに関連)'
                    ],
                    'user_id'         => [
                        'type'     => 'biginteger',
                        'null'     => false,
                        'default'  => null,
                        'unsigned' => true,
                        'key'      => 'index',
                        'comment'  => 'ユーザID(belongsToでUserモデルに関連)'
                    ],
                    'cleared_flg'     => [
                        'type'    => 'boolean',
                        'null'    => false,
                        'default' => '0',
                        'comment' => 'Clear: 1, Not Clear:0'
                    ],
                    'del_flg'         => ['type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'],
                    'deleted'         => [
                        'type'     => 'integer',
                        'null'     => true,
                        'default'  => null,
                        'unsigned' => true,
                        'comment'  => '削除した日付時刻'
                    ],
                    'created'         => [
                        'type'     => 'integer',
                        'null'     => true,
                        'default'  => null,
                        'unsigned' => true,
                        'comment'  => '追加した日付時刻'
                    ],
                    'modified'        => [
                        'type'     => 'integer',
                        'null'     => true,
                        'default'  => null,
                        'unsigned' => true,
                        'key'      => 'index',
                        'comment'  => '更新した日付時刻'
                    ],
                    'indexes'         => [
                        'PRIMARY'  => ['column' => 'id', 'unique' => 1],
                        'team_id'  => ['column' => 'team_id', 'unique' => 0],
                        'user_id'  => ['column' => 'user_id', 'unique' => 0],
                        'modified' => ['column' => 'modified', 'unique' => 0],
                        'goal_id'  => ['column' => 'goal_id', 'unique' => 0],
                    ],
                    'tableParameters' => [
                        'charset' => 'utf8mb4',
                        'collate' => 'utf8mb4_general_ci',
                        'engine'  => 'InnoDB'
                    ],
                ],
                'goal_labels'          => [
                    'id'              => [
                        'type'     => 'biginteger',
                        'null'     => false,
                        'default'  => null,
                        'unsigned' => true,
                        'key'      => 'primary',
                        'comment'  => 'アクションリザルトID'
                    ],
                    'team_id'         => [
                        'type'     => 'biginteger',
                        'null'     => false,
                        'default'  => null,
                        'unsigned' => true,
                        'key'      => 'index',
                        'comment'  => 'チームID(belongsToでTeamモデルに関連)'
                    ],
                    'goal_id'         => [
                        'type'     => 'biginteger',
                        'null'     => true,
                        'default'  => null,
                        'unsigned' => true,
                        'key'      => 'index',
                        'comment'  => 'ゴールID(belongsToでGoalモデルに関連)'
                    ],
                    'label_id'        => [
                        'type'     => 'biginteger',
                        'null'     => true,
                        'default'  => null,
                        'unsigned' => true,
                        'key'      => 'index',
                        'comment'  => 'ラベルID(belongsToでLabelモデルに関連)'
                    ],
                    'del_flg'         => ['type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'],
                    'deleted'         => [
                        'type'     => 'integer',
                        'null'     => true,
                        'default'  => null,
                        'unsigned' => true,
                        'comment'  => '削除した日付時刻'
                    ],
                    'created'         => [
                        'type'     => 'integer',
                        'null'     => true,
                        'default'  => null,
                        'unsigned' => true,
                        'key'      => 'index',
                        'comment'  => '追加した日付時刻'
                    ],
                    'modified'        => [
                        'type'     => 'integer',
                        'null'     => true,
                        'default'  => null,
                        'unsigned' => true,
                        'key'      => 'index',
                        'comment'  => '更新した日付時刻'
                    ],
                    'indexes'         => [
                        'PRIMARY'  => ['column' => 'id', 'unique' => 1],
                        'team_id'  => ['column' => 'team_id', 'unique' => 0],
                        'modified' => ['column' => 'modified', 'unique' => 0],
                        'goal_id'  => ['column' => 'goal_id', 'unique' => 0],
                        'created'  => ['column' => 'created', 'unique' => 0],
                        'label_id' => ['column' => 'label_id', 'unique' => 0],
                    ],
                    'tableParameters' => [
                        'charset' => 'utf8mb4',
                        'collate' => 'utf8mb4_general_ci',
                        'engine'  => 'InnoDB'
                    ],
                ],
                'labels'               => [
                    'id'              => [
                        'type'     => 'biginteger',
                        'null'     => false,
                        'default'  => null,
                        'unsigned' => true,
                        'key'      => 'primary',
                        'comment'  => 'ID'
                    ],
                    'team_id'         => [
                        'type'     => 'biginteger',
                        'null'     => false,
                        'default'  => null,
                        'unsigned' => true,
                        'key'      => 'index',
                        'comment'  => 'チームID(belongsToでTeamモデルに関連)'
                    ],
                    'name'            => [
                        'type'    => 'string',
                        'null'    => false,
                        'length'  => 128,
                        'collate' => 'utf8mb4_general_ci',
                        'comment' => 'ラベル',
                        'charset' => 'utf8mb4'
                    ],
                    'del_flg'         => ['type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'],
                    'deleted'         => [
                        'type'     => 'integer',
                        'null'     => true,
                        'default'  => null,
                        'unsigned' => true,
                        'comment'  => '部署を削除した日付時刻'
                    ],
                    'created'         => [
                        'type'     => 'integer',
                        'null'     => true,
                        'default'  => null,
                        'unsigned' => true,
                        'comment'  => '部署を追加した日付時刻'
                    ],
                    'modified'        => [
                        'type'     => 'integer',
                        'null'     => true,
                        'default'  => null,
                        'unsigned' => true,
                        'comment'  => '部署を更新した日付時刻'
                    ],
                    'indexes'         => [
                        'PRIMARY' => ['column' => 'id', 'unique' => 1],
                        'team_id' => ['column' => 'team_id', 'unique' => 0],
                    ],
                    'tableParameters' => [
                        'charset' => 'utf8mb4',
                        'collate' => 'utf8mb4_general_ci',
                        'engine'  => 'InnoDB'
                    ],
                ],
                'tkr_change_logs'      => [
                    'id'              => [
                        'type'     => 'biginteger',
                        'null'     => false,
                        'default'  => null,
                        'unsigned' => true,
                        'key'      => 'primary',
                        'comment'  => 'ID'
                    ],
                    'team_id'         => [
                        'type'     => 'biginteger',
                        'null'     => false,
                        'default'  => null,
                        'unsigned' => true,
                        'key'      => 'index',
                        'comment'  => 'チームID(belongsToでTeamモデルに関連)'
                    ],
                    'goal_id'         => [
                        'type'     => 'biginteger',
                        'null'     => false,
                        'default'  => null,
                        'unsigned' => true,
                        'key'      => 'index',
                        'comment'  => 'ゴールID(belongsToでGoalモデルに関連)'
                    ],
                    'key_result_id'   => [
                        'type'     => 'biginteger',
                        'null'     => false,
                        'default'  => null,
                        'unsigned' => true,
                        'key'      => 'index',
                        'comment'  => 'キーリザルトID(belongsToでKeyResultモデルに関連)'
                    ],
                    'user_id'         => [
                        'type'     => 'biginteger',
                        'null'     => false,
                        'default'  => null,
                        'unsigned' => true,
                        'key'      => 'index',
                        'comment'  => '作成者ID(belongsToでUserモデルに関連)'
                    ],
                    'data'            => [
                        'type'    => 'text',
                        'null'    => false,
                        'default' => null,
                        'collate' => 'utf8mb4_general_ci',
                        'comment' => 'データ(現時点のTKRのスナップショット)MessagePackで圧縮',
                        'charset' => 'utf8mb4'
                    ],
                    'del_flg'         => ['type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'],
                    'deleted'         => [
                        'type'     => 'integer',
                        'null'     => true,
                        'default'  => null,
                        'unsigned' => true,
                        'comment'  => '削除した日付時刻'
                    ],
                    'created'         => [
                        'type'     => 'integer',
                        'null'     => true,
                        'default'  => null,
                        'unsigned' => true,
                        'comment'  => '追加した日付時刻'
                    ],
                    'modified'        => [
                        'type'     => 'integer',
                        'null'     => true,
                        'default'  => null,
                        'unsigned' => true,
                        'key'      => 'index',
                        'comment'  => '更新した日付時刻'
                    ],
                    'indexes'         => [
                        'PRIMARY'       => ['column' => 'id', 'unique' => 1],
                        'team_id'       => ['column' => 'team_id', 'unique' => 0],
                        'goal_id'       => ['column' => 'goal_id', 'unique' => 0],
                        'modified'      => ['column' => 'modified', 'unique' => 0],
                        'user_id'       => ['column' => 'user_id', 'unique' => 0],
                        'key_result_id' => ['column' => 'key_result_id', 'unique' => 0],
                    ],
                    'tableParameters' => [
                        'charset' => 'utf8mb4',
                        'collate' => 'utf8mb4_general_ci',
                        'engine'  => 'InnoDB'
                    ],
                ],
            ],
            'drop_field'   => [
                'goals' => [
                    'purpose_id',
                    'current_value',
                    'start_value',
                    'target_value',
                    'value_unit',
                    'indexes' => ['purpose_id']
                ],
            ],
            'create_field' => [
                'key_results' => [
                    'tkr_flg' => [
                        'type'    => 'boolean',
                        'null'    => false,
                        'default' => '0',
                        'comment' => 'TopKeyResult',
                        'after'   => 'action_result_count'
                    ],
                ],
            ],
        ],
        'down' => [
            'drop_table'   => [
                'goal_change_logs',
                'goal_clear_evaluates',
                'goal_labels',
                'labels',
                'tkr_change_logs'
            ],
            'create_field' => [
                'goals' => [
                    'purpose_id'    => [
                        'type'     => 'biginteger',
                        'null'     => false,
                        'default'  => null,
                        'unsigned' => true,
                        'key'      => 'index',
                        'comment'  => '目的ID(belongsToでTeamモデルに関連)'
                    ],
                    'current_value' => [
                        'type'     => 'decimal',
                        'null'     => false,
                        'default'  => '0.000',
                        'length'   => '18,3',
                        'unsigned' => false,
                        'comment'  => '現在値'
                    ],
                    'start_value'   => [
                        'type'     => 'decimal',
                        'null'     => false,
                        'default'  => '0.000',
                        'length'   => '18,3',
                        'unsigned' => false,
                        'comment'  => '開始値'
                    ],
                    'target_value'  => [
                        'type'     => 'decimal',
                        'null'     => false,
                        'default'  => '0.000',
                        'length'   => '18,3',
                        'unsigned' => false,
                        'comment'  => '目標値'
                    ],
                    'value_unit'    => [
                        'type'     => 'integer',
                        'null'     => false,
                        'default'  => '0',
                        'unsigned' => true,
                        'comment'  => '目標値の単位'
                    ],
                    'indexes'       => [
                        'purpose_id' => ['column' => 'purpose_id', 'unique' => 0],
                    ],
                ],
            ],
            'drop_field'   => [
                'key_results' => ['tkr_flg'],
            ],
        ],
    ];

    /**
     * Before migration callback
     *
     * @param string $direction Direction of migration process (up or down)
     *
     * @return bool Should process continue
     */
    public function before($direction)
    {
        if ($direction == 'up') {
            /**
             * ゴールをtKRとして保存する。
             * 既存のプライオリティ5のKRは4に変更。
             * 完了済みのゴールはtKRも完了済みとする。
             *
             * @var Goal $Goal
             */
            $Goal = ClassRegistry::init('Goal');
            $goals = $Goal->findWithoutTeamId('all', [
                'fields'  => [
                    'Goal.id',
                    'Goal.team_id',
                    'Goal.user_id',
                    'Goal.name',
                    'Goal.progress',
                    'Goal.completed',
                    'Goal.start_value',
                    'Goal.target_value',
                    'Goal.current_value',
                    'Goal.value_unit',
                    'Goal.start_date',
                    'Goal.end_date',
                ],
                'contain' => [
                    'KeyResult' => [
                        'conditions' => ['KeyResult.priority' => 5],
                        'fields'     => [
                            'KeyResult.id',
                            'KeyResult.priority',
                        ]
                    ]
                ]
            ]);
            $save_krs = [];
            foreach ($goals as $goal) {
                $goal['Goal']['goal_id'] = $goal['Goal']['id'];
                $goal['Goal']['priority'] = 5;
                if ($goal['Goal']['progress'] == 100) {
                    $goal['Goal']['current_value'] = $goal['Goal']['target_value'];
                }
                unset($goal['Goal']['id']);

                $save_krs[] = $goal['Goal'];
                foreach ($goal['KeyResult'] as $kr) {
                    $kr['priority'] = 4;
                    $save_krs[] = $kr;
                }
            }
            $Goal->KeyResult->saveAll($save_krs);
        }
        return true;
    }

    /**
     * After migration callback
     *
     * @param string $direction Direction of migration process (up or down)
     *
     * @return bool Should process continue
     */
    public function after($direction)
    {
        if ($direction == 'up') {
            /**
             * tKRはtkr_flgに1をセット。
             * tKRの条件はpriority=5
             *
             * @var KeyResult $KeyResult
             */
            $KeyResult = ClassRegistry::init('KeyResult');
            $krs = $KeyResult->findWithoutTeamId('all', [
                'conditions' => [
                    'KeyResult.priority' => 5,
                ],
                'fields'     => [
                    'KeyResult.id',
                    'KeyResult.priority',
                ],
            ]);
            foreach ($krs as $k => $v) {
                $krs[$k]['KeyResult']['tkr_flg'] = true;
            }
            $KeyResult->saveAll($krs);
        }
        return true;
    }
}
