<?php App::uses('GoalousTestCase', 'Test');
App::uses('CirclePin', 'Model');

/**
 * CirclePin Test Case
 *
 * @property CirclePin $CirclePin
 */
class CirclePinTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.circle_pin',
        'app.circle',
        'app.circle_member',
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->CirclePin = ClassRegistry::init('CirclePin');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->CirclePin);

        parent::tearDown();
    }

    function _setDefault($uid, $team_id, $circle_orders)
    {
        $this->CirclePin->my_uid = $uid;
        $this->CirclePin->current_team_id = $team_id;
        $this->CirclePin->circle_orders = $circle_orders;
    }

    function testGetUnique()
    {
        $uid = 1;
        $team_id = 1;
        $circle_orders = '1,2,3,4,5,6,7,8,9,10,11';
        $this->_setDefault($uid, $team_id, $circle_orders);
        $res = $this->CirclePin->getUnique($uid, $team_id);
        $this->assertEquals($circle_orders,$res['circle_orders']);
    }

    function testInsertUpdate()
    {
        $uid = 1000;
        $team_id = 1000;
        $circle_orders = '1,2,3,4,5,6,7,8,9,10';
        $this->_setDefault($uid, $team_id, $circle_orders);
        $res = $this->CirclePin->insertUpdate($uid,$team_id,$circle_orders);
        $this->assertTrue($res);
    }

    function testDeleteId()
    {
        $uid = 1001;
        $team_id = 1001;
        $circle_orders = '1,2,3,4,5,6,7,8,9,10';
        $circle_id = 2;
        $this->_setDefault($uid, $team_id, $circle_orders);
        $res = $this->CirclePin->insertUpdate($uid,$team_id,$circle_orders);
        $this->assertTrue($res);
        $res = $this->CirclePin->deleteId($uid, $team_id, $circle_id);
        $this->assertTrue($res);
        $res = $this->CirclePin->getUnique($uid, $team_id);
        $this->assertEquals('1,3,4,5,6,7,8,9,10',substr($res['circle_orders'],1,-1));
    }

    public function testGetJoinedCircleData()
    {
        $uid = 1;
        $team_id = 1;
        $circle_orders = '1,2,3,4,5,6,7,8,9,10';
        $this->_setDefault($uid, $team_id, $circle_orders);
        $res = $this->CirclePin->getJoinedCircleData($uid, $team_id);
        $this->assertNotEmpty($res);
    }

    public function testGetPinData()
    {
        $uid = 1;
        $team_id = 1;
        $circle_orders = '1,2,3,4,5,6,7,8,9,10';
        $this->_setDefault($uid, $team_id, $circle_orders);
        $res = $this->CirclePin->getPinData($uid, $team_id);
        $this->assertNotEquals($res, "");
    }
}
