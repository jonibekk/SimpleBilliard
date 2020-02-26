<?php
App::uses('CheckedCircle', 'Model');

/**
 * CheckedCircle Test Case
 */
class CheckedCircleTest extends CakeTestCase {

	/**
	 * Fixtures
	 *
	 * @var array
	 */
	public $fixtures = array(
		'app.checked_circle',
		// 'app.circle',
		// 'app.user',
		// 'app.team_member',
	);

	/**
	 * setUp method
 	 *
	 * @return void
	 */
	public function setUp() {
		parent::setUp();
		$this->CheckedCircle = ClassRegistry::init('CheckedCircle');
	}

	/**
	 * tearDown method
	 *
	 * @return void
	 */
	public function tearDown() {
		unset($this->CheckedCircle);

		parent::tearDown();
	}


	public function test_getCheckedCircle_success() {

		// search exist record
		$res1 = $this->CheckedCircle->getCheckedCircle(1, 1, 1);
		$this->assertCount(1, $res1);

		// search doesn't exist record
		$res2 = $this->CheckedCircle->getCheckedCircle(1, 1, 2);
		$this->assertEqual(false, $res2);

	}

	public function test_add_success() {

		// $res1 = $this->CheckedCircle->getCheckedCircle(1, 1, 2);

		// $this->assertEqual(false, $res1);

		$resres = $this->CheckedCircle->add(1, 1, 2);

		// $data = [
		// 	'user_id' =>1,
		// 	'team_id' =>1,
		// 	'circle_id' => 2
		// ];

		// $resres = $this->CheckedCircle->save($data);


		// print_r($this->CheckedCircle->getDataSource()->getLog());
		// GoalousLog::info('SQL', $this->CheckedCircle->getDataSource()->getLog());
		// GoalousLog::info('SQL', array_pop($this->CheckedCircle->getDataSource()->getLog()['log']));

		
		// $this->assertEqual(false, $resres);

		// $res2 = $this->CheckedCircle->getCheckedCircle(1, 1, 2);

		// $this->assertCount(1, $res2);

		// $this->CheckedCircle->add(1, 1, 3);

		// $res2 = $this->CheckedCircle->getCheckedCircle(1, 1, 3);

		// $this->assertCount(1, $res2);
	}

}
