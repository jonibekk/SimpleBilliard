<?php

/**
 * UserFixture
 *
 */
class UserFixture extends CakeTestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = array(
        'id'               => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'password'         => array('type' => 'string', 'null' => false, 'length' => 128, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'email'            => array('type' => 'string', 'null' => false, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => 'Eメールアドレス', 'charset' => 'utf8'),
        'local_first_name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => 'ローカル名', 'charset' => 'utf8'),
        'local_last_name'  => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => 'ローカル姓', 'charset' => 'utf8'),
        'first_name'       => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => '英名', 'charset' => 'utf8'),
        'last_name'        => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => '英姓', 'charset' => 'utf8'),
        'created'          => array('type' => 'datetime', 'null' => false, 'default' => null),
        'modified'         => array('type' => 'datetime', 'null' => false, 'default' => null),
        'indexes'          => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters'  => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    /**
     * Records
     *
     * @var array
     */
    public $records = array(
        array(
            'id'               => '532813e1-3e0c-4c11-897e-1e04ac11b50b',
            'password'         => 'Lorem ipsum dolor sit amet',
            'email'            => 'Lorem ipsum dolor sit amet',
            'local_first_name' => 'Lorem ipsum dolor sit amet',
            'local_last_name'  => 'Lorem ipsum dolor sit amet',
            'first_name'       => 'Lorem ipsum dolor sit amet',
            'last_name'        => 'Lorem ipsum dolor sit amet',
            'created'          => '2014-03-18 18:37:37',
            'modified'         => '2014-03-18 18:37:37'
        ),
    );

}
