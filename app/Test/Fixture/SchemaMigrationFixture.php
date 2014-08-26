<?php

/**
 * SchemaMigrationFixture

 */
class SchemaMigrationFixture extends CakeTestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = array(
        'id'              => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
        'class'           => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'type'            => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 50, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'created'         => array('type' => 'datetime', 'null' => false, 'default' => null),
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
            'id'      => 1,
            'class'   => 'Lorem ipsum dolor sit amet',
            'type'    => 'Lorem ipsum dolor sit amet',
            'created' => '2014-07-08 15:07:26'
        ),
        array(
            'id'      => 2,
            'class'   => 'Lorem ipsum dolor sit amet',
            'type'    => 'Lorem ipsum dolor sit amet',
            'created' => '2014-07-08 15:07:26'
        ),
        array(
            'id'      => 3,
            'class'   => 'Lorem ipsum dolor sit amet',
            'type'    => 'Lorem ipsum dolor sit amet',
            'created' => '2014-07-08 15:07:26'
        ),
        array(
            'id'      => 4,
            'class'   => 'Lorem ipsum dolor sit amet',
            'type'    => 'Lorem ipsum dolor sit amet',
            'created' => '2014-07-08 15:07:26'
        ),
        array(
            'id'      => 5,
            'class'   => 'Lorem ipsum dolor sit amet',
            'type'    => 'Lorem ipsum dolor sit amet',
            'created' => '2014-07-08 15:07:26'
        ),
        array(
            'id'      => 6,
            'class'   => 'Lorem ipsum dolor sit amet',
            'type'    => 'Lorem ipsum dolor sit amet',
            'created' => '2014-07-08 15:07:26'
        ),
        array(
            'id'      => 7,
            'class'   => 'Lorem ipsum dolor sit amet',
            'type'    => 'Lorem ipsum dolor sit amet',
            'created' => '2014-07-08 15:07:26'
        ),
        array(
            'id'      => 8,
            'class'   => 'Lorem ipsum dolor sit amet',
            'type'    => 'Lorem ipsum dolor sit amet',
            'created' => '2014-07-08 15:07:26'
        ),
        array(
            'id'      => 9,
            'class'   => 'Lorem ipsum dolor sit amet',
            'type'    => 'Lorem ipsum dolor sit amet',
            'created' => '2014-07-08 15:07:26'
        ),
        array(
            'id'      => 10,
            'class'   => 'Lorem ipsum dolor sit amet',
            'type'    => 'Lorem ipsum dolor sit amet',
            'created' => '2014-07-08 15:07:26'
        ),
    );

}
