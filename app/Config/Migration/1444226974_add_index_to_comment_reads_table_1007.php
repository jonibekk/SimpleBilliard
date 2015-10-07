<?php

class AddIndexToCommentReadsTable1007 extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'add_index_to_comment_reads_table_1007';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up'   => array(
            'alter_field'  => array(
                'comment_reads' => array(
                    'user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '読んだしたユーザID(belongsToでUserモデルに関連)'),
                ),
            ),
            'create_field' => array(
                'comment_reads' => array(
                    'indexes' => array(
                        'comment_id' => array('column' => 'comment_id', 'unique' => 0),
                        'user_id'    => array('column' => 'user_id', 'unique' => 0),
                    ),
                ),
            ),
        ),
        'down' => array(
            'alter_field' => array(
                'comment_reads' => array(
                    'user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'comment' => '読んだしたユーザID(belongsToでUserモデルに関連)'),
                ),
            ),
            'drop_field'  => array(
                'comment_reads' => array('indexes' => array('comment_id', 'user_id')),
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
