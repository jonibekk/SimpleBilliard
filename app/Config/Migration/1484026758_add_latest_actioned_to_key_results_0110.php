<?php

class AddLatestActionedToKeyResults0110 extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'add_latest_actioned_to_key_results_0110';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up'   => array(
            'create_field' => array(
                'key_results' => array(
                    'latest_actioned' => array(
                        'type'     => 'integer',
                        'null'     => true,
                        'default'  => null,
                        'unsigned' => true,
                        'key'      => 'index',
                        'comment'  => '最新アクション日時(unixtime)',
                        'after'    => 'action_result_count'
                    ),
                    'indexes'         => array(
                        'latest_actioned' => array('column' => 'latest_actioned', 'unique' => 0),
                    ),
                ),
            ),
        ),
        'down' => array(
            'drop_field' => array(
                'key_results' => array('latest_actioned', 'indexes' => array('latest_actioned')),
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
            /* 全KRのアクション最新日時を更新 */
            $ActionResult = ClassRegistry::init('ActionResult');
            // MAX関数をかけたフィールドは結果の配列がおかしくなるのでvirtualFieldを使用して、配列をきれいにする
            // 参考:https://ah-2.com/2012/02/19/cakephp_virtual_fields.html
            $ActionResult->virtualFields['latest_actioned'] = 0;
            $options = [
                'conditions' => [
                    'ActionResult.key_result_id !=' => null,
                ],
                'fields'     => [
                    'ActionResult.key_result_id as id',
                    'MAX(ActionResult.created) as ActionResult__latest_actioned',
                ],
                'group'      => [
                    'ActionResult.key_result_id'
                ],
            ];
            $updateKrs = Hash::extract($ActionResult->find('all', $options), '{n}.ActionResult');
            if (empty($updateKrs)) {
                return true;
            }
            $KeyResult = ClassRegistry::init('KeyResult');
            $KeyResult->saveAll($updateKrs, ['validate' => false, 'deep' => false]);
        }

        return true;
    }
}
