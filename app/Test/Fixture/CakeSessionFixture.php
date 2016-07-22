<?php App::uses('CakeTestFixtureEx', 'Test/Fixture');

/**
 * CakeSessionFixture
 */

/** @noinspection PhpUndefinedClassInspection */
class CakeSessionFixture extends CakeTestFixtureEx
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = array(
        'id'              => array(
            'type'    => 'string',
            'null'    => false,
            'default' => null,
            'key'     => 'primary',
            'collate' => 'utf8mb4_general_ci',
            'charset' => 'utf8mb4'
        ),
        'data'            => array(
            'type'    => 'text',
            'null'    => false,
            'default' => null,
            'collate' => 'utf8mb4_general_ci',
            'charset' => 'utf8mb4'
        ),
        'expires'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
        'indexes'         => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
    );
}
