<?php
class AddColumnToActionResults extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_column_to_action_results';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'action_results' => array(
					'key_result_before_value' => array('type' => 'decimal', 'null' => true, 'default' => null, 'length' => '18,3', 'unsigned' => true, 'comment' => 'KR進捗値(更新前)', 'after' => 'key_result_id'),
					'key_result_change_value' => array('type' => 'decimal', 'null' => true, 'default' => null, 'length' => '18,3', 'unsigned' => false, 'comment' => 'KR進捗増減値', 'after' => 'key_result_before_value'),
					'key_result_target_value' => array('type' => 'decimal', 'null' => true, 'default' => null, 'length' => '18,3', 'unsigned' => true, 'comment' => 'KR進捗目標値', 'after' => 'key_result_change_value'),
				),
			),
			'alter_field' => array(
				'experiments' => array(
					'name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'key' => 'index', 'collate' => 'utf8mb4_general_ci', 'comment' => '実験の識別子', 'charset' => 'utf8mb4'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'action_results' => array('key_result_before_value', 'key_result_change_value', 'key_result_target_value'),
			),
			'alter_field' => array(
				'experiments' => array(
					'name' => array('type' => 'string', 'null' => false, 'length' => 128, 'key' => 'index', 'collate' => 'utf8mb4_general_ci', 'comment' => '実験の識別子', 'charset' => 'utf8mb4'),
				),
			),
		),
	);

/**
 * Before migration callback
 *
 * @param string $direction Direction of migration process (up or down)
 * @return bool Should process continue
 */
	public function before($direction) {
		return true;
	}

/**
 * After migration callback
 *
 * @param string $direction Direction of migration process (up or down)
 * @return bool Should process continue
 */
	public function after($direction) {
        if ($direction == 'up') {
            // KRの現在値を更新値と同じ値に更新
            $KeyResult = ClassRegistry::init('KeyResult');
            $krs = $KeyResult->find('all', [
                'conditions' => [
                    'completed' => null,
                    'start_value != current_value'
                ],
                'fields'     => [
                    'KeyResult.id',
                    'KeyResult.start_value',
                ],
            ]);
            foreach ($krs as &$v) {
                $v['KeyResult']['current_value'] = $v['KeyResult']['start_value'];
            }
            $KeyResult->saveAll($krs, ['validate' => false, 'deep' => false]);
        }
        return true;
	}
}
