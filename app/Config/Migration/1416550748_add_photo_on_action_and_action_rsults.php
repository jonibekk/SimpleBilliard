<?php

class AddPhotoOnActionAndActionRsults extends CakeMigration
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
                'action_results' => array(
                    'photo_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'アクションリザルト画像', 'charset' => 'utf8', 'after' => 'completed'),
                    'note'            => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'ノート', 'charset' => 'utf8', 'after' => 'photo_file_name'),
                ),
                'actions'        => array(
                    'photo_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'アクション画像', 'charset' => 'utf8', 'after' => 'priority'),
                ),
                'goals'          => array(
                    'completed' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => true, 'after' => 'progress'),
                ),
            ),
            'drop_field'   => array(
                'goals' => array('compleated',),
            ),
        ),
        'down' => array(
            'create_field' => array(
                'goals' => array(
                    'compleated' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => true),
                ),
            ),
            'drop_field'   => array(
                'action_results' => array('photo_file_name', 'note',),
                'actions'        => array('photo_file_name',),
                'goals'          => array('completed',),
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
