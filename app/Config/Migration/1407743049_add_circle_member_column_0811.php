<?php

class AddCircleMemberColumn0811 extends CakeMigration
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
                'circles' => array(
                    'member_count' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => 'メンバー数', 'after' => 'public_flg'),
                ),
            ),
        ),
        'down' => array(
            'drop_field' => array(
                'circles' => array('member_count',),
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
