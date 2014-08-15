<?php

class AddIndexModelIdOnNotifications0815 extends CakeMigration
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
            'alter_field'  => array(
                'notifications' => array(
                    'model_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'モデルID(feedならpost_id,circleならcircle_id)'),
                ),
            ),
            'create_field' => array(
                'notifications' => array(
                    'indexes' => array(
                        'model_id' => array('column' => 'model_id', 'unique' => 0),
                    ),
                ),
            ),
        ),
        'down' => array(
            'alter_field' => array(
                'notifications' => array(
                    'model_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'モデルID(feedならpost_id,circleならcircle_id)'),
                ),
            ),
            'drop_field'  => array(
                'notifications' => array('', 'indexes' => array('model_id')),
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
