<?php

class ChangeFieldNameOnAttachedFiles0727 extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'change_field_name_on_attached_files_0727';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up'   => array(
            'create_field' => array(
                'attached_files' => array(
                    'attached_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'ファイル名', 'charset' => 'utf8', 'after' => 'team_id'),
                ),
            ),
            'drop_field'   => array(
                'attached_files' => array('file_name'),
            ),
        ),
        'down' => array(
            'drop_field'   => array(
                'attached_files' => array('attached_file_name'),
            ),
            'create_field' => array(
                'attached_files' => array(
                    'file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'ファイル名', 'charset' => 'utf8'),
                ),
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
