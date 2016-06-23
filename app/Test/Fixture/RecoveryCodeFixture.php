<?php App::uses('CakeTestFixtureEx', 'Test/Fixture');

/**
 * RecoveryCodeFixture
 */
class RecoveryCodeFixture extends CakeTestFixtureEx
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = array(
        'id'              => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
        'user_id'         => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'コード持ち主のuser_id'),
        'code'            => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'アプリ側で暗号化済のコード', 'charset' => 'utf8'),
        'used'            => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'コードを利用した日時'),
        'available_flg'   => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => 'コード利用可能フラグ'),
        'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '0'),
        'deleted'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
        'created'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
        'modified'        => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
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
    public $records = [];

}
