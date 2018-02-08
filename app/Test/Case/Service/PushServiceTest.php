<?php
App::uses('GoalousTestCase', 'Test');
App::import('Service', 'PushService');

use Goalous\Model\Enum as Enum;
use Goalous\Model\Enum\Devices\DeviceType;

/**
 * Class PushServiceTest
 *
 * @property PushService $PushService
 */
class PushServiceTest extends GoalousTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.device'
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->PushService = ClassRegistry::init('PushService');
        $this->PushService->dryRequest = true;
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->PushService);
        parent::tearDown();
    }

    public function test_sendFirebasePushNotification()
    {
        $tokens = [
            [
                'device_token' => 'dSsk0ZeD_zo:APA91bEjRbRk45bn099j9LiU2u06kURkurW39Ohe3VOHnPetYW8vWdIIqBXe0oC42a2QlJLF7kdH3tlT5C4nMER3arIvrdeRGtuw2GX0MENjc88NhJNPIf1bHLlgJ5vLH-m8cNit8gVu',
                'os_type'      => 0
            ],
            [
                'device_token' => 'fbCFNCv068w:APA91bEodqhUr6NaLpwg0Bu8WUTciGk4ZrD3uewBU8lx804YaeQGVO3WUeNpJgXRSE8iNCwmsLwovxq2nPWlsklB93akO1sxMxmu3p_NFD5AaqFl6aoh9DhBkftSd6IylKjs6nDb1Ux9',
                'os_type'      => 1
            ],
            [
                'device_token' => 'xxxxxxxxxxx',
                'os_type'      => 1
            ]
        ];

        $res = $this->PushService->sendFirebasePushNotification($tokens, 'TEST...', 'https://goalous.com');
        $this->assertTrue($res === true);
    }
}