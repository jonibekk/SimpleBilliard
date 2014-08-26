<?php

class AddSitePhotoToPostsComments0813 extends CakeMigration
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
                'comments' => array(
                    'site_photo_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'サイト画像', 'charset' => 'utf8', 'after' => 'site_info'),
                ),
                'posts'    => array(
                    'site_photo_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'サイト画像', 'charset' => 'utf8', 'after' => 'site_info'),
                ),
            ),
        ),
        'down' => array(
            'drop_field' => array(
                'comments' => array('site_photo_file_name',),
                'posts'    => array('site_photo_file_name',),
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
