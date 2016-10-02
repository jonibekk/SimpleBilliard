<?php App::uses('GoalousTestCase', 'Test');
App::uses('ActionResultFile', 'Model');

/**
 * ActionResultFile Test Case
 *
 * @property ActionResultFile $ActionResultFile
 */
class ActionResultFileTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.action_result_file',
        'app.action_result',
        'app.attached_file',
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->ActionResultFile = ClassRegistry::init('ActionResultFile');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->ActionResultFile);

        parent::tearDown();
    }

    function testDummy()
    {

    }

}
