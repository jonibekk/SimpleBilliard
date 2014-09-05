<?php

class AddKeyResultsTable0828 extends CakeMigration
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
                'key_results' => array(
                    'id'              => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'キーリザルトID'),
                    'team_id'         => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
                    'goal_id'         => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'ゴールID(belongsToでGoalモデルに関連)'),
                    'name'            => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '名前', 'charset' => 'utf8'),
                    'description'     => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '説明', 'charset' => 'utf8'),
                    'start_date'      => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '開始日'),
                    'due_date'        => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '期限(unixtime)'),
                    'value'           => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '目標値'),
                    'value_unit'      => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '目標値の単位'),
                    'compleated'      => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '完了日'),
                    'special_flg'     => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '特別フラグ'),
                    'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => '削除フラグ'),
                    'deleted'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'ゴールカテゴリを削除した日付時刻'),
                    'created'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'ゴールカテゴリを追加した日付時刻'),
                    'modified'        => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'ゴールカテゴリを更新した日付時刻'),
                    'indexes'         => array(
                        'PRIMARY'  => array('column' => 'id', 'unique' => 1),
                        'team_id'  => array('column' => 'team_id', 'unique' => 0),
                        'del_flg'  => array('column' => 'del_flg', 'unique' => 0),
                        'goal_id'  => array('column' => 'goal_id', 'unique' => 0),
                        'modified' => array('column' => 'modified', 'unique' => 0),
                    ),
                    'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
                ),
            ),
        ),
        'down' => array(
            'drop_table' => array(
                'key_results'
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
