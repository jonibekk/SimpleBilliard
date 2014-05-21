<?php

/**
 * PostsImageFixture

 */
class PostsImageFixture extends CakeTestFixture
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
            'id'       => '537ce224-86a8-4ea4-bbb9-433dac11b50b',
            'post_id'  => 'Lorem ipsum dolor sit amet',
            'image_id' => 'Lorem ipsum dolor sit amet',
            'del_flg'  => 1,
            'deleted'  => '2014-05-22 02:28:04',
            'created'  => '2014-05-22 02:28:04',
            'modified' => '2014-05-22 02:28:04'
        ),
        array(
            'id'       => '537ce224-a714-4113-8f82-433dac11b50b',
            'post_id'  => 'Lorem ipsum dolor sit amet',
            'image_id' => 'Lorem ipsum dolor sit amet',
            'del_flg'  => 1,
            'deleted'  => '2014-05-22 02:28:04',
            'created'  => '2014-05-22 02:28:04',
            'modified' => '2014-05-22 02:28:04'
        ),
        array(
            'id'       => '537ce224-be84-4193-bf3c-433dac11b50b',
            'post_id'  => 'Lorem ipsum dolor sit amet',
            'image_id' => 'Lorem ipsum dolor sit amet',
            'del_flg'  => 1,
            'deleted'  => '2014-05-22 02:28:04',
            'created'  => '2014-05-22 02:28:04',
            'modified' => '2014-05-22 02:28:04'
        ),
        array(
            'id'       => '537ce224-d52c-4c3d-bf9d-433dac11b50b',
            'post_id'  => 'Lorem ipsum dolor sit amet',
            'image_id' => 'Lorem ipsum dolor sit amet',
            'del_flg'  => 1,
            'deleted'  => '2014-05-22 02:28:04',
            'created'  => '2014-05-22 02:28:04',
            'modified' => '2014-05-22 02:28:04'
        ),
        array(
            'id'       => '537ce224-ec38-4754-a447-433dac11b50b',
            'post_id'  => 'Lorem ipsum dolor sit amet',
            'image_id' => 'Lorem ipsum dolor sit amet',
            'del_flg'  => 1,
            'deleted'  => '2014-05-22 02:28:04',
            'created'  => '2014-05-22 02:28:04',
            'modified' => '2014-05-22 02:28:04'
        ),
        array(
            'id'       => '537ce224-02e0-4e7d-9f14-433dac11b50b',
            'post_id'  => 'Lorem ipsum dolor sit amet',
            'image_id' => 'Lorem ipsum dolor sit amet',
            'del_flg'  => 1,
            'deleted'  => '2014-05-22 02:28:04',
            'created'  => '2014-05-22 02:28:04',
            'modified' => '2014-05-22 02:28:04'
        ),
        array(
            'id'       => '537ce224-19ec-4d21-a5c4-433dac11b50b',
            'post_id'  => 'Lorem ipsum dolor sit amet',
            'image_id' => 'Lorem ipsum dolor sit amet',
            'del_flg'  => 1,
            'deleted'  => '2014-05-22 02:28:04',
            'created'  => '2014-05-22 02:28:04',
            'modified' => '2014-05-22 02:28:04'
        ),
        array(
            'id'       => '537ce224-315c-4702-adda-433dac11b50b',
            'post_id'  => 'Lorem ipsum dolor sit amet',
            'image_id' => 'Lorem ipsum dolor sit amet',
            'del_flg'  => 1,
            'deleted'  => '2014-05-22 02:28:04',
            'created'  => '2014-05-22 02:28:04',
            'modified' => '2014-05-22 02:28:04'
        ),
        array(
            'id'       => '537ce224-4868-46a8-ae11-433dac11b50b',
            'post_id'  => 'Lorem ipsum dolor sit amet',
            'image_id' => 'Lorem ipsum dolor sit amet',
            'del_flg'  => 1,
            'deleted'  => '2014-05-22 02:28:04',
            'created'  => '2014-05-22 02:28:04',
            'modified' => '2014-05-22 02:28:04'
        ),
        array(
            'id'       => '537ce224-5f74-4a74-beba-433dac11b50b',
            'post_id'  => 'Lorem ipsum dolor sit amet',
            'image_id' => 'Lorem ipsum dolor sit amet',
            'del_flg'  => 1,
            'deleted'  => '2014-05-22 02:28:04',
            'created'  => '2014-05-22 02:28:04',
            'modified' => '2014-05-22 02:28:04'
        ),
    );

}
