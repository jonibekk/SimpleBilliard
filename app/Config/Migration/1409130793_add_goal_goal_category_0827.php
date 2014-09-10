<?php

class AddGoalGoalCategory0827 extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = '';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up'   => array(
            'create_table' => array(
                'goal_categories' => array(
                    'id'              => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'ゴールカテゴリID'),
                    'team_id'         => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
                    'name'            => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '名前', 'charset' => 'utf8'),
                    'description'     => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '説明', 'charset' => 'utf8'),
                    'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => '削除フラグ'),
                    'deleted'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'ゴールカテゴリを削除した日付時刻'),
                    'created'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'ゴールカテゴリを追加した日付時刻'),
                    'modified'        => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'ゴールカテゴリを更新した日付時刻'),
                    'indexes'         => array(
                        'PRIMARY' => array('column' => 'id', 'unique' => 1),
                        'team_id' => array('column' => 'team_id', 'unique' => 0),
                        'del_flg' => array('column' => 'del_flg', 'unique' => 0),
                    ),
                    'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
                ),
                'goals'           => array(
                    'id'               => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'ゴールID'),
                    'user_id'          => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'ユーザID(belongsToでUserモデルに関連)'),
                    'team_id'          => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
                    'goal_category_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'ゴールカテゴリ'),
                    'goal'             => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '目標', 'charset' => 'utf8'),
                    'purpose'          => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '目的', 'charset' => 'utf8'),
                    'due_date'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '期限(unixtime)'),
                    'goal_value'       => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '目標値'),
                    'goal_value_unit'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '目標値の単位'),
                    'valued_flg'       => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '価値フラグ'),
                    'evaluate_flg'     => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '評価フラグ'),
                    'status'           => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => 'ステータス(0 = 進行中, 1 = 中断, 2 = 完了)'),
                    'priority'         => array('type' => 'integer', 'null' => false, 'default' => '3', 'unsigned' => false, 'comment' => '重要度(1〜5)'),
                    'description'      => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '説明', 'charset' => 'utf8'),
                    'start_date'       => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '開始日'),
                    'compleated'       => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '完了日'),
                    'del_flg'          => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => '削除フラグ'),
                    'deleted'          => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'ゴールを削除した日付時刻'),
                    'created'          => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'ゴールを追加した日付時刻'),
                    'modified'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'ゴールを更新した日付時刻'),
                    'indexes'          => array(
                        'PRIMARY'  => array('column' => 'id', 'unique' => 1),
                        'modified' => array('column' => 'modified', 'unique' => 0),
                        'user_id'  => array('column' => 'user_id', 'unique' => 0),
                        'team_id'  => array('column' => 'team_id', 'unique' => 0),
                        'del_flg'  => array('column' => 'del_flg', 'unique' => 0),
                    ),
                    'tableParameters'  => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
                ),
            ),
        ),
        'down' => array(
            'drop_table' => array(
                'goal_categories', 'goals'
            ),
        ),
    );

    /**
     * Before migration callback
     *
     * @param string $direction , up or down direction of migration process
     *
     * @return boolean Should process continue
     */
    public function before($direction)
    {
        return true;
    }

    /**
     * After migration callback
     *
     * @param string $direction , up or down direction of migration process
     *
     * @return boolean Should process continue
     */
    public function after($direction)
    {
        return true;
    }
}