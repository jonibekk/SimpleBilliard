<?php

class AddIndexOnTeamMemter0126 extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'add_index_on_team_memter_0126';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up'   => array(
            'alter_field'  => array(
                'team_members' => array(
                    'member_no' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36, 'key' => 'index', 'collate' => 'utf8_general_ci', 'comment' => 'メンバーナンバー(組織内でメンバーを識別する為のナンバー。exp社員番号)', 'charset' => 'utf8'),
                ),
            ),
            'create_field' => array(
                'team_members' => array(
                    'indexes' => array(
                        'member_no' => array('column' => 'member_no', 'unique' => 0),
                    ),
                ),
            ),
        ),
        'down' => array(
            'alter_field' => array(
                'team_members' => array(
                    'member_no' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'メンバーナンバー(組織内でメンバーを識別する為のナンバー。exp社員番号)', 'charset' => 'utf8'),
                ),
            ),
            'drop_field'  => array(
                'team_members' => array('indexes' => array('member_no')),
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
