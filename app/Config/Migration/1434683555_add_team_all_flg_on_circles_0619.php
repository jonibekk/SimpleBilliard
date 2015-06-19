<?php

class AddTeamAllFlgOnCircles0619 extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'add_team_all_flg_on_circles_0619';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up'   => array(
            'create_field' => array(
                'circles' => array(
                    'team_all_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => 'チーム全体フラグ(各チームに必須で１つ存在する)', 'after' => 'public_flg'),
                ),
            ),
        ),
        'down' => array(
            'drop_field' => array(
                'circles' => array('team_all_flg'),
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
