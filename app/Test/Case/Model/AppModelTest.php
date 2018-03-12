<?php App::uses('GoalousTestCase', 'Test');
App::uses('ActionResult', 'Model');
App::uses('User', 'Model');
/**
 * ActionResult Test Case
 *
 * @property ActionResult $ActionResult
 */
class AppModelTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.action_result',
        'app.team',
        'app.user',
        'app.email',
        'app.goal',
        'app.goal_category',
        'app.key_result',
        'app.attached_file',
        'app.action_result_file',
        'app.kr_progress_log',
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->ActionResult = ClassRegistry::init('ActionResult');
        $this->User = ClassRegistry::init('User');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->ActionResult);

        parent::tearDown();
    }

    function testAfterFindWithConverting()
    {
        $this->ActionResult->read(null, 1);
        $this->ActionResult->set('name', '%%%user_1%%%');
        $this->ActionResult->save();
        $this->ActionResult->read(null, 1);
        $this->assertEqual($this->ActionResult->field('name'), '%%%user_1:firstname lastname%%%');
    }

    function testAfterFindWithoutConverting() {
        $this->User->read(null, 1);
        $this->User->set('first_name', '%%%user_1%%%');
        $this->User->save();
        $this->User->read(null, 1);
        $this->assertEqual($this->User->field('first_name'), 'firstname');
    }

}
