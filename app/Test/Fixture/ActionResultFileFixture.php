<?php App::uses('CakeTestFixtureEx', 'Test/Fixture');

/**
 * ActionResultFileFixture
 */
class ActionResultFileFixture extends CakeTestFixtureEx
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = array(
        'id'               => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'ID'),
        'action_result_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'アクションID(belongsToでActionResultモデルに関連)'),
        'attached_file_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'ファイルID(belongsToでFileモデルに関連)'),
        'team_id'          => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
        'index_num'        => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => '表示順'),
        'del_flg'          => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
        'deleted'          => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '削除した日付時刻'),
        'created'          => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '追加した日付時刻'),
        'modified'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '更新した日付時刻'),
        'indexes'          => array(
            'PRIMARY'          => array('column' => 'id', 'unique' => 1),
            'action_result_id' => array('column' => 'action_result_id', 'unique' => 0),
            'team_id'          => array('column' => 'team_id', 'unique' => 0),
            'attached_file_id' => array('column' => 'attached_file_id', 'unique' => 0)
        ),
        'tableParameters'  => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    /**
     * Records
     *
     * @var array
     */
    public $records = [
        [
            'id'               => 1,
            'action_result_id' => 1,
            'attached_file_id' => 2,
            'team_id'          => 1,
            'index_num'        => 0,
            'del_flg'          => 0,
            'deleted'          => null,
            'created'          => 1,
            'modified'         => 1,
        ]
    ];

}
