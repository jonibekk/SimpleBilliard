<?php

class AddShowForAllFeedFlg extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'add_show_for_all_feed_flg';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up'   => array(
            'create_field' => array(
                'circle_members' => array(
                    'show_for_all_feed_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => 'オールフィード表示フラグ', 'after' => 'unread_count'),
                ),
            ),
        ),
        'down' => array(
            'drop_field' => array(
                'circle_members' => array('show_for_all_feed_flg'),
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
