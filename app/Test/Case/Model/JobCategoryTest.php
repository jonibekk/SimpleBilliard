<?php App::uses('GoalousTestCase', 'Test');
App::uses('JobCategory', 'Model');

/**
 * JobCategory Test Case
 *
 * @property mixed JobCategory
 */
class JobCategoryTest extends GoalousTestCase
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

    //ダミーテスト
    function testDummy()
    {
    }

}
