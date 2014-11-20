<?php

/**
 * ActionFixture

 */
class ActionFixture extends CakeTestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = array(
        'id'              => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'アクションID'),
        'team_id'         => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
        'goal_id'         => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'ゴールID(belongsToでGoalモデルに関連)'),
        'key_result_id'   => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'キーリザルトID(belongsToでGoalモデルに関連)'),
        'user_id'         => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '作成者ID(belongsToでUserモデルに関連)'),
        'name'            => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '名前', 'charset' => 'utf8'),
        'description'     => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '説明', 'charset' => 'utf8'),
        'priority'        => array('type' => 'integer', 'null' => false, 'default' => '3', 'unsigned' => false, 'comment' => '重要度(1〜5)'),
        'start_date'      => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '開始日(unixtime)'),
        'end_date'        => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '終了日(unixtime)'),
        'repeat_type'     => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => '繰り返しタイプ(0:disabled,1:daily,2:weekly,3:weekday,4:monthly)'),
        'mon_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '月曜'),
        'tues_flg'        => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '火曜'),
        'wed_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '水曜'),
        'thurs_flg'       => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '木曜'),
        'fri_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '金曜'),
        'sat_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '土曜'),
        'sun_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '日曜'),
        'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => '削除フラグ'),
        'deleted'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '削除した日付時刻'),
        'created'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '追加した日付時刻'),
        'modified'        => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '更新した日付時刻'),
        'indexes'         => array(
            'PRIMARY'       => array('column' => 'id', 'unique' => 1),
            'team_id'       => array('column' => 'team_id', 'unique' => 0),
            'del_flg'       => array('column' => 'del_flg', 'unique' => 0),
            'goal_id'       => array('column' => 'goal_id', 'unique' => 0),
            'key_result_id' => array('column' => 'key_result_id', 'unique' => 0),
            'modified'      => array('column' => 'modified', 'unique' => 0),
            'user_id'       => array('column' => 'user_id', 'unique' => 0)
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
            'id'            => '',
            'team_id'       => '',
            'goal_id'       => '',
            'key_result_id' => '',
            'user_id'       => '',
            'name'          => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'description'   => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'priority'      => 1,
            'start_date'    => 1,
            'end_date'      => 1,
            'repeat_type'   => 1,
            'mon_flg'       => 1,
            'tues_flg'      => 1,
            'wed_flg'       => 1,
            'thurs_flg'     => 1,
            'fri_flg'       => 1,
            'sat_flg'       => 1,
            'sun_flg'       => 1,
            'del_flg'       => 1,
            'deleted'       => 1,
            'created'       => 1,
            'modified'      => 1
        ),
    );

}
