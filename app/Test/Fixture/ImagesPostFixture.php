<?php

/**
 * ImagesPostFixture
 */
class ImagesPostFixture extends CakeTestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = array(
        'id'              => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => '投稿画像ID'),
        'post_id'         => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '投稿ID(belongsToでPostモデルと関連)'),
        'image_id'        => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '画像ID(belongsToでImageモデルと関連)'),
        'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => '削除フラグ'),
        'deleted'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '所有バッジを削除した日付時刻'),
        'created'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '所有バッジを追加した日付時刻'),
        'modified'        => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '所有バッジを更新した日付時刻'),
        'indexes'         => array(
            'PRIMARY'  => array('column' => 'id', 'unique' => 1),
            'post_id'  => array('column' => 'post_id', 'unique' => 0),
            'image_id' => array('column' => 'image_id', 'unique' => 0),
            'del_flg'  => array('column' => 'del_flg', 'unique' => 0)
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
            'id'       => '',
            'post_id'  => '',
            'image_id' => '',
            'del_flg'  => 1,
            'deleted'  => 1,
            'created'  => 1,
            'modified' => 1
        ),
        array(
            'id'       => '',
            'post_id'  => '',
            'image_id' => '',
            'del_flg'  => 1,
            'deleted'  => 2,
            'created'  => 2,
            'modified' => 2
        ),
        array(
            'id'       => '',
            'post_id'  => '',
            'image_id' => '',
            'del_flg'  => 1,
            'deleted'  => 3,
            'created'  => 3,
            'modified' => 3
        ),
        array(
            'id'       => '',
            'post_id'  => '',
            'image_id' => '',
            'del_flg'  => 1,
            'deleted'  => 4,
            'created'  => 4,
            'modified' => 4
        ),
        array(
            'id'       => '',
            'post_id'  => '',
            'image_id' => '',
            'del_flg'  => 1,
            'deleted'  => 5,
            'created'  => 5,
            'modified' => 5
        ),
        array(
            'id'       => '',
            'post_id'  => '',
            'image_id' => '',
            'del_flg'  => 1,
            'deleted'  => 6,
            'created'  => 6,
            'modified' => 6
        ),
        array(
            'id'       => '',
            'post_id'  => '',
            'image_id' => '',
            'del_flg'  => 1,
            'deleted'  => 7,
            'created'  => 7,
            'modified' => 7
        ),
        array(
            'id'       => '',
            'post_id'  => '',
            'image_id' => '',
            'del_flg'  => 1,
            'deleted'  => 8,
            'created'  => 8,
            'modified' => 8
        ),
        array(
            'id'       => '',
            'post_id'  => '',
            'image_id' => '',
            'del_flg'  => 1,
            'deleted'  => 9,
            'created'  => 9,
            'modified' => 9
        ),
        array(
            'id'       => '',
            'post_id'  => '',
            'image_id' => '',
            'del_flg'  => 1,
            'deleted'  => 10,
            'created'  => 10,
            'modified' => 10
        ),
    );

}
