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
        'id'              => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'comment' => 'ローカル名ID', 'charset' => 'utf8'),
        'user_id'         => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'key' => 'index', 'collate' => 'utf8_general_ci', 'comment' => 'ユーザID(belongsToでUserモデルに関連)', 'charset' => 'utf8'),
        'language'        => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => '言語(日本語ならjpn)', 'charset' => 'utf8'),
        'first_name'      => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => '名', 'charset' => 'utf8'),
        'last_name'       => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => '姓', 'charset' => 'utf8'),
        'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
        'deleted'         => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'メアドを削除した日付時刻'),
        'created'         => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'メアドを登録した日付時刻'),
        'modified'        => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'メアドを最後に更新した日付時刻'),
        'indexes'         => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1),
            'user_id' => array('column' => 'user_id', 'unique' => 0)
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
            'id'         => '53aa6b10-e598-484c-a35b-1606ac11b50b',
            'user_id'    => '537ce224-54b0-4081-b044-433dac11aaab',
            'language'   => 'jpn',
            'first_name' => 'ろーかる名',
            'last_name'  => 'ろーかる姓',
            'del_flg'    => 0,
            'deleted'    => null,
            'created'    => '2014-06-25 06:24:16',
            'modified'   => '2014-06-25 06:24:16'
        ),
        array(
            'id'         => '53aa6b10-0b7c-4904-9ae5-1606ac11b50b',
            'user_id'    => '537ce224-5ca4-4fd5-aaf2-433dac11b50b',
            'language'   => 'jpn',
            'first_name' => '名',
            'last_name'  => '姓',
            'del_flg'    => 0,
            'deleted'    => null,
            'created'    => '2014-06-25 06:24:16',
            'modified'   => '2014-06-25 06:24:16'
        ),
        array(
            'id'         => '53aa6b10-20f8-4147-a561-1606ac11b50b',
            'user_id'    => '537ce224-8f08-4cf3-9c8f-433dac11b50b',
            'language'   => 'jpn',
            'first_name' => '名',
            'last_name'  => '姓',
            'del_flg'    => 0,
            'deleted'    => null,
            'created'    => '2014-06-25 06:24:16',
            'modified'   => '2014-06-25 06:24:16'
        ),
        array(
            'id'       => '53aa6b10-3548-404a-a835-1606ac11b50b',
            'user_id'    => 'Lorem ipsum dolor sit amet',
            'language'   => 'Lorem ipsum dolor sit amet',
            'first_name' => 'Lorem ipsum dolor sit amet',
            'last_name'  => 'Lorem ipsum dolor sit amet',
            'del_flg'    => 1,
            'deleted'  => '2014-06-25 06:24:16',
            'created'  => '2014-06-25 06:24:16',
            'modified' => '2014-06-25 06:24:16'
        ),
        array(
            'id'         => '53aa6b10-486c-44c6-b928-1606ac11b50b',
            'user_id'    => 'Lorem ipsum dolor sit amet',
            'language'   => 'Lorem ipsum dolor sit amet',
            'first_name' => 'Lorem ipsum dolor sit amet',
            'last_name'  => 'Lorem ipsum dolor sit amet',
            'del_flg'    => 1,
            'deleted'    => '2014-06-25 06:24:16',
            'created'    => '2014-06-25 06:24:16',
            'modified'   => '2014-06-25 06:24:16'
        ),
        array(
            'id'         => '53aa6b10-5b2c-4071-991c-1606ac11b50b',
            'user_id'    => 'Lorem ipsum dolor sit amet',
            'language'   => 'Lorem ipsum dolor sit amet',
            'first_name' => 'Lorem ipsum dolor sit amet',
            'last_name'  => 'Lorem ipsum dolor sit amet',
            'del_flg'    => 1,
            'deleted'    => '2014-06-25 06:24:16',
            'created'    => '2014-06-25 06:24:16',
            'modified'   => '2014-06-25 06:24:16'
        ),
        array(
            'id'         => '53aa6b10-73c8-4281-a938-1606ac11b50b',
            'user_id'    => 'Lorem ipsum dolor sit amet',
            'language'   => 'Lorem ipsum dolor sit amet',
            'first_name' => 'Lorem ipsum dolor sit amet',
            'last_name'  => 'Lorem ipsum dolor sit amet',
            'del_flg'    => 1,
            'deleted'    => '2014-06-25 06:24:16',
            'created'    => '2014-06-25 06:24:16',
            'modified'   => '2014-06-25 06:24:16'
        ),
        array(
            'id'         => '53aa6b10-86ec-4933-a9a2-1606ac11b50b',
            'user_id'    => 'Lorem ipsum dolor sit amet',
            'language'   => 'Lorem ipsum dolor sit amet',
            'first_name' => 'Lorem ipsum dolor sit amet',
            'last_name'  => 'Lorem ipsum dolor sit amet',
            'del_flg'    => 1,
            'deleted'    => '2014-06-25 06:24:16',
            'created'    => '2014-06-25 06:24:16',
            'modified'   => '2014-06-25 06:24:16'
        ),
        array(
            'id'         => '53aa6b10-9a10-4afd-b8ee-1606ac11b50b',
            'user_id'    => 'Lorem ipsum dolor sit amet',
            'language'   => 'Lorem ipsum dolor sit amet',
            'first_name' => 'Lorem ipsum dolor sit amet',
            'last_name'  => 'Lorem ipsum dolor sit amet',
            'del_flg'    => 1,
            'deleted'    => '2014-06-25 06:24:16',
            'created'    => '2014-06-25 06:24:16',
            'modified'   => '2014-06-25 06:24:16'
        ),
        array(
            'id'         => '53aa6b10-acd0-460a-8815-1606ac11b50b',
            'user_id'    => 'Lorem ipsum dolor sit amet',
            'language'   => 'Lorem ipsum dolor sit amet',
            'first_name' => 'Lorem ipsum dolor sit amet',
            'last_name'  => 'Lorem ipsum dolor sit amet',
            'del_flg'    => 1,
            'deleted'    => '2014-06-25 06:24:16',
            'created'    => '2014-06-25 06:24:16',
            'modified'   => '2014-06-25 06:24:16'
        ),
    );

}
