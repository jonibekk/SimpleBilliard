<?php

class Add2faForUsers0322 extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'add_2fa_for_users_0322';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up'   => array(
            'create_field' => array(
                'users' => array(
                    '2fa_secret' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => '２要素認証シークレットキー', 'charset' => 'utf8', 'after' => 'password_token'),
                ),
            ),
        ),
        'down' => array(
            'drop_field' => array(
                'users' => array('2fa_secret'),
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
