<?php App::uses('CakeTestFixtureEx', 'Test/Fixture');

/**
 * PostFileFixture
 */
class PostFileFixture extends CakeTestFixtureEx
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = array(
        'id'               => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'ID'),
        'post_id'          => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '投稿ID(belongsToでPostモデルに関連)'),
        'attached_file_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'ファイルID(belongsToでFileモデルに関連)'),
        'team_id'          => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
        'index_num'        => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => '表示順'),
        'del_flg'          => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
        'deleted'          => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '削除した日付時刻'),
        'created'          => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '追加した日付時刻'),
        'modified'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '更新した日付時刻'),
        'indexes'          => array(
            'PRIMARY'          => array('column' => 'id', 'unique' => 1),
            'post_id'          => array('column' => 'post_id', 'unique' => 0),
            'team_id'          => array('column' => 'team_id', 'unique' => 0),
            'attached_file_id' => array('column' => 'attached_file_id', 'unique' => 0)
        ),
        'tableParameters'  => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
    );

    /**
     * Records
     *
     * @var array
     */
    public $records = array(
        [
            'id'               => 1,
            'post_id'          => 7,
            'attached_file_id' => 1,
            'team_id'          => 1,
            'index_num'        => 0,
            'del_flg'          => 0,
            'deleted'          => null,
            'created'          => 1,
            'modified'         => 1,
        ],
        [
            'id'               => 2,
            'post_id'          => 6,
            'attached_file_id' => 5,
            'team_id'          => 1,
            'index_num'        => 0,
            'del_flg'          => 0,
            'deleted'          => null,
            'created'          => 1,
            'modified'         => 1,
        ],
        [
            'id'               => 3,
            'post_id'          => 10,
            'attached_file_id' => 6,
            'team_id'          => 1,
            'index_num'        => 0,
            'del_flg'          => 0,
            'deleted'          => null,
            'created'          => 1,
            'modified'         => 1,
        ],
        [
            'id'               => 4,
            'post_id'          => 11,
            'attached_file_id' => 7,
            'team_id'          => 1,
            'index_num'        => 0,
            'del_flg'          => 0,
            'deleted'          => null,
            'created'          => 1,
            'modified'         => 1,
        ],
        [
            'id'               => 5,
            'post_id'          => 999,
            'attached_file_id' => 8,
            'team_id'          => 1,
            'index_num'        => 0,
            'del_flg'          => 0,
            'deleted'          => null,
            'created'          => 1,
            'modified'         => 1,
        ],
    );

}
