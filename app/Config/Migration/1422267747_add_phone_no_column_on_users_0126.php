<?php

class AddPhoneNoColumnOnUsers0126 extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'add_phone_no_column_on_users_0126';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up'   => array(
            'create_field' => array(
                'users' => array(
                    'phone_no' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20, 'collate' => 'utf8_general_ci', 'comment' => '電話番号', 'charset' => 'utf8', 'after' => 'hide_year_flg'),
                ),
            ),
        ),
        'down' => array(
            'drop_field' => array(
                'users' => array('phone_no'),
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
