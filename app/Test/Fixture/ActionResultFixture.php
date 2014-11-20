<?php

/**
 * ActionResultFixture

 */
class ActionResultFixture extends CakeTestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = array(
        'id'              => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'アクションリザルトID'),
        'team_id'         => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
        'action_id'       => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'アクションID(belongsToでGoalモデルに関連)'),
        'user_id'         => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '作成者ID(belongsToでUserモデルに関連)'),
        'scheduled'       => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '予定日'),
        'completed'       => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '完了日'),
        'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => '削除フラグ'),
        'deleted'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '削除した日付時刻'),
        'created'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '追加した日付時刻'),
        'modified'        => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '更新した日付時刻'),
        'indexes'         => array(
            'PRIMARY'   => array('column' => 'id', 'unique' => 1),
            'team_id'   => array('column' => 'team_id', 'unique' => 0),
            'del_flg'   => array('column' => 'del_flg', 'unique' => 0),
            'action_id' => array('column' => 'action_id', 'unique' => 0),
            'modified'  => array('column' => 'modified', 'unique' => 0),
            'user_id'   => array('column' => 'user_id', 'unique' => 0)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    /**
     * Records
     *
     * @var array
     */
    public $records = array(
        array(
            'id'        => '',
            'team_id'   => '',
            'action_id' => '',
            'user_id'   => '',
            'scheduled' => 1,
            'completed' => 1,
            'del_flg'   => 1,
            'deleted'   => 1,
            'created'   => 1,
            'modified'  => 1
        ),
    );

}
