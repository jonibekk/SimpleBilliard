<?php

class AddInviteType0215 extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'add_invite_type_0215';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up'   => array(
            'create_field' => array(
                'invites' => array(
                    'type' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 3, 'unsigned' => true, 'comment' => '招待タイプ(0:通常招待,1:一括登録)', 'after' => 'email_token_expires'),
                ),
            ),
        ),
        'down' => array(
            'drop_field' => array(
                'invites' => array('type'),
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
