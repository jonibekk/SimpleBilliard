<?php

class AddPostsCommentsPhotoColumn0722 extends CakeMigration
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
                    'photo1_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'コメント画像1', 'charset' => 'utf8', 'after' => 'comment_read_count'),
                    'photo2_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'コメント画像2', 'charset' => 'utf8', 'after' => 'photo1_file_name'),
                    'photo3_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'コメント画像3', 'charset' => 'utf8', 'after' => 'photo2_file_name'),
                    'photo4_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'コメント画像4', 'charset' => 'utf8', 'after' => 'photo3_file_name'),
                    'photo5_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'コメント画像5', 'charset' => 'utf8', 'after' => 'photo4_file_name'),
                ),
                'posts'    => array(
                    'photo1_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '投稿画像1', 'charset' => 'utf8', 'after' => 'goal_id'),
                    'photo2_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '投稿画像2', 'charset' => 'utf8', 'after' => 'photo1_file_name'),
                    'photo3_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '投稿画像3', 'charset' => 'utf8', 'after' => 'photo2_file_name'),
                    'photo4_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '投稿画像4', 'charset' => 'utf8', 'after' => 'photo3_file_name'),
                    'photo5_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '投稿画像5', 'charset' => 'utf8', 'after' => 'photo4_file_name'),
                ),
            ),
        ),
        'down' => array(
            'drop_field' => array(
                'comments' => array('photo1_file_name', 'photo2_file_name', 'photo3_file_name', 'photo4_file_name', 'photo5_file_name',),
                'posts'    => array('photo1_file_name', 'photo2_file_name', 'photo3_file_name', 'photo4_file_name', 'photo5_file_name',),
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
