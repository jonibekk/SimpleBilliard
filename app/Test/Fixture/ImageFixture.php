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
        'id'              => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'comment' => '画像ID', 'charset' => 'utf8'),
        'user_id'         => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'ユーザID(belongsToでUserモデルに関連)', 'charset' => 'utf8'),
        'type'            => array('type' => 'integer', 'null' => false, 'default' => '1', 'unsigned' => false, 'comment' => '画像タイプ(1:ユーザ画像,2:ゴール画像,3:バッジ画像,4:投稿画像)'),
        'name'            => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '画像名', 'charset' => 'utf8'),
        'item_file_name'  => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '画像ファイル名', 'charset' => 'utf8'),
        'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
        'deleted'         => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '画像を削除した日付時刻'),
        'created'         => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '画像を追加した日付時刻'),
        'modified'        => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '画像を更新した日付時刻'),
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
            'id'             => '537ce223-827c-4d53-b925-433dac11b50b',
            'user_id'        => 'Lorem ipsum dolor sit amet',
            'type'           => 1,
            'name'           => 'Lorem ipsum dolor sit amet',
            'item_file_name' => 'Lorem ipsum dolor sit amet',
            'del_flg'        => 1,
            'deleted'        => '2014-05-22 02:28:03',
            'created'        => '2014-05-22 02:28:03',
            'modified'       => '2014-05-22 02:28:03'
        ),
        array(
            'id'             => '537ce223-a284-41c2-9f9f-433dac11b50b',
            'user_id'        => 'Lorem ipsum dolor sit amet',
            'type'           => 2,
            'name'           => 'Lorem ipsum dolor sit amet',
            'item_file_name' => 'Lorem ipsum dolor sit amet',
            'del_flg'        => 1,
            'deleted'        => '2014-05-22 02:28:03',
            'created'        => '2014-05-22 02:28:03',
            'modified'       => '2014-05-22 02:28:03'
        ),
        array(
            'id'             => '537ce223-bea4-46e3-bd60-433dac11b50b',
            'user_id'        => 'Lorem ipsum dolor sit amet',
            'type'           => 3,
            'name'           => 'Lorem ipsum dolor sit amet',
            'item_file_name' => 'Lorem ipsum dolor sit amet',
            'del_flg'        => 1,
            'deleted'        => '2014-05-22 02:28:03',
            'created'        => '2014-05-22 02:28:03',
            'modified'       => '2014-05-22 02:28:03'
        ),
        array(
            'id'             => '537ce223-d9fc-43bc-8777-433dac11b50b',
            'user_id'        => 'Lorem ipsum dolor sit amet',
            'type'           => 4,
            'name'           => 'Lorem ipsum dolor sit amet',
            'item_file_name' => 'Lorem ipsum dolor sit amet',
            'del_flg'        => 1,
            'deleted'        => '2014-05-22 02:28:03',
            'created'        => '2014-05-22 02:28:03',
            'modified'       => '2014-05-22 02:28:03'
        ),
        array(
            'id'             => '537ce223-f0a4-4511-bf5c-433dac11b50b',
            'user_id'        => 'Lorem ipsum dolor sit amet',
            'type'           => 5,
            'name'           => 'Lorem ipsum dolor sit amet',
            'item_file_name' => 'Lorem ipsum dolor sit amet',
            'del_flg'        => 1,
            'deleted'        => '2014-05-22 02:28:03',
            'created'        => '2014-05-22 02:28:03',
            'modified'       => '2014-05-22 02:28:03'
        ),
        array(
            'id'             => '537ce223-06e8-4a1b-b26c-433dac11b50b',
            'user_id'        => 'Lorem ipsum dolor sit amet',
            'type'           => 6,
            'name'           => 'Lorem ipsum dolor sit amet',
            'item_file_name' => 'Lorem ipsum dolor sit amet',
            'del_flg'        => 1,
            'deleted'        => '2014-05-22 02:28:03',
            'created'        => '2014-05-22 02:28:03',
            'modified'       => '2014-05-22 02:28:03'
        ),
        array(
            'id'             => '537ce223-1d90-4f35-ba00-433dac11b50b',
            'user_id'        => 'Lorem ipsum dolor sit amet',
            'type'           => 7,
            'name'           => 'Lorem ipsum dolor sit amet',
            'item_file_name' => 'Lorem ipsum dolor sit amet',
            'del_flg'        => 1,
            'deleted'        => '2014-05-22 02:28:03',
            'created'        => '2014-05-22 02:28:03',
            'modified'       => '2014-05-22 02:28:03'
        ),
        array(
            'id'             => '537ce223-3438-4b43-812e-433dac11b50b',
            'user_id'        => 'Lorem ipsum dolor sit amet',
            'type'           => 8,
            'name'           => 'Lorem ipsum dolor sit amet',
            'item_file_name' => 'Lorem ipsum dolor sit amet',
            'del_flg'        => 1,
            'deleted'        => '2014-05-22 02:28:03',
            'created'        => '2014-05-22 02:28:03',
            'modified'       => '2014-05-22 02:28:03'
        ),
        array(
            'id'             => '537ce223-4ae0-43bc-a404-433dac11b50b',
            'user_id'        => 'Lorem ipsum dolor sit amet',
            'type'           => 9,
            'name'           => 'Lorem ipsum dolor sit amet',
            'item_file_name' => 'Lorem ipsum dolor sit amet',
            'del_flg'        => 1,
            'deleted'        => '2014-05-22 02:28:03',
            'created'        => '2014-05-22 02:28:03',
            'modified'       => '2014-05-22 02:28:03'
        ),
        array(
            'id'             => '537ce223-6124-40e8-b9ae-433dac11b50b',
            'user_id'        => 'Lorem ipsum dolor sit amet',
            'type'           => 10,
            'name'           => 'Lorem ipsum dolor sit amet',
            'item_file_name' => 'Lorem ipsum dolor sit amet',
            'del_flg'        => 1,
            'deleted'        => '2014-05-22 02:28:03',
            'created'        => '2014-05-22 02:28:03',
            'modified'       => '2014-05-22 02:28:03'
        ),
    );

}
