<?php

class ChangeColumnTypeOfLangOnLocalNames0109 extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'change_column_type_of_lang_on_local_names_0109';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up'   => array(
            'alter_field'  => array(
                'local_names' => array(
                    'language' => array('type' => 'string', 'null' => false, 'length' => 3, 'key' => 'index', 'collate' => 'utf8_general_ci', 'comment' => '言語(日本語ならjpn)', 'charset' => 'utf8'),
                ),
            ),
            'create_field' => array(
                'local_names' => array(
                    'indexes' => array(
                        'language' => array('column' => 'language', 'unique' => 0),
                    ),
                ),
            ),
        ),
        'down' => array(
            'alter_field' => array(
                'local_names' => array(
                    'language' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => '言語(日本語ならjpn)', 'charset' => 'utf8'),
                ),
            ),
            'drop_field'  => array(
                'local_names' => array('indexes' => array('language')),
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
