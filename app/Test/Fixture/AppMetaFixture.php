<?php
App::uses('CakeTestFixtureEx', 'Test/Fixture');

/**
 * AppMetaFixture
 */
class AppMetaFixture extends CakeTestFixtureEx
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = array(
        'id'              => array(
            'type'     => 'integer',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'primary',
            'comment'  => 'メタID'
        ),
        'key_name'        => array(
            'type'    => 'string',
            'null'    => false,
            'default' => null,
            'length'  => 20,
            'collate' => 'utf8mb4_general_ci',
            'comment' => 'キーの名前',
            'charset' => 'utf8mb4'
        ),
        'value'           => array(
            'type'    => 'string',
            'null'    => false,
            'default' => null,
            'length'  => 128,
            'collate' => 'utf8mb4_general_ci',
            'comment' => '値',
            'charset' => 'utf8mb4'
        ),
        'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
        'deleted'         => array(
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => '削除した日付時刻'
        ),
        'created'         => array(
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => '追加した日付時刻'
        ),
        'modified'        => array(
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => '更新した日付時刻'
        ),
        'indexes'         => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
    );

    /**
     * Records
     *
     * @var array
     */
    public $records = [];

}
