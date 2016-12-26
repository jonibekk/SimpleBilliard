<?php
App::uses('CakeTestFixtureEx', 'Test/Fixture');

/**
 * KrChangeLogFixture
 */
class KrChangeLogFixture extends CakeTestFixtureEx
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = array(
        'id'                 => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'primary',
            'comment'  => 'ID'
        ),
        'team_id'            => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => 'チームID'
        ),
        'goal_id'            => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => 'ゴールID'
        ),
        'user_id'            => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'comment'  => 'ユーザーID'
        ),
        'key_result_id'      => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => 'キーリザルトID'
        ),
        'type' => array(
            'type'    => 'integer',
            'null'    => false,
            'unsigned' => true,
            'default' => '0',
            'comment' => '種別(0:KR編集時ログ, 1:コーチ認定時ログ)'
        ),
        'data'               => array(
            'type'    => 'binary',
            'null'    => false,
            'default' => null,
            'comment' => 'KRのスナップショット(MessagePackで圧縮)'
        ),
        'del_flg'            => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '0',
            'comment' => '削除フラグ'
        ),
        'deleted'            => array(
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => '削除した日付時刻'
        ),
        'created'            => array(
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => '追加した日付時刻'
        ),
        'modified'           => array(
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => '更新した日付時刻'
        ),
        'indexes'            => array(
            'PRIMARY'       => array('column' => 'id', 'unique' => 1),
            'team_id'       => array('column' => 'team_id', 'unique' => 0),
            'goal_id'       => array('column' => 'goal_id', 'unique' => 0),
            'modified'      => array('column' => 'modified', 'unique' => 0),
            'key_result_id' => array('column' => 'key_result_id', 'unique' => 0),
        ),
        'tableParameters'    => array(
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
