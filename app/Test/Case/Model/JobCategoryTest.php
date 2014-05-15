<?php
App::uses('JobCategory', 'Model');

/**
 * JobCategory Test Case

 */
class JobCategoryTest extends CakeTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.job_category',
        'app.team',
        'app.team_member'
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->JobCategory = ClassRegistry::init('JobCategory');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->JobCategory);

        parent::tearDown();
    }

}
