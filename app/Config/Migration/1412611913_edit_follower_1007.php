<?php

class EditFollower1007 extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = '';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up'   => array(
            'create_field' => array(
                'followers' => array(
                    'key_result_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'キーリザルトID(belongsToでKeyResultモデルに関連)', 'after' => 'team_id'),
                    'indexes'       => array(
                        'key_result_id' => array('column' => 'key_result_id', 'unique' => 0),
                    ),
                ),
            ),
            'drop_field'   => array(
                'followers' => array('goal_id', 'indexes' => array('goal_id')),
            ),
            'alter_field'  => array(
                'followers' => array(
                    'deleted'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '削除した日付時刻'),
                    'created'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '追加した日付時刻'),
                    'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '更新した日付時刻'),
                ),
            ),
        ),
        'down' => array(
            'drop_field'   => array(
                'followers' => array('key_result_id', 'indexes' => array('key_result_id')),
            ),
            'create_field' => array(
                'followers' => array(
                    'goal_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'ゴールID(belongsToでGoalモデルに関連)'),
                    'indexes' => array(
                        'goal_id' => array('column' => 'goal_id', 'unique' => 0),
                    ),
                ),
            ),
            'alter_field'  => array(
                'followers' => array(
                    'deleted'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'ゴールカテゴリを削除した日付時刻'),
                    'created'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'ゴールカテゴリを追加した日付時刻'),
                    'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'ゴールカテゴリを更新した日付時刻'),
                ),
            ),
        ),
    );

    /**
     * Before migration callback
     *
     * @param string $direction , up or down direction of migration process
     *
     * @return boolean Should process continue
     */
    public function before($direction)
    {
        return true;
    }

    /**
     * After migration callback
     *
     * @param string $direction , up or down direction of migration process
     *
     * @return boolean Should process continue
     */
    public function after($direction)
    {
        return true;
    }
}
