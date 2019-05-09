<?php App::uses('CakeTestFixtureEx', 'Test/Fixture');

/**
 * TermFixture
 */
class TermFixture extends CakeTestFixtureEx
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
            'key'      => 'primary',
            'comment'  => 'ID'
        ),
        'team_id'         => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => 'チームID'
        ),
        'start_date'      => array(
            'type'    => 'date',
            'null'    => false,
            'default' => null,
            'key'     => 'index',
            'comment' => '期開始日'
        ),
        'end_date'        => array(
            'type'    => 'date',
            'null'    => false,
            'default' => null,
            'key'     => 'index',
            'comment' => '期終了日'
        ),
        'evaluate_status' => array(
            'type'     => 'integer',
            'null'     => false,
            'default'  => '0',
            'unsigned' => false,
            'comment'  => '評価ステータス(0 = 評価開始前, 1 = 評価中,2 = 評価凍結中, 3 = 最終評価終了)'
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
            'PRIMARY'    => array('column' => 'id', 'unique' => 1),
            'team_id'    => array('column' => 'team_id', 'unique' => 0),
            'start_date' => array('column' => 'start_date', 'unique' => 0),
            'end_date'   => array('column' => 'end_date', 'unique' => 0)
        ),
        'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
    );

    /**
     * Records
     *
     * @var array
     */
    public $records = [
        [
            'id'              => 1,
            'team_id'         => 1,
            'start_date'      => 10000,
            'end_date'        => 19999,
            'evaluate_status' => true,
            'del_flg'         => false,
            'created'         => 1,
            'modified'        => 1
        ],
        [
            'id'              => 2,
            'team_id'         => 1,
            'start_date'      => 20000,
            'end_date'        => 29999,
            'evaluate_status' => true,
            'del_flg'         => false,
            'created'         => 1,
            'modified'        => 1
        ],
    ];

}
