<?php

class ChangeActionsAndActionResults1202 extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'change_actions_and_action_results_1202';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up'   => array(
            'create_field' => array(
                'action_results' => array(
                    'created_user_id'   => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '作成者ID(belongsToでUserモデルに関連)', 'after' => 'action_id'),
                    'completed_user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '完了者ID(belongsToでUserモデルに関連)', 'after' => 'created_user_id'),
                    'type'              => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => 'タイプ(0:user,1:goal,2:kr)', 'after' => 'completed_user_id'),
                    'completed_flg'     => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '完了フラグ', 'after' => 'completed'),
                    'photo1_file_name'  => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'アクションリザルト画像1', 'charset' => 'utf8', 'after' => 'completed_flg'),
                    'photo2_file_name'  => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'アクションリザルト画像2', 'charset' => 'utf8', 'after' => 'photo1_file_name'),
                    'photo3_file_name'  => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'アクションリザルト画像3', 'charset' => 'utf8', 'after' => 'photo2_file_name'),
                    'photo4_file_name'  => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'アクションリザルト画像4', 'charset' => 'utf8', 'after' => 'photo3_file_name'),
                    'photo5_file_name'  => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'アクションリザルト画像5', 'charset' => 'utf8', 'after' => 'photo4_file_name'),
                    'indexes'           => array(
                        'created_user_id'   => array('column' => 'created_user_id', 'unique' => 0),
                        'completed_user_id' => array('column' => 'completed_user_id', 'unique' => 0),
                    ),
                ),
                'actions'        => array(
                    'photo1_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'アクション画像1', 'charset' => 'utf8', 'after' => 'priority'),
                    'photo2_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'アクション画像2', 'charset' => 'utf8', 'after' => 'photo1_file_name'),
                    'photo3_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'アクション画像3', 'charset' => 'utf8', 'after' => 'photo2_file_name'),
                    'photo4_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'アクション画像4', 'charset' => 'utf8', 'after' => 'photo3_file_name'),
                    'photo5_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'アクション画像5', 'charset' => 'utf8', 'after' => 'photo4_file_name'),
                    'monthly_day'      => array('type' => 'integer', 'null' => false, 'default' => '1', 'unsigned' => false, 'comment' => '月次の日にち', 'after' => 'sun_flg'),
                ),
            ),
            'drop_field'   => array(
                'action_results' => array('user_id', 'photo_file_name', 'indexes' => array('user_id')),
                'actions'        => array('photo_file_name'),
            ),
            'alter_field'  => array(
                'actions' => array(
                    'repeat_type' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => '繰り返しタイプ(0:disabled,1:daily,2:weekly,4:monthly)'),
                ),
            ),
        ),
        'down' => array(
            'drop_field'   => array(
                'action_results' => array('created_user_id', 'completed_user_id', 'type', 'completed_flg', 'photo1_file_name', 'photo2_file_name', 'photo3_file_name', 'photo4_file_name', 'photo5_file_name', 'indexes' => array('created_user_id', 'completed_user_id')),
                'actions'        => array('photo1_file_name', 'photo2_file_name', 'photo3_file_name', 'photo4_file_name', 'photo5_file_name', 'monthly_day'),
            ),
            'create_field' => array(
                'action_results' => array(
                    'user_id'         => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '作成者ID(belongsToでUserモデルに関連)'),
                    'photo_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'アクションリザルト画像', 'charset' => 'utf8'),
                    'indexes'         => array(
                        'user_id' => array('column' => 'user_id', 'unique' => 0),
                    ),
                ),
                'actions'        => array(
                    'photo_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'アクション画像', 'charset' => 'utf8'),
                ),
            ),
            'alter_field'  => array(
                'actions' => array(
                    'repeat_type' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => '繰り返しタイプ(0:disabled,1:daily,2:weekly,3:weekday,4:monthly)'),
                ),
            ),
        ),
    );

    /**
     * Before migration callback
     *
     * @param string $direction Direction of migration process (up or down)
     *
     * @return bool Should process continue
     */
    public function before($direction)
    {
        return true;
    }

    /**
     * After migration callback
     *
     * @param string $direction Direction of migration process (up or down)
     *
     * @return bool Should process continue
     */
    public function after($direction)
    {
        return true;
    }
}
