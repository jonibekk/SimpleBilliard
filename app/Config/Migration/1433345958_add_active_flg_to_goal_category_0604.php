<?php

class AddActiveFlgToGoalCategory0604 extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'add_active_flg_to_goal_category_0604';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up'   => array(
            'create_field' => array(
                'goal_categories' => array(
                    'active_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '有効フラグ', 'after' => 'description'),
                ),
            ),
        ),
        'down' => array(
            'drop_field' => array(
                'goal_categories' => array('active_flg'),
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
