<?php

class AddColumnIntoNotifySettings1118 extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'add_column_into_notify_settings_1118';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up'   => array(
            'create_field' => array(
                'notify_settings' => array(
                    'my_evaluator_evaluated_app_flg'    => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '評価者が自分の評価をしたときのアプリ通知', 'after' => 'start_can_evaluate_as_evaluator_mobile_flg'),
                    'my_evaluator_evaluated_email_flg'  => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '評価者が自分の評価をしたときのメール通知', 'after' => 'my_evaluator_evaluated_app_flg'),
                    'my_evaluator_evaluated_mobile_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '評価者が自分の評価をしたときのモバイル通知', 'after' => 'my_evaluator_evaluated_email_flg'),
                ),
            ),
        ),
        'down' => array(
            'drop_field'  => array(
                'notify_settings' => array('my_evaluator_evaluated_app_flg', 'my_evaluator_evaluated_email_flg', 'my_evaluator_evaluated_mobile_flg'),
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
