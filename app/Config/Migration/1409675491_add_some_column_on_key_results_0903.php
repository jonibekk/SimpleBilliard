<?php

class AddSomeColumnOnKeyResults0903 extends CakeMigration
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
                'key_results' => array(
                    'end_date'     => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '終了日(unixtime)', 'after' => 'start_date'),
                    'start_value'  => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => '開始値', 'after' => 'current_value'),
                    'target_value' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => '目標値', 'after' => 'start_value'),
                    'progress'     => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => '進捗%', 'after' => 'value_unit'),
                    'completed'    => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '完了日', 'after' => 'progress'),
                ),
            ),
            'drop_field'   => array(
                'key_results' => array('due_date', 'desired_value', 'compleated',),
            ),
            'alter_field'  => array(
                'key_results' => array(
                    'name'          => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '名前', 'charset' => 'utf8'),
                    'start_date'    => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '開始日(unixtime)'),
                    'current_value' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => '現在値'),
                    'value_unit'    => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => '目標値の単位'),
                ),
            ),
        ),
        'down' => array(
            'drop_field'   => array(
                'key_results' => array('end_date', 'start_value', 'target_value', 'progress', 'completed',),
            ),
            'create_field' => array(
                'key_results' => array(
                    'due_date'      => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '期限(unixtime)'),
                    'desired_value' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '目標値'),
                    'compleated'    => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '完了日'),
                ),
            ),
            'alter_field'  => array(
                'key_results' => array(
                    'name'          => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '名前', 'charset' => 'utf8'),
                    'start_date'    => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '開始日'),
                    'current_value' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '現在値'),
                    'value_unit'    => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '目標値の単位'),
                ),
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
