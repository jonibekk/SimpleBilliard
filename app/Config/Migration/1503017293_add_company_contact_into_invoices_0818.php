<?php
class AddCompanyContactIntoInvoices0818 extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_company_contact_into_invoices_0818';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'invoices' => array(
					'company_name' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Company name', 'charset' => 'utf8mb4', 'after' => 'credit_status'),
					'company_post_code' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 16, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Company address(post_code)', 'charset' => 'utf8mb4', 'after' => 'company_name'),
					'company_region' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Company address(region)', 'charset' => 'utf8mb4', 'after' => 'company_post_code'),
					'company_city' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Company address(city)', 'charset' => 'utf8mb4', 'after' => 'company_region'),
					'company_street' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Company address(street)', 'charset' => 'utf8mb4', 'after' => 'company_city'),
					'contact_person_first_name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Contact person.first name', 'charset' => 'utf8mb4', 'after' => 'company_street'),
					'contact_person_first_name_kana' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 128, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Contact person.first name kana', 'charset' => 'utf8mb4', 'after' => 'contact_person_first_name'),
					'contact_person_last_name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Contact person.last name', 'charset' => 'utf8mb4', 'after' => 'contact_person_first_name_kana'),
					'contact_person_last_name_kana' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 128, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Contact person.last name kana', 'charset' => 'utf8mb4', 'after' => 'contact_person_last_name'),
					'contact_person_tel' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 20, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Contact person.tel number', 'charset' => 'utf8mb4', 'after' => 'contact_person_last_name_kana'),
					'contact_person_email' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Contact person.email address', 'charset' => 'utf8mb4', 'after' => 'contact_person_tel'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'invoices' => array('company_name', 'company_post_code', 'company_region', 'company_city', 'company_street', 'contact_person_first_name', 'contact_person_first_name_kana', 'contact_person_last_name', 'contact_person_last_name_kana', 'contact_person_tel', 'contact_person_email'),
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
