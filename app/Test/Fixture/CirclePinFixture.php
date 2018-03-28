<?php App::uses('CakeTestFixtureEx', 'Test/Fixture');

/**
 * CircleMemberFixture
 */
class CirclePinFixture extends CakeTestFixtureEx
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = array(
        'id'                    => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'primary',
            'comment'  => 'サークルメンバーID'
        ),
        'circle_orders'         => array(
            'type'     => 'string',
            'null'     => false,
            'comment'  => 'サークルID順番'
        ),
        'team_id'               => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => 'チームID'
        ),
        'user_id'               => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => 'ユーザID'
        ),
        'del_flg'               => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
        'deleted'               => array(
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => '部署を削除した日付時刻'
        ),
        'created'               => array(
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => '部署を追加した日付時刻'
        ),
        'modified'              => array(
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => '部署を更新した日付時刻'
        ),
        'indexes'               => array(
            'PRIMARY'   => array('column' => 'id', 'unique' => 1),
            'team_id'   => array('column' => 'team_id', 'unique' => 0),
            'user_id'   => array('column' => 'user_id', 'unique' => 0)
        ),
        'tableParameters'       => array(
            'charset' => 'utf8',
            'collate' => 'utf8_general_ci',
            'engine'  => 'InnoDB'
        )
    );
    /**
     * Records
     *
     * @var array
     */
    public $records = array(
        array(
            'id'           => 1,
            'circle_orders'    => '1,2,3,4',
            'team_id'      => 1,
            'user_id'      => 1,
            'del_flg'      => 0,
            'deleted'      => null,
            'created'      => 1,
            'modified'     => 1
        ),
        array(
            'id'           => 2,
            'circle_orders'    => '1,2,3,4',
            'team_id'      => 1,
            'user_id'      => 2,
            'del_flg'      => 0,
            'deleted'      => null,
            'created'      => 1,
            'modified'     => 1
        ),
        array(
            'id'           => 3,
            'circle_orders'    => '1,2,3,4',
            'team_id'      => 1,
            'user_id'      => 3,
            'del_flg'      => 0,
            'deleted'      => null,
            'created'      => 1,
            'modified'     => 1
        ),
        array(
            'id'           => 4,
            'circle_orders'    => '1,2,3,4',
            'team_id'      => 1,
            'user_id'      => 4,
            'del_flg'      => 0,
            'deleted'      => null,
            'created'      => 1,
            'modified'     => 1
        ),
        array(
            'id'           => 5,
            'circle_orders'    => '1,2,3,4',
            'team_id'      => 2,
            'user_id'      => 1,
            'del_flg'      => 0,
            'deleted'      => null,
            'created'      => 1,
            'modified'     => 1
        ),
        array(
            'id'           => 6,
            'circle_orders'    => '1,2,3,4',
            'team_id'      => 2,
            'user_id'      => 2,
            'del_flg'      => 0,
            'deleted'      => null,
            'created'      => 1,
            'modified'     => 1
        ),
        array(
            'id'           => 7,
            'circle_orders'    => '1,2,3,4',
            'team_id'      => 2,
            'user_id'      => 3,
            'del_flg'      => 0,
            'deleted'      => null,
            'created'      => 1,
            'modified'     => 1
        ),
        array(
            'id'           => 8,
            'circle_orders'    => '1,2,3,4',
            'team_id'      => 2,
            'user_id'      => 4,
            'del_flg'      => 0,
            'deleted'      => null,
            'created'      => 1,
            'modified'     => 1
        ),
        array(
            'id'           => 9,
            'circle_orders'    => '1,2,3,4',
            'team_id'      => 1,
            'user_id'      => 5,
            'del_flg'      => 0,
            'deleted'      => null,
            'created'      => 1,
            'modified'     => 1
        ),
        array(
            'id'           => 10,
            'circle_orders'    => '1,2,3,4',
            'team_id'      => 2,
            'user_id'      => 5,
            'del_flg'      => 0,
            'deleted'      => null,
            'created'      => 1,
            'modified'     => 1
        ),
    );

}
