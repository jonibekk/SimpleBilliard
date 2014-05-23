<?php

/**
 * PostLikeFixture

 */
class PostLikeFixture extends CakeTestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = array(
        'id'              => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'comment' => '投稿いいねID', 'charset' => 'utf8'),
        'post_id'         => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => '投稿ID(belongsToでPostモデルに関連)', 'charset' => 'utf8'),
        'user_id'         => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'いいねしたユーザID(belongsToでUserモデルに関連)', 'charset' => 'utf8'),
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
            'id'       => '537ce224-8f20-436f-906e-433dac11b50b',
            'post_id'  => 'Lorem ipsum dolor sit amet',
            'user_id'  => 'Lorem ipsum dolor sit amet',
            'team_id'  => 'Lorem ipsum dolor sit amet',
            'del_flg'  => 1,
            'deleted'  => '2014-05-22 02:28:04',
            'created'  => '2014-05-22 02:28:04',
            'modified' => '2014-05-22 02:28:04'
        ),
        array(
            'id'       => '537ce224-b504-4825-ac68-433dac11b50b',
            'post_id'  => 'Lorem ipsum dolor sit amet',
            'user_id'  => 'Lorem ipsum dolor sit amet',
            'team_id'  => 'Lorem ipsum dolor sit amet',
            'del_flg'  => 1,
            'deleted'  => '2014-05-22 02:28:04',
            'created'  => '2014-05-22 02:28:04',
            'modified' => '2014-05-22 02:28:04'
        ),
        array(
            'id'       => '537ce224-cecc-49d2-bea4-433dac11b50b',
            'post_id'  => 'Lorem ipsum dolor sit amet',
            'user_id'  => 'Lorem ipsum dolor sit amet',
            'team_id'  => 'Lorem ipsum dolor sit amet',
            'del_flg'  => 1,
            'deleted'  => '2014-05-22 02:28:04',
            'created'  => '2014-05-22 02:28:04',
            'modified' => '2014-05-22 02:28:04'
        ),
        array(
            'id'       => '537ce224-e7cc-4ea2-9fb6-433dac11b50b',
            'post_id'  => 'Lorem ipsum dolor sit amet',
            'user_id'  => 'Lorem ipsum dolor sit amet',
            'team_id'  => 'Lorem ipsum dolor sit amet',
            'del_flg'  => 1,
            'deleted'  => '2014-05-22 02:28:04',
            'created'  => '2014-05-22 02:28:04',
            'modified' => '2014-05-22 02:28:04'
        ),
        array(
            'id'       => '537ce224-0004-4d49-a078-433dac11b50b',
            'post_id'  => 'Lorem ipsum dolor sit amet',
            'user_id'  => 'Lorem ipsum dolor sit amet',
            'team_id'  => 'Lorem ipsum dolor sit amet',
            'del_flg'  => 1,
            'deleted'  => '2014-05-22 02:28:04',
            'created'  => '2014-05-22 02:28:04',
            'modified' => '2014-05-22 02:28:04'
        ),
        array(
            'id'       => '537ce224-183c-48fb-94a2-433dac11b50b',
            'post_id'  => 'Lorem ipsum dolor sit amet',
            'user_id'  => 'Lorem ipsum dolor sit amet',
            'team_id'  => 'Lorem ipsum dolor sit amet',
            'del_flg'  => 1,
            'deleted'  => '2014-05-22 02:28:04',
            'created'  => '2014-05-22 02:28:04',
            'modified' => '2014-05-22 02:28:04'
        ),
        array(
            'id'       => '537ce224-313c-42c8-aee6-433dac11b50b',
            'post_id'  => 'Lorem ipsum dolor sit amet',
            'user_id'  => 'Lorem ipsum dolor sit amet',
            'team_id'  => 'Lorem ipsum dolor sit amet',
            'del_flg'  => 1,
            'deleted'  => '2014-05-22 02:28:04',
            'created'  => '2014-05-22 02:28:04',
            'modified' => '2014-05-22 02:28:04'
        ),
        array(
            'id'       => '537ce224-4a3c-4841-9702-433dac11b50b',
            'post_id'  => 'Lorem ipsum dolor sit amet',
            'user_id'  => 'Lorem ipsum dolor sit amet',
            'team_id'  => 'Lorem ipsum dolor sit amet',
            'del_flg'  => 1,
            'deleted'  => '2014-05-22 02:28:04',
            'created'  => '2014-05-22 02:28:04',
            'modified' => '2014-05-22 02:28:04'
        ),
        array(
            'id'       => '537ce224-62d8-4ae4-bbc5-433dac11b50b',
            'post_id'  => 'Lorem ipsum dolor sit amet',
            'user_id'  => 'Lorem ipsum dolor sit amet',
            'team_id'  => 'Lorem ipsum dolor sit amet',
            'del_flg'  => 1,
            'deleted'  => '2014-05-22 02:28:04',
            'created'  => '2014-05-22 02:28:04',
            'modified' => '2014-05-22 02:28:04'
        ),
        array(
            'id'       => '537ce224-7b74-4d00-8ac9-433dac11b50b',
            'post_id'  => 'Lorem ipsum dolor sit amet',
            'user_id'  => 'Lorem ipsum dolor sit amet',
            'team_id'  => 'Lorem ipsum dolor sit amet',
            'del_flg'  => 1,
            'deleted'  => '2014-05-22 02:28:04',
            'created'  => '2014-05-22 02:28:04',
            'modified' => '2014-05-22 02:28:04'
        ),
    );

}
