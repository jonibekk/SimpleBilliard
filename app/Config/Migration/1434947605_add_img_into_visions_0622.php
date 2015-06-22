<?php

class AddImgIntoVisions0622 extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'add_img_into_visions_0622';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up'   => array(
            'create_field' => array(
                'group_visions' => array(
                    'photo_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '画像', 'charset' => 'utf8', 'after' => 'description'),
                ),
                'team_visions'  => array(
                    'photo_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '画像', 'charset' => 'utf8', 'after' => 'description'),
                ),
            ),
        ),
        'down' => array(
            'drop_field' => array(
                'group_visions' => array('photo_file_name'),
                'team_visions'  => array('photo_file_name'),
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
