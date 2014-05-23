<?php

/**
 * PostReadFixture

 */
class PostReadFixture extends CakeTestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = array(
        'id'              => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'comment' => '投稿読んだID', 'charset' => 'utf8'),
        'post_id'         => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => '投稿ID(belongsToでPostモデルに関連)', 'charset' => 'utf8'),
        'user_id'         => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => '読んだしたユーザID(belongsToでUserモデルに関連)', 'charset' => 'utf8'),
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
            'id'       => '537ce224-27f4-4412-a9a2-433dac11b50b',
            'post_id'  => 'Lorem ipsum dolor sit amet',
            'user_id'  => 'Lorem ipsum dolor sit amet',
            'team_id'  => 'Lorem ipsum dolor sit amet',
            'del_flg'  => 1,
            'deleted'  => '2014-05-22 02:28:04',
            'created'  => '2014-05-22 02:28:04',
            'modified' => '2014-05-22 02:28:04'
        ),
        array(
            'id'       => '537ce224-4860-4cb4-aef0-433dac11b50b',
            'post_id'  => 'Lorem ipsum dolor sit amet',
            'user_id'  => 'Lorem ipsum dolor sit amet',
            'team_id'  => 'Lorem ipsum dolor sit amet',
            'del_flg'  => 1,
            'deleted'  => '2014-05-22 02:28:04',
            'created'  => '2014-05-22 02:28:04',
            'modified' => '2014-05-22 02:28:04'
        ),
        array(
            'id'       => '537ce224-5fd0-424d-942d-433dac11b50b',
            'post_id'  => 'Lorem ipsum dolor sit amet',
            'user_id'  => 'Lorem ipsum dolor sit amet',
            'team_id'  => 'Lorem ipsum dolor sit amet',
            'del_flg'  => 1,
            'deleted'  => '2014-05-22 02:28:04',
            'created'  => '2014-05-22 02:28:04',
            'modified' => '2014-05-22 02:28:04'
        ),
        array(
            'id'       => '537ce224-76dc-4f97-ad29-433dac11b50b',
            'post_id'  => 'Lorem ipsum dolor sit amet',
            'user_id'  => 'Lorem ipsum dolor sit amet',
            'team_id'  => 'Lorem ipsum dolor sit amet',
            'del_flg'  => 1,
            'deleted'  => '2014-05-22 02:28:04',
            'created'  => '2014-05-22 02:28:04',
            'modified' => '2014-05-22 02:28:04'
        ),
        array(
            'id'       => '537ce224-8de8-47ca-8908-433dac11b50b',
            'post_id'  => 'Lorem ipsum dolor sit amet',
            'user_id'  => 'Lorem ipsum dolor sit amet',
            'team_id'  => 'Lorem ipsum dolor sit amet',
            'del_flg'  => 1,
            'deleted'  => '2014-05-22 02:28:04',
            'created'  => '2014-05-22 02:28:04',
            'modified' => '2014-05-22 02:28:04'
        ),
        array(
            'id'       => '537ce224-a4f4-46df-9231-433dac11b50b',
            'post_id'  => 'Lorem ipsum dolor sit amet',
            'user_id'  => 'Lorem ipsum dolor sit amet',
            'team_id'  => 'Lorem ipsum dolor sit amet',
            'del_flg'  => 1,
            'deleted'  => '2014-05-22 02:28:04',
            'created'  => '2014-05-22 02:28:04',
            'modified' => '2014-05-22 02:28:04'
        ),
        array(
            'id'       => '537ce224-c2a4-486e-af05-433dac11b50b',
            'post_id'  => 'Lorem ipsum dolor sit amet',
            'user_id'  => 'Lorem ipsum dolor sit amet',
            'team_id'  => 'Lorem ipsum dolor sit amet',
            'del_flg'  => 1,
            'deleted'  => '2014-05-22 02:28:04',
            'created'  => '2014-05-22 02:28:04',
            'modified' => '2014-05-22 02:28:04'
        ),
        array(
            'id'       => '537ce224-dadc-4400-b66b-433dac11b50b',
            'post_id'  => 'Lorem ipsum dolor sit amet',
            'user_id'  => 'Lorem ipsum dolor sit amet',
            'team_id'  => 'Lorem ipsum dolor sit amet',
            'del_flg'  => 1,
            'deleted'  => '2014-05-22 02:28:04',
            'created'  => '2014-05-22 02:28:04',
            'modified' => '2014-05-22 02:28:04'
        ),
        array(
            'id'       => '537ce224-f24c-4639-8fdc-433dac11b50b',
            'post_id'  => 'Lorem ipsum dolor sit amet',
            'user_id'  => 'Lorem ipsum dolor sit amet',
            'team_id'  => 'Lorem ipsum dolor sit amet',
            'del_flg'  => 1,
            'deleted'  => '2014-05-22 02:28:04',
            'created'  => '2014-05-22 02:28:04',
            'modified' => '2014-05-22 02:28:04'
        ),
        array(
            'id'       => '537ce224-0958-440c-bee5-433dac11b50b',
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
