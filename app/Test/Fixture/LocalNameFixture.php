<?php

/**
 * LocalNameFixture

 */
class LocalNameFixture extends CakeTestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = array(
        'id'              => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'ローカル名ID'),
        'user_id'         => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'ユーザID(belongsToでUserモデルに関連)'),
        'language'        => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => '言語(日本語ならjpn)', 'charset' => 'utf8'),
        'first_name'      => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => '名', 'charset' => 'utf8'),
        'last_name'       => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => '姓', 'charset' => 'utf8'),
        'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => '削除フラグ'),
        'deleted'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'メアドを削除した日付時刻'),
        'created'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'メアドを登録した日付時刻'),
        'modified'        => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'メアドを最後に更新した日付時刻'),
        'indexes'         => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1),
            'user_id' => array('column' => 'user_id', 'unique' => 0),
            'del_flg' => array('column' => 'del_flg', 'unique' => 0)
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
            'id'         => '1',
            'user_id'    => '12',
            'language'   => 'jpn',
            'first_name' => 'ろーかる名',
            'last_name'  => 'ろーかる姓',
            'del_flg'    => 0,
            'deleted'    => null,
            'created'    => '2014-06-25 06:24:16',
            'modified'   => '2014-06-25 06:24:16'
        ),
        array(
            'id'         => '2',
            'user_id'    => '5',
            'language'   => 'jpn',
            'first_name' => '名',
            'last_name'  => '姓',
            'del_flg'    => 0,
            'deleted'    => null,
            'created'    => '2014-06-25 06:24:16',
            'modified'   => '2014-06-25 06:24:16'
        ),
        array(
            'id'         => '3',
            'user_id'    => '6',
            'language'   => 'jpn',
            'first_name' => '名',
            'last_name'  => '姓',
            'del_flg'    => 0,
            'deleted'    => null,
            'created'    => '2014-06-25 06:24:16',
            'modified'   => '2014-06-25 06:24:16'
        ),
        array(
            'id'         => '',
            'user_id'    => '',
            'language'   => 'Lorem ipsum dolor sit amet',
            'first_name' => 'Lorem ipsum dolor sit amet',
            'last_name'  => 'Lorem ipsum dolor sit amet',
            'del_flg'    => 1,
            'deleted'    => 4,
            'created'    => 4,
            'modified'   => 4
        ),
        array(
            'id'         => '',
            'user_id'    => '',
            'language'   => 'Lorem ipsum dolor sit amet',
            'first_name' => 'Lorem ipsum dolor sit amet',
            'last_name'  => 'Lorem ipsum dolor sit amet',
            'del_flg'    => 1,
            'deleted'    => 5,
            'created'    => 5,
            'modified'   => 5
        ),
        array(
            'id'         => '',
            'user_id'    => '',
            'language'   => 'Lorem ipsum dolor sit amet',
            'first_name' => 'Lorem ipsum dolor sit amet',
            'last_name'  => 'Lorem ipsum dolor sit amet',
            'del_flg'    => 1,
            'deleted'    => 6,
            'created'    => 6,
            'modified'   => 6
        ),
        array(
            'id'         => '',
            'user_id'    => '',
            'language'   => 'Lorem ipsum dolor sit amet',
            'first_name' => 'Lorem ipsum dolor sit amet',
            'last_name'  => 'Lorem ipsum dolor sit amet',
            'del_flg'    => 1,
            'deleted'    => 7,
            'created'    => 7,
            'modified'   => 7
        ),
        array(
            'id'         => '',
            'user_id'    => '',
            'language'   => 'Lorem ipsum dolor sit amet',
            'first_name' => 'Lorem ipsum dolor sit amet',
            'last_name'  => 'Lorem ipsum dolor sit amet',
            'del_flg'    => 1,
            'deleted'    => 8,
            'created'    => 8,
            'modified'   => 8
        ),
        array(
            'id'         => '',
            'user_id'    => '',
            'language'   => 'Lorem ipsum dolor sit amet',
            'first_name' => 'Lorem ipsum dolor sit amet',
            'last_name'  => 'Lorem ipsum dolor sit amet',
            'del_flg'    => 1,
            'deleted'    => 9,
            'created'    => 9,
            'modified'   => 9
        ),
        array(
            'id'         => '',
            'user_id'    => '',
            'language'   => 'Lorem ipsum dolor sit amet',
            'first_name' => 'Lorem ipsum dolor sit amet',
            'last_name'  => 'Lorem ipsum dolor sit amet',
            'del_flg'    => 1,
            'deleted'    => 10,
            'created'    => 10,
            'modified'   => 10
        ),
    );

}
