<?php

class ChangeIndexPosts0718 extends CakeMigration
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
                'comments' => array(
                    'created' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'key' => 'primary', 'comment' => '投稿を追加した日付時刻'),
                ),
                'posts'    => array(
                    'modified' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'key' => 'primary', 'comment' => '投稿を更新した日付時刻'),
                ),
            ),
            'drop_field'   => array(
                'comments' => array('', 'indexes' => array('PRIMARY')),
                'posts'    => array('', 'indexes' => array('modified', 'PRIMARY')),
            ),
            'create_field' => array(
                'comments' => array(
                    'indexes' => array(
                        'PRIMARY' => array('column' => array('id', 'created'), 'unique' => 1),
                    ),
                ),
                'posts'    => array(
                    'indexes' => array(
                        'team_id_modified' => array('column' => array('team_id', 'modified'), 'unique' => 0),
                        'PRIMARY'          => array('column' => array('id', 'modified'), 'unique' => 1),
                    ),
                ),
            ),
        ),
        'down' => array(
            'alter_field'  => array(
                'comments' => array(
                    'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '投稿を追加した日付時刻'),
                ),
                'posts'    => array(
                    'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '投稿を更新した日付時刻'),
                ),
            ),
            'create_field' => array(
                'comments' => array(
                    'indexes' => array(
                        'PRIMARY' => array(),
                    ),
                ),
                'posts'    => array(
                    'indexes' => array(
                        'modified' => array('column' => 'modified', 'unique' => 0),
                        'PRIMARY'  => array(),
                    ),
                ),
            ),
            'drop_field'   => array(
                'comments' => array('', 'indexes' => array('PRIMARY')),
                'posts'    => array('', 'indexes' => array('team_id_modified', 'PRIMARY')),
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
