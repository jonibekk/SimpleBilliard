<?php
App::uses('CakeTestFixtureEx', 'Test/Fixture');

/**
 * @class PostResource
 */
class PostResourceFixture extends CakeTestFixtureEx
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = array(
        'id'              => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'primary'
        ),
        'post_id'         => array(
            'type'     => 'biginteger',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => '= posts.id'
        ),
        'post_draft_id'   => array(
            'type'     => 'biginteger',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => '= post_drafts.id'
        ),
        'resource_type'   => array(
            'type'     => 'integer',
            'null'     => false,
            'default'  => '0',
            'length'   => 3,
            'unsigned' => true,
            'comment'  => 'type of resource e.g. image, video, ...'
        ),
        'resource_id'     => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'comment'  => 'resource table\'s primary key id'
        ),
        'resource_order'   => array(
            'type'     => 'integer',
            'null'     => false,
            'default'  => '0',
            'unsigned' => true,
        ),
        'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '0'),
        'created'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
        'modified'        => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
        'deleted'         => array(
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => '削除した日付時刻'
        ),
        'indexes'         => array(
            'PRIMARY'       => array('column' => 'id', 'unique' => 1),
            'post_id'       => array('column' => 'post_id', 'unique' => 0),
            'post_draft_id' => array('column' => 'post_draft_id', 'unique' => 0),
        ),
        'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB'),
    );

    public $records = [
    ];

}
