<?php

/**
 * ActionResultFixture

 */
class ActionResultFixture extends CakeTestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = array(
        'id'                => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'アクションリザルトID'),
        'team_id'           => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
        'action_id'         => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'アクションID(belongsToでGoalモデルに関連)'),
        'created_user_id'   => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '作成者ID(belongsToでUserモデルに関連)'),
        'completed_user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '完了者ID(belongsToでUserモデルに関連)'),
        'type'              => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => 'タイプ(0:user,1:goal,2:kr)'),
        'scheduled'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '予定日'),
        'completed'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '完了日'),
        'completed_flg'     => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '完了フラグ'),
        'photo1_file_name'  => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'アクションリザルト画像1', 'charset' => 'utf8'),
        'photo2_file_name'  => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'アクションリザルト画像2', 'charset' => 'utf8'),
        'photo3_file_name'  => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'アクションリザルト画像3', 'charset' => 'utf8'),
        'photo4_file_name'  => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'アクションリザルト画像4', 'charset' => 'utf8'),
        'photo5_file_name'  => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'アクションリザルト画像5', 'charset' => 'utf8'),
        'note'              => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'ノート', 'charset' => 'utf8'),
        'del_flg'           => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => '削除フラグ'),
        'deleted'           => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '削除した日付時刻'),
        'created'           => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '追加した日付時刻'),
        'modified'          => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '更新した日付時刻'),
        'indexes'           => array(
            'PRIMARY'           => array('column' => 'id', 'unique' => 1),
            'team_id'           => array('column' => 'team_id', 'unique' => 0),
            'del_flg'           => array('column' => 'del_flg', 'unique' => 0),
            'action_id'         => array('column' => 'action_id', 'unique' => 0),
            'modified'          => array('column' => 'modified', 'unique' => 0),
            'created_user_id'   => array('column' => 'created_user_id', 'unique' => 0),
            'completed_user_id' => array('column' => 'completed_user_id', 'unique' => 0)
        ),
        'tableParameters'   => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );
}
