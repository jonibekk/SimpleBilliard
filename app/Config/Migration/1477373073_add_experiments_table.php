<?php

class AddExperimentsTable extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'add_experiments_table';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up'   => array(
            'create_table' => array(
                'experiments' => array(
                    'id'              => array(
                        'type'     => 'biginteger',
                        'null'     => false,
                        'default'  => null,
                        'unsigned' => true,
                        'key'      => 'primary'
                    ),
                    'name'            => array(
                        'type'    => 'string',
                        'null'    => false,
                        'length'  => 128,
                        'key'     => 'index',
                        'collate' => 'utf8mb4_general_ci',
                        'comment' => '実験の識別子',
                        'charset' => 'utf8mb4'
                    ),
                    'team_id'         => array(
                        'type'     => 'biginteger',
                        'null'     => false,
                        'default'  => null,
                        'unsigned' => true,
                        'key'      => 'index',
                        'comment'  => 'チームID(belongsToでTeamモデルに関連)'
                    ),
                    'del_flg'         => array(
                        'type'    => 'boolean',
                        'null'    => false,
                        'default' => '0',
                        'comment' => '削除フラグ'
                    ),
                    'deleted'         => array(
                        'type'     => 'integer',
                        'null'     => true,
                        'default'  => null,
                        'unsigned' => true
                    ),
                    'created'         => array(
                        'type'     => 'integer',
                        'null'     => true,
                        'default'  => null,
                        'unsigned' => true
                    ),
                    'modified'        => array(
                        'type'     => 'integer',
                        'null'     => true,
                        'default'  => null,
                        'unsigned' => true
                    ),
                    'indexes'         => array(
                        'PRIMARY' => array('column' => 'id', 'unique' => 1),
                        'team_id' => array('column' => 'team_id', 'unique' => 0),
                        'name'    => array('column' => 'name', 'unique' => 0),
                    ),
                    'tableParameters' => array(
                        'charset' => 'utf8mb4',
                        'collate' => 'utf8mb4_general_ci',
                        'engine'  => 'InnoDB'
                    ),
                ),
            ),
        ),
        'down' => array(
            'drop_table' => array(
                'experiments'
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
