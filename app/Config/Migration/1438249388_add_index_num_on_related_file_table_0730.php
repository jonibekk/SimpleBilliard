<?php

class AddIndexNumOnRelatedFileTable0730 extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'add_index_num_on_related_file_table_0730';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up'   => array(
            'create_field' => array(
                'action_result_files' => array(
                    'index_num' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => '表示順', 'after' => 'team_id'),
                ),
                'attached_files'      => array(
                    'display_file_list_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => 'ファイル一覧に表示するフラグ', 'after' => 'model_type'),
                    'removable_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '削除可能フラグ', 'after' => 'display_file_list_flg'),
                ),
                'comment_files'       => array(
                    'index_num' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => '表示順', 'after' => 'team_id'),
                ),
                'post_files'          => array(
                    'index_num' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => '表示順', 'after' => 'team_id'),
                ),
            ),
        ),
        'down' => array(
            'drop_field' => array(
                'action_result_files' => array('index_num'),
                'attached_files'      => array('display_file_list_flg', 'removable_flg'),
                'comment_files'       => array('index_num'),
                'post_files'          => array('index_num'),
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
