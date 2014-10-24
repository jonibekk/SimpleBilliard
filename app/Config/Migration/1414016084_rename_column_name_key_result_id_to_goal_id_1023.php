<?php

class RenameColumnNameKeyResultIdToGoalId1023 extends CakeMigration
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
                    'goal_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'ゴールID(belongsToでGoalモデルに関連)', 'after' => 'team_id'),
                    'indexes' => array(
                        'goal_id' => array('column' => 'goal_id', 'unique' => 0),
                    ),
                ),
            ),
            'drop_field'   => array(
                'followers' => array('key_result_id', 'indexes' => array('key_result_id')),
            ),
        ),
        'down' => array(
            'drop_field'   => array(
                'followers' => array('goal_id', 'indexes' => array('goal_id')),
            ),
            'create_field' => array(
                'followers' => array(
                    'key_result_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'キーリザルトID(belongsToでKeyResultモデルに関連)'),
                    'indexes'       => array(
                        'key_result_id' => array('column' => 'key_result_id', 'unique' => 0),
                    ),
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
