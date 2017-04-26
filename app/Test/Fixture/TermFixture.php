<?php App::uses('CakeTestFixtureEx', 'Test/Fixture');

/**
 * EvaluateTermFixture
 */
class EvaluateTermFixture extends CakeTestFixtureEx
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
            'comment'  => 'チームID(belongsToでTeamモデルに関連)'
        ),
        'start_date'      => array(
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => '評価対象期間の開始日'
        ),
        'end_date'        => array(
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => '評価対象期間の終了日'
        ),
        'evaluate_status' => array('type'     => 'integer',
                                   'null'     => false,
                                   'default'  => '0',
                                   'unsigned' => false,
                                   'comment'  => '評価ステータス(0 = 評価開始前, 1 = 評価中,2 = 評価凍結中, 3 = 最終評価終了)'
        ),
        'timezone'        => array('type'     => 'float',
                                   'null'     => true,
                                   'default'  => null,
                                   'unsigned' => false,
                                   'comment'  => '評価期間のタイムゾーン'
        ),
        'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
        'deleted'         => array('type'     => 'integer',
                                   'null'     => true,
                                   'default'  => null,
                                   'unsigned' => true,
                                   'comment'  => '削除した日付時刻'
        ),
        'created'         => array('type'     => 'integer',
                                   'null'     => true,
                                   'default'  => null,
                                   'unsigned' => true,
                                   'key'      => 'index',
                                   'comment'  => '追加した日付時刻'
        ),
        'modified'        => array('type'     => 'integer',
                                   'null'     => true,
                                   'default'  => null,
                                   'unsigned' => true,
                                   'comment'  => '更新した日付時刻'
        ),
        'indexes'         => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1),
            'team_id' => array('column' => 'team_id', 'unique' => 0),
            'created' => array('column' => 'created', 'unique' => 0)
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
            'id'         => 1,
            'team_id'    => 1,
            'start_date' => 10000,
            'end_date'   => 19999,
            'timezone'   => 9,
            'created'    => 1,
            'modified'   => 1
        ],
        [
            'id'         => 2,
            'team_id'    => 1,
            'start_date' => 20000,
            'end_date'   => 29999,
            'timezone'   => 9,
            'created'    => 1,
            'modified'   => 1
        ],
    ];

}
