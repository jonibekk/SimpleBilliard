<?php

class AddNotificationFlgOnCircleMembers1014 extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'add_notification_flg_on_circle_members_1014';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up'   => array(
            'create_field' => array(
                'circle_members' => array(
                    'get_notification_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '通知設定', 'after' => 'show_for_all_feed_flg'),
                ),
            ),
        ),
        'down' => array(
            'drop_field' => array(
                'circle_members' => array('get_notification_flg'),

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
