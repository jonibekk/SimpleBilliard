<?php App::uses('GoalousTestCase', 'Test');
App::uses('Device', 'Model');

/**
 * Device Test Case
 *
 * @property Device $Device
 */
class DeviceTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.device',
        'app.user',
        'app.team',
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->Device = ClassRegistry::init('Device');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Device);

        parent::tearDown();
    }

    function testDummy()
    {

    }

    public function testAddDevice()
    {
        $data = [
            'Device' => [
                'user_id'      => 1,
                'device_token' => 'dummy-dummy-dummy',
                'os_type'      => 0,
            ]
        ];
        $res = $this->Device->add($data);
        $this->assertTrue($res);
    }

    public function testGetDevicesByUserId1()
    {
        $data = $this->Device->getDevicesByUserId(1);
        $this->assertTrue(count($data) === 1);
    }

    public function testGetDevicesByUserId2()
    {
        $data = $this->Device->getDevicesByUserId(1);
        $this->assertTrue($data[0]['Device']['device_token'] === 'ios_dummy1');
    }

    public function testGetDevicesByUserId3()
    {
        $data = $this->Device->getDevicesByUserId(2);
        $this->assertTrue($data[0]['Device']['device_token'] === 'android_dummy1');
    }

    public function testGetDevicesByUserId4()
    {
        $data = $this->Device->getDevicesByUserId(3);
        $this->assertTrue(count($data) === 2);
    }

    public function testGetDevicesByUserId5()
    {
        $data = $this->Device->getDevicesByUserId(3);
        $this->assertTrue($data[0]['Device']['device_token'] === 'ios_dummy2');
    }

    public function testGetDevicesByUserId6()
    {
        $data = $this->Device->getDevicesByUserId(3);
        $this->assertTrue($data[1]['Device']['device_token'] === 'android_dummy2');
    }

    public function testGetDevicesByUserIdNotFound()
    {
        $data = $this->Device->getDevicesByUserId(99);
        $this->assertTrue(empty($data));
    }

    public function testGetDevicesByUserIdNotFoundDelData()
    {
        $data = $this->Device->getDevicesByUserId(4);
        $this->assertTrue(empty($data));
    }

    public function testGetDeviceTokens1()
    {
        $data = $this->Device->getDeviceTokens(1);
        $this->assertTrue(count($data) === 1);
    }

    public function testGetDeviceTokens2()
    {
        $data = $this->Device->getDeviceTokens(1);
        $this->assertTrue($data[0] === "ios_dummy1");
    }

    public function testGetDeviceTokens3()
    {
        $data = $this->Device->getDeviceTokens(3);
        $this->assertTrue(count($data) === 2);
    }

    public function testGetDeviceTokens4()
    {
        $data = $this->Device->getDeviceTokens(3);
        $this->assertTrue($data[0] === "ios_dummy2");
    }

    public function testGetDeviceTokens5()
    {
        $data = $this->Device->getDeviceTokens(3);
        $this->assertTrue($data[1] === "android_dummy2");
    }

    public function testGetDeviceTokensNotFound()
    {
        $data = $this->Device->getDeviceTokens(99);
        $this->assertTrue(empty($data));
    }

    public function testGetDevicesByUserIdAndDeviceToken1()
    {
        $data = $this->Device->getDevicesByUserIdAndDeviceToken(1, 'ios_dummy1');
        $this->assertTrue(count($data) === 1);
    }

    public function testGetDevicesByUserIdAndDeviceToken2()
    {
        $data = $this->Device->getDevicesByUserId(3, 'ios_dummy2');
        $this->assertTrue($data[0]['Device']['device_token'] === 'ios_dummy2');
    }

    public function testGetDevicesByUserIdAndDeviceTokenNotFound()
    {
        $data = $this->Device->getDevicesByUserId(99, 'dummy!');
        $this->assertTrue(empty($data));
    }

    public function testGetDevicesByUserIdAndDeviceTokenNotFoundDelData()
    {
        $data = $this->Device->getDevicesByUserId(4, 'android_dummy3');
        $this->assertTrue(empty($data));
    }

    function testIsInstalledMobileApp()
    {
        $this->Device->my_uid = 1;
        // In case that mobile app is installed
        $this->Device->save(['user_id' => $this->Device->my_uid, 'device_token' => 1, 'os_type' => 1]);
        $res = $this->Device->isInstalledMobileApp($this->Device->my_uid);
        $this->assertTrue($res);

        // In case that mobile app is not installed
        $this->Device->deleteAll(['user_id' => $this->Device->my_uid]);
        $res = $this->Device->isInstalledMobileApp($this->Device->my_uid);
        $this->assertFalse($res);
    }

    function testSaveZeroUserId()
    {
        $ret = $this->Device->save(['user_id' => 0, 'device_token' => 1, 'installation_id' => 1, 'os_type' => 1]);
        $this->assertFalse($ret);
        $this->assertNotEmpty($this->Device->validationErrors['user_id']);
    }

    function testSaveDuplicatedInstallationId()
    {
        $ret1 = $this->Device->save(['user_id' => 1, 'device_token' => 1, 'installation_id' => 1, 'os_type' => 1]);
        $this->assertNotEmpty($ret1);
        $ret1Id = $this->Device->getLastInsertID();

        $this->Device->create();
        $ret2 = $this->Device->save(['user_id' => 1, 'device_token' => 1, 'installation_id' => 1, 'os_type' => 1]);
        $this->assertFalse($ret2);
        $this->assertNotEmpty($this->Device->validationErrors['installation_id']);

        $this->Device->delete($ret1Id);

        $this->Device->create();
        $ret3 = $this->Device->save(['user_id' => 1, 'device_token' => 1, 'installation_id' => 1, 'os_type' => 1]);
        $this->assertNotEmpty($ret3);
    }

}
