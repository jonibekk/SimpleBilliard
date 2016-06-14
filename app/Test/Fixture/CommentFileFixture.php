<?php

/**
 * CommentFileFixture
 */
class CommentFileFixture extends CakeTestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = array(
        'id'               => array('type' => 'biginteger', 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'ID'),
        'comment_id'       => array('type' => 'biginteger', 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'コメントID(belongsToでCommentモデルに関連)'),
        'attached_file_id' => array('type' => 'biginteger', 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'ファイルID(belongsToでFileモデルに関連)'),
        'team_id'          => array('type' => 'biginteger', 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
        'index_num'        => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => '表示順'),
        'del_flg'          => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
        'deleted'          => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '削除した日付時刻'),
        'created'          => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '追加した日付時刻'),
        'modified'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '更新した日付時刻'),
        'indexes'          => array(
            'PRIMARY'          => array('column' => 'id', 'unique' => 1),
            'comment_id'       => array('column' => 'comment_id', 'unique' => 0),
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
    public $records = array(
        [
            'id'               => 1,
            'comment_id'       => 1,
            'attached_file_id' => 3,
            'team_id'          => 1,
            'index_num'        => 0,
            'del_flg'          => 0,
            'deleted'          => null,
            'created'          => 1,
            'modified'         => 1,
        ],
        [
            'id'               => 2,
            'comment_id'       => 2,
            'attached_file_id' => 4,
            'team_id'          => 1,
            'index_num'        => 0,
            'del_flg'          => 0,
            'deleted'          => null,
            'created'          => 1,
            'modified'         => 1,
        ],
    );

}
