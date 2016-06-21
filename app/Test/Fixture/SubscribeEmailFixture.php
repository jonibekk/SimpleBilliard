<?php App::uses('CakeTestFixtureEx', 'Test/Fixture');

/**
 * SubscribeEmailFixture
 */
class SubscribeEmailFixture extends CakeTestFixtureEx
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = array(
        'id'              => array('type' => 'primary_key', 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'ID'),
        'email'           => array('type' => 'string', 'default' => null, 'key' => 'index', 'collate' => 'utf8_general_ci', 'comment' => 'メアド', 'charset' => 'utf8'),
        'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
        'deleted'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '削除した日付時刻'),
        'created'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '登録した日付時刻'),
        'modified'        => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '最後に更新した日付時刻'),
        'indexes'         => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1),
            'email'   => array('column' => 'email', 'unique' => 0)
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
            'id'       => '1',
            'email'    => 'Lorem ipsum dolor sit amet',
            'del_flg'  => 1,
            'deleted'  => 1,
            'created'  => 1,
            'modified' => 1
        ),
    );

}
