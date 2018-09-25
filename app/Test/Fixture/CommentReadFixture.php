<?php App::uses('CakeTestFixtureEx', 'Test/Fixture');

/**
 * CommentReadFixture
 */
class CommentReadFixture extends CakeTestFixtureEx
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = array(
        'id'              => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'primary',
            'comment'  => 'コメント読んだID'
        ),
        'comment_id'      => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => 'コメントID(belongsToでcommentモデルに関連)'
        ),
        'user_id'         => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => '読んだしたユーザID(belongsToでUserモデルに関連)'
        ),
        'team_id'         => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => 'チームID(belongsToでTeamモデルに関連)'
        ),
        'del_flg'         => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '0',
            'key'     => 'index',
            'comment' => '削除フラグ'
        ),
        'deleted'         => array(
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => 'コメントを削除した日付時刻'
        ),
        'created'         => array(
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => 'コメントを追加した日付時刻'
        ),
        'modified'        => array(
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => 'コメントを更新した日付時刻'
        ),
        'indexes'         => array(
            'PRIMARY'      => array('column' => 'id', 'unique' => 1),
            'comment_id'   => array('column' => 'comment_id', 'unique' => 0),
            'comment_id_2' => array('column' => ['comment_id', 'user_id'], 'unique' => 1),
            'user_id'      => array('column' => 'user_id', 'unique' => 0),
            'team_id'      => array('column' => 'team_id', 'unique' => 0),
            'del_flg'      => array('column' => 'del_flg', 'unique' => 0)
        ),
        'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
    );

    /**
     * Records
     *
     * @var array
     */
    public $records = array(
        array(
            'id'            => 1,
            'comment_id'    => 1,
            'user_id'       => 2,
            'team_id'       => 1,
            'del_flg'       => 0,
            'created'       => 1,
            'modified'      => 1
        ),
		array(
			'id'            => 2,
			'comment_id'    => 1,
			'user_id'       => 13,
			'team_id'       => 1,
			'del_flg'       => 0,
			'created'       => 1,
			'modified'      => 1
		),
    );

}
