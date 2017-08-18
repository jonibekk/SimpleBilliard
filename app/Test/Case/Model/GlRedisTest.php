<?php App::uses('GoalousTestCase', 'Test');
App::uses('GlRedis', 'Model');

/**
 * GlRedis Test Case
 *
 * @property GlRedis $GlRedis
 */
class GlRedisTest extends GoalousTestCase
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

    function testGetCountOfNewMessageNotificationZero()
    {
        $this->assertEquals(0, $this->GlRedis->getCountOfNewMessageNotification(1, 1));
    }

    function testGetCountOfNewNotificationOne()
    {
        $this->GlRedis->setNotifications(1, 1, [1], 2, "body", ['/'], 1234);
        $this->assertEquals(1, $this->GlRedis->getCountOfNewNotification(1, 1));
    }

    function testGetCountOfNewMessageNotificationOne()
    {
        $this->GlRedis->setNotifications(25, 1, [1], 2, "body", ['/'], 1234);
        $this->assertEquals(1, $this->GlRedis->getCountOfNewMessageNotification(1, 1));
    }

    function testGetCountOfNewNotificationTwo()
    {
        $this->GlRedis->setNotifications(1, 1, [1], 2, "body", ['/'], 1234);
        $this->GlRedis->setNotifications(1, 1, [1], 2, "body", ['/'], 1234);
        $this->assertEquals(2, $this->GlRedis->getCountOfNewNotification(1, 1));
    }

    function testGetCountOfNewMessageNotificationTwo()
    {
        $this->GlRedis->setNotifications(25, 1, [1], 2, "body", ['/'], 1234);
        $this->GlRedis->setNotifications(25, 1, [1], 2, "body", ['/'], 1234);
        $this->assertEquals(2, $this->GlRedis->getCountOfNewMessageNotification(1, 1));
    }

    function testDeleteCountOfNewNotificationFalse()
    {
        $this->assertFalse($this->GlRedis->deleteCountOfNewNotification(1, 1));
        $this->assertEquals(0, $this->GlRedis->getCountOfNewNotification(1, 1));
    }

    function testDeleteCountOfNewMessageNotificationFalse()
    {
        $this->assertFalse($this->GlRedis->deleteCountOfNewMessageNotification(1, 1));
        $this->assertEquals(0, $this->GlRedis->getCountOfNewMessageNotification(1, 1));
    }

    function testDeleteCountOfNewNotificationTrue()
    {
        $this->GlRedis->setNotifications(1, 1, [1], 2, "body", ['/'], 1234);
        $this->assertTrue($this->GlRedis->deleteCountOfNewNotification(1, 1));
        $this->assertEquals(0, $this->GlRedis->getCountOfNewNotification(1, 1));
    }

    function testDeleteCountOfNewMessageNotificationTrue()
    {
        $this->GlRedis->setNotifications(25, 1, [1], 2, "body", ['/'], 1234);
        $this->assertTrue($this->GlRedis->deleteCountOfNewMessageNotification(1, 1));
        $this->assertEquals(0, $this->GlRedis->getCountOfNewMessageNotification(1, 1));
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

    function testGetNotifyIdsFromDateNullLimitNull()
    {
        for ($i = 0; $i < 3; $i++) {
            $this->GlRedis->setNotifications(1, 1, [1], 2, "body", [], 1893423600);
        }
        $res = $this->GlRedis->getNotifyIds(1, 1);
        $this->assertCount(3, $res);
    }

    function testGetMessageNotificationsFromDateNullLimitNull()
    {
        for ($i = 0; $i < 3; $i++) {
            $this->GlRedis->setNotifications(25, 1, [1], 2, "body", [], 1893423600);
        }
        $res = $this->GlRedis->getMessageNotifications(1, 1);
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

    function testGetNotifyIdsFromDateNullLimited()
    {
        for ($i = 0; $i < 3; $i++) {
            $this->GlRedis->setNotifications(1, 1, [1], 2, "body", [], 1893423600);
        }
        $res = $this->GlRedis->getNotifyIds(1, 1, 1);
        $this->assertCount(1, $res);
    }

    function testGetMessageNotificationsFromDateNullLimited()
    {
        for ($i = 0; $i < 3; $i++) {
            $this->GlRedis->setNotifications(25, 1, [1], 2, "body", [], 1893423600);
        }
        $res = $this->GlRedis->getMessageNotifications(1, 1, 1);
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

    function testGetNotifyIdsFromDateLimited()
    {
        for ($i = 0; $i < 3; $i++) {
            $this->GlRedis->setNotifications(1, 1, [1], 2, "body", [], 1893423600);
        }
        $res = $this->GlRedis->getNotifications(1, 1);
        $this->assertCount(2, $this->GlRedis->getNotifyIds(1, 1, 3, $res[0]['score']));
    }

    function testGetMessageNotificationsFromDateLimited()
    {
        for ($i = 0; $i < 3; $i++) {
            $this->GlRedis->setNotifications(25, 1, [1], 2, "body", [], 1893423600);
        }
        $res = $this->GlRedis->getMessageNotifications(1, 1);
        $this->assertCount(2, $this->GlRedis->getMessageNotifications(1, 1, 3, $res[0]['score']));
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
        for ($i = 0; $i < 4; $i++) {
            $this->GlRedis->incrementLoginFailedCount('aaa@aaa.com', "111");
        }
        $this->assertFalse($this->GlRedis->isAccountLocked('aaa@aaa.com', "111"));
        $this->GlRedis->incrementLoginFailedCount('aaa@aaa.com', "111");
        $this->assertTrue($this->GlRedis->isAccountLocked('aaa@aaa.com', "111"));
    }

    function testIsTwoFaAccountLocked()
    {
        for ($i = 0; $i < 3; $i++) {
            $this->GlRedis->isTwoFaAccountLocked(111, "111");
        }
        $this->assertFalse($this->GlRedis->isTwoFaAccountLocked(111, "111"));
        $this->assertTrue($this->GlRedis->isTwoFaAccountLocked(111, "111"));
    }

    function testGetNotificationsNotFound()
    {
        $this->assertEmpty($this->GlRedis->getNotifications(1, 1));
    }

    function testGetNotifyIdsNotFound()
    {
        $this->assertEmpty($this->GlRedis->getNotifyIds(1, 1));
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

    function testSaveAccessUser()
    {
        $this->GlRedis->saveAccessUser(1, 1, 1442567110, [9, 1]);
        $this->GlRedis->saveAccessUser(1, 2, 1442567110, [9, 1]);
        $res = $this->GlRedis->getAccessUsers(1, '2015-09-18', 9);
        $this->assertEquals([0 => '1', 1 => '2'], $res);
        $res = $this->GlRedis->getAccessUsers(1, '2015-09-18', 1);
        $this->assertEquals([0 => '1', 1 => '2'], $res);

        $this->GlRedis->delAccessUsers(1, '2015-09-18', 9);
        $res = $this->GlRedis->getAccessUsers(1, '2015-09-18', 9);
        $this->assertEquals([], $res);
        $res = $this->GlRedis->getAccessUsers(1, '2015-09-18', 1);
        $this->assertEquals([0 => '1', 1 => '2'], $res);
    }

    function testSaveTeamInsight()
    {
        $this->GlRedis->saveTeamInsight(1, '2015-09-18', '2015-09-19', 9, [1, 2, 3]);
        $res = $this->GlRedis->getTeamInsight(1, '2015-09-18', '2015-09-19', 9);
        $this->assertEquals([1, 2, 3], $res);
        $res = $this->GlRedis->getTeamInsight(1, '2015-09-18', '2015-09-18', 9);
        $this->assertEquals(null, $res);
        $res = $this->GlRedis->getTeamInsight(1, '2015-09-18', '2015-09-19', 1);
        $this->assertEquals(null, $res);
    }

    function testSaveGroupInsight()
    {
        $this->GlRedis->saveGroupInsight(1, '2015-09-18', '2015-09-19', 9, 1, [1, 2, 3]);
        $res = $this->GlRedis->getGroupInsight(1, '2015-09-18', '2015-09-19', 9, 1);
        $this->assertEquals([1, 2, 3], $res);
        $res = $this->GlRedis->getGroupInsight(1, '2015-09-18', '2015-09-18', 9, 1);
        $this->assertEquals(null, $res);
        $res = $this->GlRedis->getGroupInsight(1, '2015-09-18', '2015-09-18', 9, 2);
        $this->assertEquals(null, $res);
        $res = $this->GlRedis->getGroupInsight(1, '2015-09-18', '2015-09-19', 1, 1);
        $this->assertEquals(null, $res);
    }

    function testSaveCircleInsight()
    {
        $this->GlRedis->saveCircleInsight(1, '2015-09-18', '2015-09-19', 9, [1, 2, 3]);
        $res = $this->GlRedis->getCircleInsight(1, '2015-09-18', '2015-09-19', 9);
        $this->assertEquals([1, 2, 3], $res);
        $res = $this->GlRedis->getCircleInsight(1, '2015-09-18', '2015-09-18', 9);
        $this->assertEquals(null, $res);
        $res = $this->GlRedis->getCircleInsight(1, '2015-09-18', '2015-09-19', 1);
        $this->assertEquals(null, $res);
    }

    function testSaveTeamRanking()
    {
        $this->GlRedis->saveTeamRanking(1, '2015-09-18', '2015-09-19', 9, 'ranking', [1, 2, 3]);
        $res = $this->GlRedis->getTeamRanking(1, '2015-09-18', '2015-09-19', 9, 'ranking');
        $this->assertEquals([1, 2, 3], $res);
        $res = $this->GlRedis->getTeamRanking(1, '2015-09-18', '2015-09-18', 9, 'ranking');
        $this->assertEquals(null, $res);
        $res = $this->GlRedis->getTeamRanking(1, '2015-09-18', '2015-09-18', 9, 'ranking2');
        $this->assertEquals(null, $res);
        $res = $this->GlRedis->getTeamRanking(1, '2015-09-18', '2015-09-19', 1, 'ranking');
        $this->assertEquals(null, $res);
    }

    function testSaveGroupRanking()
    {
        $this->GlRedis->saveGroupRanking(1, '2015-09-18', '2015-09-19', 9, 1, 'ranking', [1, 2, 3]);
        $res = $this->GlRedis->getGroupRanking(1, '2015-09-18', '2015-09-19', 9, 1, 'ranking');
        $this->assertEquals([1, 2, 3], $res);
        $res = $this->GlRedis->getGroupRanking(1, '2015-09-18', '2015-09-18', 9, 1, 'ranking');
        $this->assertEquals(null, $res);
        $res = $this->GlRedis->getGroupRanking(1, '2015-09-18', '2015-09-18', 9, 1, 'ranking2');
        $this->assertEquals(null, $res);
        $res = $this->GlRedis->getGroupRanking(1, '2015-09-18', '2015-09-19', 1, 1, 'ranking');
        $this->assertEquals(null, $res);
    }

    function testGetKeyCount()
    {
        $this->GlRedis->saveAccessUser(1, 1, 1, [9, 10]);
        $this->GlRedis->saveAccessUser(2, 2, 1, [9]);
        $res = $this->GlRedis->getKeyCount("*9:access_user:");
        $this->assertEquals(2, $res);
    }

    function testDellKeys()
    {
        try {
            $this->GlRedis->dellKeys("*");
        } catch (RuntimeException $e) {
        }
        $this->assertTrue(isset($e));

        $this->GlRedis->saveAccessUser(1, 1, 1, [9, 10]);
        $this->GlRedis->saveAccessUser(2, 2, 1, [9]);
        $res = $this->GlRedis->dellKeys("*9:access_user:");
        $this->assertEquals(2, $res);
    }

    function testGetSetupGuideStatus()
    {
        $this->GlRedis->getSetupGuideStatus($user_id = 1);
    }

    function testSaveSetupGuideStatus()
    {
        $this->GlRedis->saveSetupGuideStatus(
            $user_id = 1,
            $save_hash_status = [
                1                                     => 1,
                2                                     => 1,
                3                                     => 1,
                4                                     => 1,
                5                                     => 0,
                6                                     => 1,
                GlRedis::FIELD_SETUP_LAST_UPDATE_TIME => time()
            ]
        );
        $res = $this->GlRedis->getSetupGuideStatus($user_id);
        $this->assertEquals($save_hash_status, $res);
    }

    function testDeleteSetupGuideStatus()
    {
        $this->GlRedis->saveSetupGuideStatus(
            $user_id = 1,
            $save_hash_status = [
                1                                     => 1,
                2                                     => 1,
                3                                     => 1,
                4                                     => 1,
                5                                     => 0,
                6                                     => 1,
                GlRedis::FIELD_SETUP_LAST_UPDATE_TIME => time()
            ]
        );
        $this->GlRedis->deleteSetupGuideStatus($user_id);
        $this->assertFalse((bool)$this->GlRedis->getSetupGuideStatus($user_id));
    }

}
