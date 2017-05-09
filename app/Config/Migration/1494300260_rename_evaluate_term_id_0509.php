<?php
class RenameEvaluateTermId0509 extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'rename_evaluate_term_id_0509';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'alter_field' => array(
				'teams' => array(
					'timezone' => array('type' => 'float', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => 'チームのタイムゾーン'),
				),
			),
			'drop_field' => array(
				'evaluations' => array('indexes' => array('evaluate_term_id')),
			),
            'rename_field' => array(
                'evaluations'       => array('evaluate_term_id' => 'term_id'),
            ),
            'create_field' => array(
                'evaluations' => array(
                    'indexes' => array(
                        'term_id' => array('column' => 'term_id', 'unique' => 0),
                    ),
                ),
            ),
        ),
		'down' => array(
			'alter_field' => array(
				'teams' => array(
					'timezone' => array('type' => 'float', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'チームのタイムゾーン'),
				),
			),
            'drop_field' => array(
                'evaluations' => array('indexes' => array('term_id')),
            ),
            'rename_field' => array(
                'evaluations'       => array('term_id' => 'evaluate_term_id'),
            ),
			'create_field' => array(
				'evaluations' => array(
					'indexes' => array(
						'evaluate_term_id' => array('column' => 'evaluate_term_id', 'unique' => 0),
					),
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
		return true;
	}
}
