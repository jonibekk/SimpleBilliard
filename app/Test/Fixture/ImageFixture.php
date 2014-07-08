<?php

/**
 * ImageFixture

 */
class ImageFixture extends CakeTestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = array(
        'id'              => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => '画像ID'),
        'user_id'         => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'ユーザID(belongsToでUserモデルに関連)'),
        'type'            => array('type' => 'integer', 'null' => false, 'default' => '1', 'length' => 3, 'unsigned' => true, 'comment' => '画像タイプ(1:ユーザ画像,2:ゴール画像,3:バッジ画像,4:投稿画像)'),
        'name'            => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '画像名', 'charset' => 'utf8'),
        'item_file_name'  => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '画像ファイル名', 'charset' => 'utf8'),
        'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => '削除フラグ'),
        'deleted'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '画像を削除した日付時刻'),
        'created'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '画像を追加した日付時刻'),
        'modified'        => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '画像を更新した日付時刻'),
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
            'id'             => '',
            'user_id'        => '',
            'type'           => 1,
            'name'           => 'Lorem ipsum dolor sit amet',
            'item_file_name' => 'Lorem ipsum dolor sit amet',
            'del_flg'        => 1,
            'deleted'        => 1,
            'created'        => 1,
            'modified'       => 1
        ),
        array(
            'id'             => '',
            'user_id'        => '',
            'type'           => 2,
            'name'           => 'Lorem ipsum dolor sit amet',
            'item_file_name' => 'Lorem ipsum dolor sit amet',
            'del_flg'        => 1,
            'deleted'        => 2,
            'created'        => 2,
            'modified'       => 2
        ),
        array(
            'id'             => '',
            'user_id'        => '',
            'type'           => 3,
            'name'           => 'Lorem ipsum dolor sit amet',
            'item_file_name' => 'Lorem ipsum dolor sit amet',
            'del_flg'        => 1,
            'deleted'        => 3,
            'created'        => 3,
            'modified'       => 3
        ),
        array(
            'id'             => '',
            'user_id'        => '',
            'type'           => 4,
            'name'           => 'Lorem ipsum dolor sit amet',
            'item_file_name' => 'Lorem ipsum dolor sit amet',
            'del_flg'        => 1,
            'deleted'        => 4,
            'created'        => 4,
            'modified'       => 4
        ),
        array(
            'id'             => '',
            'user_id'        => '',
            'type'           => 5,
            'name'           => 'Lorem ipsum dolor sit amet',
            'item_file_name' => 'Lorem ipsum dolor sit amet',
            'del_flg'        => 1,
            'deleted'        => 5,
            'created'        => 5,
            'modified'       => 5
        ),
        array(
            'id'             => '',
            'user_id'        => '',
            'type'           => 6,
            'name'           => 'Lorem ipsum dolor sit amet',
            'item_file_name' => 'Lorem ipsum dolor sit amet',
            'del_flg'        => 1,
            'deleted'        => 6,
            'created'        => 6,
            'modified'       => 6
        ),
        array(
            'id'             => '',
            'user_id'        => '',
            'type'           => 7,
            'name'           => 'Lorem ipsum dolor sit amet',
            'item_file_name' => 'Lorem ipsum dolor sit amet',
            'del_flg'        => 1,
            'deleted'        => 7,
            'created'        => 7,
            'modified'       => 7
        ),
        array(
            'id'             => '',
            'user_id'        => '',
            'type'           => 8,
            'name'           => 'Lorem ipsum dolor sit amet',
            'item_file_name' => 'Lorem ipsum dolor sit amet',
            'del_flg'        => 1,
            'deleted'        => 8,
            'created'        => 8,
            'modified'       => 8
        ),
        array(
            'id'             => '',
            'user_id'        => '',
            'type'           => 9,
            'name'           => 'Lorem ipsum dolor sit amet',
            'item_file_name' => 'Lorem ipsum dolor sit amet',
            'del_flg'        => 1,
            'deleted'        => 9,
            'created'        => 9,
            'modified'       => 9
        ),
        array(
            'id'             => '',
            'user_id'        => '',
            'type'           => 10,
            'name'           => 'Lorem ipsum dolor sit amet',
            'item_file_name' => 'Lorem ipsum dolor sit amet',
            'del_flg'        => 1,
            'deleted'        => 10,
            'created'        => 10,
            'modified'       => 10
        ),
    );

}
