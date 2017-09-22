<?php
class SeparateCompanyAddressOnPaymentSettings extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'separate_company_address_on_payment_settings';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'payment_settings' => array(
					'company_country' => array('type' => 'string', 'null' => false, 'length' => 2, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Company address(country)', 'charset' => 'utf8mb4', 'after' => 'company_name'),
					'company_post_code' => array('type' => 'string', 'null' => false, 'length' => 16, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Company address(post_code)', 'charset' => 'utf8mb4', 'after' => 'company_country'),
					'company_region' => array('type' => 'string', 'null' => false, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Company address(region)', 'charset' => 'utf8mb4', 'after' => 'company_post_code'),
					'company_city' => array('type' => 'string', 'null' => false, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Company address(city)', 'charset' => 'utf8mb4', 'after' => 'company_region'),
					'company_street' => array('type' => 'string', 'null' => false, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Company address(street)', 'charset' => 'utf8mb4', 'after' => 'company_city'),
				),
			),
			'drop_field' => array(
				'payment_settings' => array('company_address'),
			),
		),
		'down' => array(
			'drop_field' => array(
				'payment_settings' => array('company_country', 'company_post_code', 'company_region', 'company_city', 'company_street'),
			),
			'create_field' => array(
				'payment_settings' => array(
					'company_address' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Company address', 'charset' => 'utf8mb4'),
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
