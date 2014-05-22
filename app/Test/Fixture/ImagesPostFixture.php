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
        'id'              => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'comment' => '投稿画像ID', 'charset' => 'utf8'),
        'post_id'         => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => '投稿ID(belongsToでPostモデルと関連)', 'charset' => 'utf8'),
        'image_id'        => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => '画像ID(belongsToでImageモデルと関連)', 'charset' => 'utf8'),
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
            'id'       => '537d95df-73d0-4167-9eb4-13d0ac11b50b',
            'post_id'  => 'Lorem ipsum dolor sit amet',
            'image_id' => 'Lorem ipsum dolor sit amet',
            'del_flg'  => 1,
            'deleted'  => '2014-05-22 15:14:55',
            'created'  => '2014-05-22 15:14:55',
            'modified' => '2014-05-22 15:14:55'
        ),
        array(
            'id'       => '537d95df-9cd4-49b9-9f51-13d0ac11b50b',
            'post_id'  => 'Lorem ipsum dolor sit amet',
            'image_id' => 'Lorem ipsum dolor sit amet',
            'del_flg'  => 1,
            'deleted'  => '2014-05-22 15:14:55',
            'created'  => '2014-05-22 15:14:55',
            'modified' => '2014-05-22 15:14:55'
        ),
        array(
            'id'       => '537d95df-b570-4fe0-bbb9-13d0ac11b50b',
            'post_id'  => 'Lorem ipsum dolor sit amet',
            'image_id' => 'Lorem ipsum dolor sit amet',
            'del_flg'  => 1,
            'deleted'  => '2014-05-22 15:14:55',
            'created'  => '2014-05-22 15:14:55',
            'modified' => '2014-05-22 15:14:55'
        ),
        array(
            'id'       => '537d95df-cce0-4776-be3b-13d0ac11b50b',
            'post_id'  => 'Lorem ipsum dolor sit amet',
            'image_id' => 'Lorem ipsum dolor sit amet',
            'del_flg'  => 1,
            'deleted'  => '2014-05-22 15:14:55',
            'created'  => '2014-05-22 15:14:55',
            'modified' => '2014-05-22 15:14:55'
        ),
        array(
            'id'       => '537d95df-e3ec-4193-bb63-13d0ac11b50b',
            'post_id'  => 'Lorem ipsum dolor sit amet',
            'image_id' => 'Lorem ipsum dolor sit amet',
            'del_flg'  => 1,
            'deleted'  => '2014-05-22 15:14:55',
            'created'  => '2014-05-22 15:14:55',
            'modified' => '2014-05-22 15:14:55'
        ),
        array(
            'id'       => '537d95df-fcec-4cbf-880f-13d0ac11b50b',
            'post_id'  => 'Lorem ipsum dolor sit amet',
            'image_id' => 'Lorem ipsum dolor sit amet',
            'del_flg'  => 1,
            'deleted'  => '2014-05-22 15:14:55',
            'created'  => '2014-05-22 15:14:55',
            'modified' => '2014-05-22 15:14:55'
        ),
        array(
            'id'       => '537d95df-14c0-4402-8b54-13d0ac11b50b',
            'post_id'  => 'Lorem ipsum dolor sit amet',
            'image_id' => 'Lorem ipsum dolor sit amet',
            'del_flg'  => 1,
            'deleted'  => '2014-05-22 15:14:55',
            'created'  => '2014-05-22 15:14:55',
            'modified' => '2014-05-22 15:14:55'
        ),
        array(
            'id'       => '537d95df-2b68-4d5a-8671-13d0ac11b50b',
            'post_id'  => 'Lorem ipsum dolor sit amet',
            'image_id' => 'Lorem ipsum dolor sit amet',
            'del_flg'  => 1,
            'deleted'  => '2014-05-22 15:14:55',
            'created'  => '2014-05-22 15:14:55',
            'modified' => '2014-05-22 15:14:55'
        ),
        array(
            'id'       => '537d95df-51b0-434d-97e1-13d0ac11b50b',
            'post_id'  => 'Lorem ipsum dolor sit amet',
            'image_id' => 'Lorem ipsum dolor sit amet',
            'del_flg'  => 1,
            'deleted'  => '2014-05-22 15:14:55',
            'created'  => '2014-05-22 15:14:55',
            'modified' => '2014-05-22 15:14:55'
        ),
        array(
            'id'       => '537d95df-68bc-4397-b0ef-13d0ac11b50b',
            'post_id'  => 'Lorem ipsum dolor sit amet',
            'image_id' => 'Lorem ipsum dolor sit amet',
            'del_flg'  => 1,
            'deleted'  => '2014-05-22 15:14:55',
            'created'  => '2014-05-22 15:14:55',
            'modified' => '2014-05-22 15:14:55'
        ),
    );

}
