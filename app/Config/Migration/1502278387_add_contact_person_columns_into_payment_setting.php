<?php
class AddContactPersonColumnsIntoPaymentSetting extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_contact_person_columns_into_payment_setting';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'payment_settings' => array(
					'contact_person_first_name' => array('type' => 'string', 'null' => false, 'length' => 128, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Contact person.first name', 'charset' => 'utf8mb4', 'after' => 'company_tel'),
					'contact_person_first_name_kana' => array('type' => 'string', 'null' => true, 'length' => 128, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Contact person.first name kana', 'charset' => 'utf8mb4', 'after' => 'contact_person_first_name'),
					'contact_person_last_name' => array('type' => 'string', 'null' => false, 'length' => 128, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Contact person.last name', 'charset' => 'utf8mb4', 'after' => 'contact_person_first_name_kana'),
					'contact_person_last_name_kana' => array('type' => 'string', 'null' => true, 'length' => 128, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Contact person.last name kana', 'charset' => 'utf8mb4', 'after' => 'contact_person_last_name'),
					'contact_person_tel' => array('type' => 'string', 'null' => false, 'length' => 20, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Contact person.tel number', 'charset' => 'utf8mb4', 'after' => 'contact_person_last_name_kana'),
					'contact_person_email' => array('type' => 'string', 'null' => false, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Contact person.email address', 'charset' => 'utf8mb4', 'after' => 'contact_person_tel'),
				),
			),
			'drop_field' => array(
				'payment_settings' => array('payer_name', 'email'),
			),
			'alter_field' => array(
				'payment_settings' => array(
					'company_tel' => array('type' => 'string', 'null' => false, 'length' => 20, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Company tel number', 'charset' => 'utf8mb4'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'payment_settings' => array('contact_person_first_name', 'contact_person_first_name_kana', 'contact_person_last_name', 'contact_person_last_name_kana', 'contact_person_tel', 'contact_person_email'),
			),
			'create_field' => array(
				'payment_settings' => array(
					'payer_name' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Payer name', 'charset' => 'utf8mb4'),
					'email' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Payer email', 'charset' => 'utf8mb4'),
				),
			),
			'alter_field' => array(
				'payment_settings' => array(
					'company_tel' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Company tel number', 'charset' => 'utf8mb4'),
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
