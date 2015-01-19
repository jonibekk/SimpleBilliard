<?php

class ActionDbModified0010106 extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'action_db_modified_001_0106';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up'   => array(
            'create_field' => array(
                'action_results' => array(
                    'goal_id'       => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'ゴールID(belongsToでGoalモデルに関連)', 'after' => 'action_id'),
                    'key_result_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'キーリザルトID(belongsToでGoalモデルに関連)', 'after' => 'goal_id'),
                    'name'          => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '名前', 'charset' => 'utf8', 'after' => 'completed_user_id'),
                    'indexes'       => array(
                        'goal_id'       => array('column' => 'goal_id', 'unique' => 0),
                        'key_result_id' => array('column' => 'key_result_id', 'unique' => 0),
                    ),
                ),
            ),
        ),
        'down' => array(
            'drop_field' => array(
                'action_results' => array('goal_id', 'key_result_id', 'name', 'indexes' => array('goal_id', 'key_result_id')),
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
        if ($direction == 'up') {
            //データ移行
            /**
             * @var Action $Action
             */
            $Action = $this->generateModel('Action');
            $actions = $Action->find('all', ['fields' => ['id', 'goal_id', 'key_result_id', 'name']]);
            /**
             * @var ActionResult $ActionResult
             */
            $ActionResult = $this->generateModel('ActionResult');

            foreach ($actions as $action) {
                if (isset($action['Action']['id']) && !empty($action['Action']['id'])) {
                    $action_id = $action['Action']['id'];
                    $fields = [
                        'ActionResult.name'          => "'" . $action['Action']['name'] . "'",
                        'ActionResult.goal_id'       => $action['Action']['goal_id'],
                        'ActionResult.key_result_id' => $action['Action']['key_result_id'],
                    ];
                    $ActionResult->recursive = -1;
                    $ActionResult->updateAll($fields, ['ActionResult.action_id' => $action_id]);
                }
            }
        }
        return true;
    }
}
