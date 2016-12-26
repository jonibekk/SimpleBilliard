<?php
App::uses('CakeTestFixtureEx', 'Test/Fixture');

/**
 * KrProgressLogFixture
 */
class KrProgressLogFixture extends CakeTestFixtureEx
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = array(
        'id'               => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'primary',
            'comment'  => 'ID'
        ),
        'team_id'          => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => 'チームID'
        ),
        'goal_id'          => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => 'ゴールID'
        ),
        'user_id'          => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'comment'  => 'ユーザーID'
        ),
        'key_result_id'    => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => 'キーリザルトID'
        ),
        'action_result_id' => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'unique',
            'comment'  => 'アクションID'
        ),
        'value_unit'       => array(
            'type'     => 'integer',
            'null'     => false,
            'default'  => '0',
            'unsigned' => true,
            'comment'  => '進捗単位'
        ),
        'before_value'     => array(
            'type'     => 'decimal',
            'null'     => true,
            'default'  => null,
            'length'   => '18,3',
            'unsigned' => true,
            'comment'  => '更新前進捗値'
        ),
        'change_value'     => array(
            'type'     => 'decimal',
            'null'     => true,
            'default'  => null,
            'length'   => '18,3',
            'unsigned' => false,
            'comment'  => '進捗増減値'
        ),
        'target_value'     => array(
            'type'     => 'decimal',
            'null'     => true,
            'default'  => null,
            'length'   => '18,3',
            'unsigned' => true,
            'comment'  => '進捗目標値'
        ),
        'del_flg'          => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '0',
            'comment' => '削除フラグ'
        ),
        'deleted'          => array(
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => '削除した日付時刻'
        ),
        'created'          => array(
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => '追加した日付時刻'
        ),
        'modified'         => array(
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => '更新した日付時刻'
        ),
        'indexes'          => array(
            'PRIMARY'          => array('column' => 'id', 'unique' => 1),
            'action_result_id' => array('column' => 'action_result_id', 'unique' => 1),
            'team_id'          => array('column' => 'team_id', 'unique' => 0),
            'goal_id'          => array('column' => 'goal_id', 'unique' => 0),
            'key_result_id'    => array('column' => 'key_result_id', 'unique' => 0),
            'created'          => array('column' => 'created', 'unique' => 0),
        ),
        'tableParameters'  => array(
            'charset' => 'utf8mb4',
            'collate' => 'utf8mb4_general_ci',
            'engine'  => 'InnoDB'
        ),
    );

    /**
     * Records
     *
     * @var array
     */
    public $records = [
    ];

}
