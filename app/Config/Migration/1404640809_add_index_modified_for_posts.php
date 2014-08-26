<?php

class AddIndexModifiedForPosts extends CakeMigration
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
            'alter_field'  => array(
                'posts' => array(
                    'modified' => array('type' => 'datetime', 'null' => true, 'default' => null, 'key' => 'index', 'comment' => '投稿を更新した日付時刻'),
                ),
            ),
            'create_field' => array(
                'posts' => array(
                    'indexes' => array(
                        'modified' => array('column' => 'modified', 'unique' => 0),
                    ),
                ),
            ),
        ),
        'down' => array(
            'alter_field' => array(
                'posts' => array(
                    'modified' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '投稿を更新した日付時刻'),
                ),
            ),
            'drop_field'  => array(
                'posts' => array('', 'indexes' => array('modified')),
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
