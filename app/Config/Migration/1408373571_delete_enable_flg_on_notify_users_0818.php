<?php

class DeleteEnableFlgOnNotifyUsers0818 extends CakeMigration
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
            'drop_field' => array(
                'notify_users' => array('enable_flg', 'indexes' => array('enable_flg')),
            ),
        ),
        'down' => array(
            'create_field' => array(
                'notify_users' => array(
                    'enable_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'key' => 'index', 'comment' => '有効フラグ(通知設定offの場合はfalse)'),
                    'indexes'    => array(
                        'enable_flg' => array('column' => 'enable_flg', 'unique' => 0),
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
