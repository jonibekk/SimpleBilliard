<?php

class TopicTkrDbModifyPart20909 extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'topic_tkr_db_modify_part2_0909';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up'   => array(
            'create_field' => array(
                'approval_histories' => array(
                    'is_clear_or_not'     => array(
                        'type'     => 'integer',
                        'null'     => false,
                        'default'  => '0',
                        'unsigned' => false,
                        'comment'  => '0:no select, 1:is clear, 2:is not clear',
                        'after'    => 'action_status'
                    ),
                    'is_important_or_not' => array(
                        'type'     => 'integer',
                        'null'     => false,
                        'default'  => '0',
                        'unsigned' => false,
                        'comment'  => '0:no select, 1:is important, 2:not important',
                        'after'    => 'is_clear_or_not'
                    ),
                ),
                'labels'             => array(
                    'goal_label_count' => array(
                        'type'     => 'integer',
                        'null'     => false,
                        'default'  => '0',
                        'unsigned' => true,
                        'comment'  => 'ゴールラベルのカウンタキャッシュ',
                        'after'    => 'name'
                    ),
                ),
            ),
            'alter_field'  => array(
                'labels' => array(
                    'name' => array(
                        'type'    => 'string',
                        'null'    => true,
                        'length'  => 128,
                        'collate' => 'utf8mb4_general_ci',
                        'comment' => 'ラベル',
                        'charset' => 'utf8mb4'
                    ),
                ),
            ),
            'rename_field' => array(
                'collaborators' => array(
                    'valued_flg' => 'approval_status'
                ),
            ),
        ),
        'down' => array(
            'drop_field'   => array(
                'approval_histories' => array('is_clear_or_not', 'is_important_or_not'),
                'labels'             => array('goal_label_count'),
            ),
            'rename_field' => array(
                'collaborators' => array(
                    'approval_status' => 'valued_flg'
                ),
            ),
            'alter_field'  => array(
                'labels' => array(
                    'name' => array('type'    => 'string',
                                    'null'    => false,
                                    'default' => null,
                                    'length'  => 128,
                                    'collate' => 'utf8mb4_general_ci',
                                    'comment' => 'ラベル',
                                    'charset' => 'utf8mb4'
                    ),
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
