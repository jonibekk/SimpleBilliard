<?php

/**
 * CommentReadFixture

 */
class CommentReadFixture extends CakeTestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = array(
        'id'              => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'comment' => 'コメント読んだID', 'charset' => 'utf8'),
        'comment_id'      => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'コメントID(belongsToでcommentモデルに関連)', 'charset' => 'utf8'),
        'user_id'         => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => '読んだしたユーザID(belongsToでUserモデルに関連)', 'charset' => 'utf8'),
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
            'id'         => '537ce223-3a74-4b71-aad7-433dac11b50b',
            'comment_id' => 'Lorem ipsum dolor sit amet',
            'user_id'    => 'Lorem ipsum dolor sit amet',
            'team_id'    => 'Lorem ipsum dolor sit amet',
            'del_flg'    => 1,
            'deleted'    => '2014-05-22 02:28:03',
            'created'    => '2014-05-22 02:28:03',
            'modified'   => '2014-05-22 02:28:03'
        ),
        array(
            'id'         => '537ce223-5d38-4623-9d55-433dac11b50b',
            'comment_id' => 'Lorem ipsum dolor sit amet',
            'user_id'    => 'Lorem ipsum dolor sit amet',
            'team_id'    => 'Lorem ipsum dolor sit amet',
            'del_flg'    => 1,
            'deleted'    => '2014-05-22 02:28:03',
            'created'    => '2014-05-22 02:28:03',
            'modified'   => '2014-05-22 02:28:03'
        ),
        array(
            'id'         => '537ce223-7638-4510-a79b-433dac11b50b',
            'comment_id' => 'Lorem ipsum dolor sit amet',
            'user_id'    => 'Lorem ipsum dolor sit amet',
            'team_id'    => 'Lorem ipsum dolor sit amet',
            'del_flg'    => 1,
            'deleted'    => '2014-05-22 02:28:03',
            'created'    => '2014-05-22 02:28:03',
            'modified'   => '2014-05-22 02:28:03'
        ),
        array(
            'id'         => '537ce223-8e0c-48ea-8e49-433dac11b50b',
            'comment_id' => 'Lorem ipsum dolor sit amet',
            'user_id'    => 'Lorem ipsum dolor sit amet',
            'team_id'    => 'Lorem ipsum dolor sit amet',
            'del_flg'    => 1,
            'deleted'    => '2014-05-22 02:28:03',
            'created'    => '2014-05-22 02:28:03',
            'modified'   => '2014-05-22 02:28:03'
        ),
        array(
            'id'         => '537ce223-aa2c-4700-b768-433dac11b50b',
            'comment_id' => 'Lorem ipsum dolor sit amet',
            'user_id'    => 'Lorem ipsum dolor sit amet',
            'team_id'    => 'Lorem ipsum dolor sit amet',
            'del_flg'    => 1,
            'deleted'    => '2014-05-22 02:28:03',
            'created'    => '2014-05-22 02:28:03',
            'modified'   => '2014-05-22 02:28:03'
        ),
        array(
            'id'         => '537ce223-c264-4677-99be-433dac11b50b',
            'comment_id' => 'Lorem ipsum dolor sit amet',
            'user_id'    => 'Lorem ipsum dolor sit amet',
            'team_id'    => 'Lorem ipsum dolor sit amet',
            'del_flg'    => 1,
            'deleted'    => '2014-05-22 02:28:03',
            'created'    => '2014-05-22 02:28:03',
            'modified'   => '2014-05-22 02:28:03'
        ),
        array(
            'id'         => '537ce223-de20-4b0a-a3c8-433dac11b50b',
            'comment_id' => 'Lorem ipsum dolor sit amet',
            'user_id'    => 'Lorem ipsum dolor sit amet',
            'team_id'    => 'Lorem ipsum dolor sit amet',
            'del_flg'    => 1,
            'deleted'    => '2014-05-22 02:28:03',
            'created'    => '2014-05-22 02:28:03',
            'modified'   => '2014-05-22 02:28:03'
        ),
        array(
            'id'         => '537ce223-f9dc-4b7a-b8ee-433dac11b50b',
            'comment_id' => 'Lorem ipsum dolor sit amet',
            'user_id'    => 'Lorem ipsum dolor sit amet',
            'team_id'    => 'Lorem ipsum dolor sit amet',
            'del_flg'    => 1,
            'deleted'    => '2014-05-22 02:28:03',
            'created'    => '2014-05-22 02:28:03',
            'modified'   => '2014-05-22 02:28:03'
        ),
        array(
            'id'         => '537ce223-1278-4694-ac48-433dac11b50b',
            'comment_id' => 'Lorem ipsum dolor sit amet',
            'user_id'    => 'Lorem ipsum dolor sit amet',
            'team_id'    => 'Lorem ipsum dolor sit amet',
            'del_flg'    => 1,
            'deleted'    => '2014-05-22 02:28:03',
            'created'    => '2014-05-22 02:28:03',
            'modified'   => '2014-05-22 02:28:03'
        ),
        array(
            'id'         => '537ce223-2ab0-44b5-82a3-433dac11b50b',
            'comment_id' => 'Lorem ipsum dolor sit amet',
            'user_id'    => 'Lorem ipsum dolor sit amet',
            'team_id'    => 'Lorem ipsum dolor sit amet',
            'del_flg'    => 1,
            'deleted'    => '2014-05-22 02:28:03',
            'created'    => '2014-05-22 02:28:03',
            'modified'   => '2014-05-22 02:28:03'
        ),
    );

}
