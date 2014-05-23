<?php

/**
 * GivenBadgeFixture

 */
class GivenBadgeFixture extends CakeTestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = array(
        'id'              => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'comment' => '所有バッジID', 'charset' => 'utf8'),
        'user_id'         => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'バッジ所有ユーザID(belongsToでUserモデルに関連)', 'charset' => 'utf8'),
        'grant_user_id'   => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'バッジあげたユーザID(belongsToでUserモデルに関連)', 'charset' => 'utf8'),
        'team_id'         => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'チームID(belongsToでTeamモデルに関連)', 'charset' => 'utf8'),
        'post_id'         => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => '投稿ID(hasOneでPostモデルに関連)', 'charset' => 'utf8'),
        'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
        'deleted'         => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '所有バッジを削除した日付時刻'),
        'created'         => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '所有バッジを追加した日付時刻'),
        'modified'        => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '所有バッジを更新した日付時刻'),
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
            'id'            => '537ce223-250c-4803-adb3-433dac11b50b',
            'user_id'       => 'Lorem ipsum dolor sit amet',
            'grant_user_id' => 'Lorem ipsum dolor sit amet',
            'team_id'       => 'Lorem ipsum dolor sit amet',
            'post_id'       => 'Lorem ipsum dolor sit amet',
            'del_flg'       => 1,
            'deleted'       => '2014-05-22 02:28:03',
            'created'       => '2014-05-22 02:28:03',
            'modified'      => '2014-05-22 02:28:03'
        ),
        array(
            'id'            => '537ce223-4960-45b7-958d-433dac11b50b',
            'user_id'       => 'Lorem ipsum dolor sit amet',
            'grant_user_id' => 'Lorem ipsum dolor sit amet',
            'team_id'       => 'Lorem ipsum dolor sit amet',
            'post_id'       => 'Lorem ipsum dolor sit amet',
            'del_flg'       => 1,
            'deleted'       => '2014-05-22 02:28:03',
            'created'       => '2014-05-22 02:28:03',
            'modified'      => '2014-05-22 02:28:03'
        ),
        array(
            'id'            => '537ce223-6328-428f-9cb3-433dac11b50b',
            'user_id'       => 'Lorem ipsum dolor sit amet',
            'grant_user_id' => 'Lorem ipsum dolor sit amet',
            'team_id'       => 'Lorem ipsum dolor sit amet',
            'post_id'       => 'Lorem ipsum dolor sit amet',
            'del_flg'       => 1,
            'deleted'       => '2014-05-22 02:28:03',
            'created'       => '2014-05-22 02:28:03',
            'modified'      => '2014-05-22 02:28:03'
        ),
        array(
            'id'            => '537ce223-7c8c-4e12-ad4b-433dac11b50b',
            'user_id'       => 'Lorem ipsum dolor sit amet',
            'grant_user_id' => 'Lorem ipsum dolor sit amet',
            'team_id'       => 'Lorem ipsum dolor sit amet',
            'post_id'       => 'Lorem ipsum dolor sit amet',
            'del_flg'       => 1,
            'deleted'       => '2014-05-22 02:28:03',
            'created'       => '2014-05-22 02:28:03',
            'modified'      => '2014-05-22 02:28:03'
        ),
        array(
            'id'            => '537ce223-958c-4dd4-9096-433dac11b50b',
            'user_id'       => 'Lorem ipsum dolor sit amet',
            'grant_user_id' => 'Lorem ipsum dolor sit amet',
            'team_id'       => 'Lorem ipsum dolor sit amet',
            'post_id'       => 'Lorem ipsum dolor sit amet',
            'del_flg'       => 1,
            'deleted'       => '2014-05-22 02:28:03',
            'created'       => '2014-05-22 02:28:03',
            'modified'      => '2014-05-22 02:28:03'
        ),
        array(
            'id'            => '537ce223-ae8c-4f59-80bb-433dac11b50b',
            'user_id'       => 'Lorem ipsum dolor sit amet',
            'grant_user_id' => 'Lorem ipsum dolor sit amet',
            'team_id'       => 'Lorem ipsum dolor sit amet',
            'post_id'       => 'Lorem ipsum dolor sit amet',
            'del_flg'       => 1,
            'deleted'       => '2014-05-22 02:28:03',
            'created'       => '2014-05-22 02:28:03',
            'modified'      => '2014-05-22 02:28:03'
        ),
        array(
            'id'            => '537ce223-c78c-47d8-a3ac-433dac11b50b',
            'user_id'       => 'Lorem ipsum dolor sit amet',
            'grant_user_id' => 'Lorem ipsum dolor sit amet',
            'team_id'       => 'Lorem ipsum dolor sit amet',
            'post_id'       => 'Lorem ipsum dolor sit amet',
            'del_flg'       => 1,
            'deleted'       => '2014-05-22 02:28:03',
            'created'       => '2014-05-22 02:28:03',
            'modified'      => '2014-05-22 02:28:03'
        ),
        array(
            'id'            => '537ce223-e08c-47db-ae12-433dac11b50b',
            'user_id'       => 'Lorem ipsum dolor sit amet',
            'grant_user_id' => 'Lorem ipsum dolor sit amet',
            'team_id'       => 'Lorem ipsum dolor sit amet',
            'post_id'       => 'Lorem ipsum dolor sit amet',
            'del_flg'       => 1,
            'deleted'       => '2014-05-22 02:28:03',
            'created'       => '2014-05-22 02:28:03',
            'modified'      => '2014-05-22 02:28:03'
        ),
        array(
            'id'            => '537ce223-f98c-4b2b-bb96-433dac11b50b',
            'user_id'       => 'Lorem ipsum dolor sit amet',
            'grant_user_id' => 'Lorem ipsum dolor sit amet',
            'team_id'       => 'Lorem ipsum dolor sit amet',
            'post_id'       => 'Lorem ipsum dolor sit amet',
            'del_flg'       => 1,
            'deleted'       => '2014-05-22 02:28:03',
            'created'       => '2014-05-22 02:28:03',
            'modified'      => '2014-05-22 02:28:03'
        ),
        array(
            'id'            => '537ce223-0ddc-42b7-a240-433dac11b50b',
            'user_id'       => 'Lorem ipsum dolor sit amet',
            'grant_user_id' => 'Lorem ipsum dolor sit amet',
            'team_id'       => 'Lorem ipsum dolor sit amet',
            'post_id'       => 'Lorem ipsum dolor sit amet',
            'del_flg'       => 1,
            'deleted'       => '2014-05-22 02:28:03',
            'created'       => '2014-05-22 02:28:03',
            'modified'      => '2014-05-22 02:28:03'
        ),
    );

}
