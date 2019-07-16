<?php

class AddedTranslationTable extends CakeMigration
{
    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'added_translation_table';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up'   => array(
            'create_field' => array(
                'comments' => array(
                    'language' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 10, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Detected language of the comment body', 'charset' => 'utf8mb4', 'after' => 'body'),
                ),
                'posts'    => array(
                    'language' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 10, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Detected language of the post body', 'charset' => 'utf8mb4', 'after' => 'body'),
                ),
            ),
            'create_table' => array(
                'translations' => array(
                    'id'              => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
                    'content_type'    => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 4, 'unsigned' => false, 'key' => 'index', 'comment' => 'Translation content type'),
                    'content_id'      => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'comment' => 'Translation content ID'),
                    'body'            => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Translation content', 'charset' => 'utf8mb4'),
                    'language'        => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 10, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Translation language', 'charset' => 'utf8mb4'),
                    'status'          => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 4, 'unsigned' => false, 'comment' => 'Translation status'),
                    'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '0'),
                    'deleted'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
                    'created'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
                    'modified'        => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
                    'indexes'         => array(
                        'PRIMARY'      => array('column' => 'id', 'unique' => 1),
                        'content_type' => array('column' => array('content_type', 'content_id', 'language'), 'unique' => 1),
                    ),
                    'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB'),
                ),
            ),
        ),
        'down' => array(
            'drop_field' => array(
                'comments' => array('language'),
                'posts'    => array('language'),
            ),
            'drop_table' => array(
                'translations'
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
