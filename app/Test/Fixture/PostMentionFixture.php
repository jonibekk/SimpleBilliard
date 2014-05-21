<?php

/**
 * PostMentionFixture

 */
class PostMentionFixture extends CakeTestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = array(
        'id'              => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'comment' => '投稿メンションID', 'charset' => 'utf8'),
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
            'id'       => '537ce224-07e4-4861-b982-433dac11b50b',
            'post_id'  => 'Lorem ipsum dolor sit amet',
            'user_id'  => 'Lorem ipsum dolor sit amet',
            'team_id'  => 'Lorem ipsum dolor sit amet',
            'del_flg'  => 1,
            'deleted'  => '2014-05-22 02:28:04',
            'created'  => '2014-05-22 02:28:04',
            'modified' => '2014-05-22 02:28:04'
        ),
        array(
            'id'       => '537ce224-28b4-49bc-8274-433dac11b50b',
            'post_id'  => 'Lorem ipsum dolor sit amet',
            'user_id'  => 'Lorem ipsum dolor sit amet',
            'team_id'  => 'Lorem ipsum dolor sit amet',
            'del_flg'  => 1,
            'deleted'  => '2014-05-22 02:28:04',
            'created'  => '2014-05-22 02:28:04',
            'modified' => '2014-05-22 02:28:04'
        ),
        array(
            'id'       => '537ce224-4088-4fc5-adcb-433dac11b50b',
            'post_id'  => 'Lorem ipsum dolor sit amet',
            'user_id'  => 'Lorem ipsum dolor sit amet',
            'team_id'  => 'Lorem ipsum dolor sit amet',
            'del_flg'  => 1,
            'deleted'  => '2014-05-22 02:28:04',
            'created'  => '2014-05-22 02:28:04',
            'modified' => '2014-05-22 02:28:04'
        ),
        array(
            'id'       => '537ce224-5794-4644-b347-433dac11b50b',
            'post_id'  => 'Lorem ipsum dolor sit amet',
            'user_id'  => 'Lorem ipsum dolor sit amet',
            'team_id'  => 'Lorem ipsum dolor sit amet',
            'del_flg'  => 1,
            'deleted'  => '2014-05-22 02:28:04',
            'created'  => '2014-05-22 02:28:04',
            'modified' => '2014-05-22 02:28:04'
        ),
        array(
            'id'       => '537ce224-6ea0-47ff-a718-433dac11b50b',
            'post_id'  => 'Lorem ipsum dolor sit amet',
            'user_id'  => 'Lorem ipsum dolor sit amet',
            'team_id'  => 'Lorem ipsum dolor sit amet',
            'del_flg'  => 1,
            'deleted'  => '2014-05-22 02:28:04',
            'created'  => '2014-05-22 02:28:04',
            'modified' => '2014-05-22 02:28:04'
        ),
        array(
            'id'       => '537ce224-85ac-4cac-aef4-433dac11b50b',
            'post_id'  => 'Lorem ipsum dolor sit amet',
            'user_id'  => 'Lorem ipsum dolor sit amet',
            'team_id'  => 'Lorem ipsum dolor sit amet',
            'del_flg'  => 1,
            'deleted'  => '2014-05-22 02:28:04',
            'created'  => '2014-05-22 02:28:04',
            'modified' => '2014-05-22 02:28:04'
        ),
        array(
            'id'       => '537ce224-a1cc-4ddd-b01a-433dac11b50b',
            'post_id'  => 'Lorem ipsum dolor sit amet',
            'user_id'  => 'Lorem ipsum dolor sit amet',
            'team_id'  => 'Lorem ipsum dolor sit amet',
            'del_flg'  => 1,
            'deleted'  => '2014-05-22 02:28:04',
            'created'  => '2014-05-22 02:28:04',
            'modified' => '2014-05-22 02:28:04'
        ),
        array(
            'id'       => '537ce224-b9a0-4c62-a261-433dac11b50b',
            'post_id'  => 'Lorem ipsum dolor sit amet',
            'user_id'  => 'Lorem ipsum dolor sit amet',
            'team_id'  => 'Lorem ipsum dolor sit amet',
            'del_flg'  => 1,
            'deleted'  => '2014-05-22 02:28:04',
            'created'  => '2014-05-22 02:28:04',
            'modified' => '2014-05-22 02:28:04'
        ),
        array(
            'id'       => '537ce224-d4f8-44a2-bd29-433dac11b50b',
            'post_id'  => 'Lorem ipsum dolor sit amet',
            'user_id'  => 'Lorem ipsum dolor sit amet',
            'team_id'  => 'Lorem ipsum dolor sit amet',
            'del_flg'  => 1,
            'deleted'  => '2014-05-22 02:28:04',
            'created'  => '2014-05-22 02:28:04',
            'modified' => '2014-05-22 02:28:04'
        ),
        array(
            'id'       => '537ce224-ec68-4acd-8d66-433dac11b50b',
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
