<?php

class AddShareTypeOnShareTables0710 extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'add_share_type_on_share_tables_0710';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up'   => array(
            'create_field' => array(
                'post_share_circles' => array(
                    'share_type' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => '共有タイプ(0:shared, 1:only_notify)', 'after' => 'team_id'),
                ),
                'post_share_users'   => array(
                    'share_type' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => '共有タイプ(0:shared, 1:only_notify)', 'after' => 'team_id'),
                ),
            ),
        ),
        'down' => array(
            'drop_field' => array(
                'post_share_circles' => array('share_type'),
                'post_share_users'   => array('share_type'),
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
