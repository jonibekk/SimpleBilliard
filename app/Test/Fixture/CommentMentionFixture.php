<?php

/**
 * CommentMentionFixture

 */
class CommentMentionFixture extends CakeTestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = array(
        'id'              => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'comment' => 'コメントメンションID', 'charset' => 'utf8'),
        'post_id'         => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => '投稿ID(belongsToでPostモデルに関連)', 'charset' => 'utf8'),
        'user_id'         => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'メンションユーザID(belongsToでUserモデルに関連)', 'charset' => 'utf8'),
        'team_id'         => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'チームID(belongsToでTeamモデルに関連)', 'charset' => 'utf8'),
        'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
        'deleted'         => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '投稿を削除した日付時刻'),
        'created'         => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '投稿を追加した日付時刻'),
        'modified'        => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '投稿を更新した日付時刻'),
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
            'id'       => '537ce223-a218-490d-9262-433dac11b50b',
            'post_id'  => 'Lorem ipsum dolor sit amet',
            'user_id'  => 'Lorem ipsum dolor sit amet',
            'team_id'  => 'Lorem ipsum dolor sit amet',
            'del_flg'  => 1,
            'deleted'  => '2014-05-22 02:28:03',
            'created'  => '2014-05-22 02:28:03',
            'modified' => '2014-05-22 02:28:03'
        ),
        array(
            'id'       => '537ce223-c34c-41fb-b72f-433dac11b50b',
            'post_id'  => 'Lorem ipsum dolor sit amet',
            'user_id'  => 'Lorem ipsum dolor sit amet',
            'team_id'  => 'Lorem ipsum dolor sit amet',
            'del_flg'  => 1,
            'deleted'  => '2014-05-22 02:28:03',
            'created'  => '2014-05-22 02:28:03',
            'modified' => '2014-05-22 02:28:03'
        ),
        array(
            'id'       => '537ce223-da58-4d1a-9374-433dac11b50b',
            'post_id'  => 'Lorem ipsum dolor sit amet',
            'user_id'  => 'Lorem ipsum dolor sit amet',
            'team_id'  => 'Lorem ipsum dolor sit amet',
            'del_flg'  => 1,
            'deleted'  => '2014-05-22 02:28:03',
            'created'  => '2014-05-22 02:28:03',
            'modified' => '2014-05-22 02:28:03'
        ),
        array(
            'id'       => '537ce223-f09c-4dee-940a-433dac11b50b',
            'post_id'  => 'Lorem ipsum dolor sit amet',
            'user_id'  => 'Lorem ipsum dolor sit amet',
            'team_id'  => 'Lorem ipsum dolor sit amet',
            'del_flg'  => 1,
            'deleted'  => '2014-05-22 02:28:03',
            'created'  => '2014-05-22 02:28:03',
            'modified' => '2014-05-22 02:28:03'
        ),
        array(
            'id'       => '537ce223-06e0-4fb4-a7db-433dac11b50b',
            'post_id'  => 'Lorem ipsum dolor sit amet',
            'user_id'  => 'Lorem ipsum dolor sit amet',
            'team_id'  => 'Lorem ipsum dolor sit amet',
            'del_flg'  => 1,
            'deleted'  => '2014-05-22 02:28:03',
            'created'  => '2014-05-22 02:28:03',
            'modified' => '2014-05-22 02:28:03'
        ),
        array(
            'id'       => '537ce223-1d24-4562-8ce1-433dac11b50b',
            'post_id'  => 'Lorem ipsum dolor sit amet',
            'user_id'  => 'Lorem ipsum dolor sit amet',
            'team_id'  => 'Lorem ipsum dolor sit amet',
            'del_flg'  => 1,
            'deleted'  => '2014-05-22 02:28:03',
            'created'  => '2014-05-22 02:28:03',
            'modified' => '2014-05-22 02:28:03'
        ),
        array(
            'id'       => '537ce223-33cc-486a-bb46-433dac11b50b',
            'post_id'  => 'Lorem ipsum dolor sit amet',
            'user_id'  => 'Lorem ipsum dolor sit amet',
            'team_id'  => 'Lorem ipsum dolor sit amet',
            'del_flg'  => 1,
            'deleted'  => '2014-05-22 02:28:03',
            'created'  => '2014-05-22 02:28:03',
            'modified' => '2014-05-22 02:28:03'
        ),
        array(
            'id'       => '537ce223-4a10-498f-803f-433dac11b50b',
            'post_id'  => 'Lorem ipsum dolor sit amet',
            'user_id'  => 'Lorem ipsum dolor sit amet',
            'team_id'  => 'Lorem ipsum dolor sit amet',
            'del_flg'  => 1,
            'deleted'  => '2014-05-22 02:28:03',
            'created'  => '2014-05-22 02:28:03',
            'modified' => '2014-05-22 02:28:03'
        ),
        array(
            'id'       => '537ce223-6054-40ae-b1cf-433dac11b50b',
            'post_id'  => 'Lorem ipsum dolor sit amet',
            'user_id'  => 'Lorem ipsum dolor sit amet',
            'team_id'  => 'Lorem ipsum dolor sit amet',
            'del_flg'  => 1,
            'deleted'  => '2014-05-22 02:28:03',
            'created'  => '2014-05-22 02:28:03',
            'modified' => '2014-05-22 02:28:03'
        ),
        array(
            'id'       => '537ce223-7698-4955-bca1-433dac11b50b',
            'post_id'  => 'Lorem ipsum dolor sit amet',
            'user_id'  => 'Lorem ipsum dolor sit amet',
            'team_id'  => 'Lorem ipsum dolor sit amet',
            'del_flg'  => 1,
            'deleted'  => '2014-05-22 02:28:03',
            'created'  => '2014-05-22 02:28:03',
            'modified' => '2014-05-22 02:28:03'
        ),
    );

}
