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
        'id'                  => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'アクションID'),
        'team_id'             => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
        'goal_id'             => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'ゴールID(belongsToでGoalモデルに関連)'),
        'key_result_id'       => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'キーリザルトID(belongsToでGoalモデルに関連)'),
        'user_id'             => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '作成者ID(belongsToでUserモデルに関連)'),
        'name'                => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '名前', 'charset' => 'utf8'),
        'description'         => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '説明', 'charset' => 'utf8'),
        'priority'            => array('type' => 'integer', 'null' => false, 'default' => '3', 'unsigned' => false, 'comment' => '重要度(1〜5)'),
        'photo1_file_name'    => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'アクション画像1', 'charset' => 'utf8'),
        'photo2_file_name'    => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'アクション画像2', 'charset' => 'utf8'),
        'photo3_file_name'    => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'アクション画像3', 'charset' => 'utf8'),
        'photo4_file_name'    => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'アクション画像4', 'charset' => 'utf8'),
        'photo5_file_name'    => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'アクション画像5', 'charset' => 'utf8'),
        'start_date'          => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '開始日(unixtime)'),
        'end_date'            => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '終了日(unixtime)'),
        'repeat_type'         => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => '繰り返しタイプ(0:disabled,1:daily,2:weekly,4:monthly)'),
        'mon_flg'             => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '月曜'),
        'tues_flg'            => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '火曜'),
        'wed_flg'             => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '水曜'),
        'thurs_flg'           => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '木曜'),
        'fri_flg'             => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '金曜'),
        'sat_flg'             => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '土曜'),
        'sun_flg'             => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '日曜'),
        'monthly_day'         => array('type' => 'integer', 'null' => false, 'default' => '1', 'unsigned' => false, 'comment' => '月次の日にち'),
        'action_result_count' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => 'アクションリザルトカウント'),
        'del_flg'             => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => '削除フラグ'),
        'deleted'             => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '削除した日付時刻'),
        'created'             => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '追加した日付時刻'),
        'modified'            => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '更新した日付時刻'),
        'indexes'             => array(
            'PRIMARY'       => array('column' => 'id', 'unique' => 1),
            'team_id'       => array('column' => 'team_id', 'unique' => 0),
            'del_flg'       => array('column' => 'del_flg', 'unique' => 0),
            'goal_id'       => array('column' => 'goal_id', 'unique' => 0),
            'key_result_id' => array('column' => 'key_result_id', 'unique' => 0),
            'modified'      => array('column' => 'modified', 'unique' => 0),
            'user_id'       => array('column' => 'user_id', 'unique' => 0)
        ),
        'tableParameters'     => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );
}
