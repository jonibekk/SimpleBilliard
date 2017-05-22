<?php

class AddTimezoneOnEvaluateTerms1013 extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'add_timezone_on_evaluate_terms_1013';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up'   => array(
            'create_field' => array(
                'evaluate_terms' => array(
                    'timezone' => array('type' => 'float', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '評価期間のタイムゾーン', 'after' => 'evaluate_status'),
                ),
            ),
        ),
        'down' => array(
            'drop_field' => array(
                'evaluate_terms' => array('timezone'),
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
        if ($direction == 'down') {
            return true;
        }
//        //一旦、全てのチームのタイムゾーンを9に設定
//        /**
//         * @var Term $EvaluateTerm
//         */
//        $EvaluateTerm = ClassRegistry::init('Term');
//        $EvaluateTerm->updateAll(['timezone' => 9]);

        return true;
    }
}
