<?php

/**
 * CommentLikeFixture

 */
class CommentLikeFixture extends CakeTestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = array(
        'id'              => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'comment' => 'コメントいいねID', 'charset' => 'utf8'),
        'comment_id'      => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'コメントID(belongsToでcommentモデルに関連)', 'charset' => 'utf8'),
        'user_id'         => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'いいねしたユーザID(belongsToでUserモデルに関連)', 'charset' => 'utf8'),
        'team_id'         => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'チームID(belongsToでTeamモデルに関連)', 'charset' => 'utf8'),
        'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
        'deleted'         => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'コメントを削除した日付時刻'),
        'created'         => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'コメントを追加した日付時刻'),
        'modified'        => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'コメントを更新した日付時刻'),
        'indexes'         => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
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
            'id'         => '537ce222-f1d0-4ed4-828f-433dac11b50b',
            'comment_id' => 'Lorem ipsum dolor sit amet',
            'user_id'    => 'Lorem ipsum dolor sit amet',
            'team_id'    => 'Lorem ipsum dolor sit amet',
            'del_flg'    => 1,
            'deleted'    => '2014-05-22 02:28:02',
            'created'    => '2014-05-22 02:28:02',
            'modified'   => '2014-05-22 02:28:02'
        ),
        array(
            'id'         => '537ce222-12a0-49a6-96e1-433dac11b50b',
            'comment_id' => 'Lorem ipsum dolor sit amet',
            'user_id'    => 'Lorem ipsum dolor sit amet',
            'team_id'    => 'Lorem ipsum dolor sit amet',
            'del_flg'    => 1,
            'deleted'    => '2014-05-22 02:28:02',
            'created'    => '2014-05-22 02:28:02',
            'modified'   => '2014-05-22 02:28:02'
        ),
        array(
            'id'         => '537ce222-2ec0-4a93-a94a-433dac11b50b',
            'comment_id' => 'Lorem ipsum dolor sit amet',
            'user_id'    => 'Lorem ipsum dolor sit amet',
            'team_id'    => 'Lorem ipsum dolor sit amet',
            'del_flg'    => 1,
            'deleted'    => '2014-05-22 02:28:02',
            'created'    => '2014-05-22 02:28:02',
            'modified'   => '2014-05-22 02:28:02'
        ),
        array(
            'id'         => '537ce222-45cc-4391-b6f9-433dac11b50b',
            'comment_id' => 'Lorem ipsum dolor sit amet',
            'user_id'    => 'Lorem ipsum dolor sit amet',
            'team_id'    => 'Lorem ipsum dolor sit amet',
            'del_flg'    => 1,
            'deleted'    => '2014-05-22 02:28:02',
            'created'    => '2014-05-22 02:28:02',
            'modified'   => '2014-05-22 02:28:02'
        ),
        array(
            'id'         => '537ce222-5c74-4eaa-b21b-433dac11b50b',
            'comment_id' => 'Lorem ipsum dolor sit amet',
            'user_id'    => 'Lorem ipsum dolor sit amet',
            'team_id'    => 'Lorem ipsum dolor sit amet',
            'del_flg'    => 1,
            'deleted'    => '2014-05-22 02:28:02',
            'created'    => '2014-05-22 02:28:02',
            'modified'   => '2014-05-22 02:28:02'
        ),
        array(
            'id'         => '537ce222-7830-4dc4-9509-433dac11b50b',
            'comment_id' => 'Lorem ipsum dolor sit amet',
            'user_id'    => 'Lorem ipsum dolor sit amet',
            'team_id'    => 'Lorem ipsum dolor sit amet',
            'del_flg'    => 1,
            'deleted'    => '2014-05-22 02:28:02',
            'created'    => '2014-05-22 02:28:02',
            'modified'   => '2014-05-22 02:28:02'
        ),
        array(
            'id'         => '537ce222-8fa0-4f76-b674-433dac11b50b',
            'comment_id' => 'Lorem ipsum dolor sit amet',
            'user_id'    => 'Lorem ipsum dolor sit amet',
            'team_id'    => 'Lorem ipsum dolor sit amet',
            'del_flg'    => 1,
            'deleted'    => '2014-05-22 02:28:02',
            'created'    => '2014-05-22 02:28:02',
            'modified'   => '2014-05-22 02:28:02'
        ),
        array(
            'id'         => '537ce222-a710-49e6-a543-433dac11b50b',
            'comment_id' => 'Lorem ipsum dolor sit amet',
            'user_id'    => 'Lorem ipsum dolor sit amet',
            'team_id'    => 'Lorem ipsum dolor sit amet',
            'del_flg'    => 1,
            'deleted'    => '2014-05-22 02:28:02',
            'created'    => '2014-05-22 02:28:02',
            'modified'   => '2014-05-22 02:28:02'
        ),
        array(
            'id'         => '537ce222-be80-4fcf-b417-433dac11b50b',
            'comment_id' => 'Lorem ipsum dolor sit amet',
            'user_id'    => 'Lorem ipsum dolor sit amet',
            'team_id'    => 'Lorem ipsum dolor sit amet',
            'del_flg'    => 1,
            'deleted'    => '2014-05-22 02:28:02',
            'created'    => '2014-05-22 02:28:02',
            'modified'   => '2014-05-22 02:28:02'
        ),
        array(
            'id'         => '537ce222-d654-453b-869a-433dac11b50b',
            'comment_id' => 'Lorem ipsum dolor sit amet',
            'user_id'    => 'Lorem ipsum dolor sit amet',
            'team_id'    => 'Lorem ipsum dolor sit amet',
            'del_flg'    => 1,
            'deleted'    => '2014-05-22 02:28:02',
            'created'    => '2014-05-22 02:28:02',
            'modified'   => '2014-05-22 02:28:02'
        ),
    );

}
