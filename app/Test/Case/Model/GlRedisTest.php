<?php
App::uses('GlRedis', 'Model');

/**
 * GlRedis Test Case
 *
 * @property GlRedis $GlRedis
 */
class GlRedisTest extends CakeTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array();

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->GlRedis = ClassRegistry::init('GlRedis');
        $this->GlRedis->changeDbSource('redis_test');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        $this->GlRedis->deleteAllData();
        unset($this->GlRedis);
        parent::tearDown();
    }

    function testSetNotifications()
    {
        $res = $this->GlRedis->setNotifications(1, 1, [2], 1, "body", ['/'], 1234);
        $this->assertTrue($res);
    }

    function testGetCountOfNewNotificationZero()
    {
        $this->assertEquals(0, $this->GlRedis->getCountOfNewNotification(1, 1));
    }

    function testGetCountOfNewNotificationOne()
    {
        $this->GlRedis->setNotifications(1, 1, [1], 2, "body", ['/'], 1234);
        $this->assertEquals(1, $this->GlRedis->getCountOfNewNotification(1, 1));
    }

    function testGetCountOfNewNotificationTwo()
    {
        $this->GlRedis->setNotifications(1, 1, [1], 2, "body", ['/'], 1234);
        $this->GlRedis->setNotifications(1, 1, [1], 2, "body", ['/'], 1234);
        $this->assertEquals(2, $this->GlRedis->getCountOfNewNotification(1, 1));
    }

    function testDeleteCountOfNewNotificationFalse()
    {
        $this->assertFalse($this->GlRedis->deleteCountOfNewNotification(1, 1));
        $this->assertEquals(0, $this->GlRedis->getCountOfNewNotification(1, 1));
    }

    function testDeleteCountOfNewNotificationTrue()
    {
        $this->GlRedis->setNotifications(1, 1, [1], 2, "body", ['/'], 1234);
        $this->assertTrue($this->GlRedis->deleteCountOfNewNotification(1, 1));
        $this->assertEquals(0, $this->GlRedis->getCountOfNewNotification(1, 1));
    }

    function testChangeReadStatusOfNotificationSuccess()
    {
        $this->GlRedis->setNotifications(1, 1, [1], 2, "body", ['/'], 1893423600);
        $notifies = $this->GlRedis->getNotifications(1, 1);
        $notify_id = $notifies[0]['id'];
        $this->assertTrue($this->GlRedis->changeReadStatusOfNotification(1, 1, $notify_id));
        $notifies = $this->GlRedis->getNotifications(1, 1);
        $this->assertFalse($notifies[0]['unread_flg']);
    }

    function testChangeReadStatusOfNotificationNotFound()
    {
        $this->assertFalse($this->GlRedis->changeReadStatusOfNotification(1, 1, 1));
    }

    function testGetNotificationsFromDateNullLimitNull()
    {
        for ($i = 0; $i < 3; $i++) {
            $this->GlRedis->setNotifications(1, 1, [1], 2, "body", [], 1893423600);
        }
        $res = $this->GlRedis->getNotifications(1, 1);
        $this->assertCount(3, $res);
    }

    function testGetNotificationsFromDateNullLimited()
    {
        for ($i = 0; $i < 3; $i++) {
            $this->GlRedis->setNotifications(1, 1, [1], 2, "body", [], 1893423600);
        }
        $res = $this->GlRedis->getNotifications(1, 1, 1);
        $this->assertCount(1, $res);
    }

    function testGetNotificationsFromDateLimited()
    {
        for ($i = 0; $i < 3; $i++) {
            $this->GlRedis->setNotifications(1, 1, [1], 2, "body", [], 1893423600);
        }
        $res = $this->GlRedis->getNotifications(1, 1);
        $this->assertCount(2, $this->GlRedis->getNotifications(1, 1, 3, $res[0]['score']));
    }

    function testGetNotificationsNotUnread()
    {
        $this->GlRedis->setNotifications(1, 1, [1], 2, "body", [], 1893423600);
        $res = $this->GlRedis->getNotifications(1, 1);
        $this->GlRedis->changeReadStatusOfNotification(1, 1, $res[0]['id']);
        $res = $this->GlRedis->getNotifications(1, 1);
        $this->assertFalse($res[0]['unread_flg']);
    }

    function testMakeDeviceHash()
    {
        $res = $this->GlRedis->makeDeviceHash(1, "1111");
        $this->assertTrue(is_string($res));
    }

    function testSaveDeviceHash()
    {
        $res = $this->GlRedis->saveDeviceHash(1, 1);
        $this->assertEquals(1, $res);
    }

    function testIsExistsDeviceHashFalse()
    {
        $res = $this->GlRedis->isExistsDeviceHash(1, 1);
        $this->assertFalse($res);
    }

    function testIsExistsDeviceHashTrue()
    {
        $this->GlRedis->saveDeviceHash(1, 1);
        $res = $this->GlRedis->isExistsDeviceHash(1, 1);
        $this->assertTrue($res);
    }

    function testDeleteDeviceHashZero()
    {
        $res = $this->GlRedis->deleteDeviceHash(1, 1);
        $this->assertEquals(0, $res);
    }

    function testDeleteDeviceHashOne()
    {
        $this->GlRedis->saveDeviceHash(1, 1);
        $res = $this->GlRedis->deleteDeviceHash(1, 1);
        $this->assertEquals(1, $res);
    }

    function testIsAccountLocked()
    {
        for ($i = 0; $i < 3; $i++) {
            $this->GlRedis->isAccountLocked('aaa@aaa.com', "111");
        }
        $this->assertFalse($this->GlRedis->isAccountLocked('aaa@aaa.com', "111"));
        $this->assertTrue($this->GlRedis->isAccountLocked('aaa@aaa.com', "111"));
    }

    function testGetNotificationsNotFound()
    {
        $this->assertEmpty($this->GlRedis->getNotifications(1, 1));
    }

    function testGetKeyName()
    {
        $method = new ReflectionMethod($this->GlRedis, 'getKeyName');
        $method->setAccessible(true);

        try {
            $method->invoke($this->GlRedis, null);
        } catch (RuntimeException $e) {
        }
        $this->assertTrue(isset($e));
    }

}
