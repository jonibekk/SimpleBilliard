<?php
App::uses('GoalousTestCase', 'Test');
App::uses('MobileAppVersion', 'Request');

class MobileAppVersionTest extends GoalousTestCase
{
    public function test_versionSupporting()
    {
        $this->assertTrue(MobileAppVersion::isSupporting('1.0.0', '1.0.0'));
        $this->assertTrue(MobileAppVersion::isSupporting('10.0.0', '10.0.1'));
        $this->assertTrue(MobileAppVersion::isSupporting('1.0.10', '1.0.11'));
        $this->assertTrue(MobileAppVersion::isSupporting('1.20.', '1.20.1'));
        $this->assertTrue(MobileAppVersion::isSupporting('1.0.0.0', '1.0.0.1'));

        $this->assertFalse(MobileAppVersion::isExpired('1.0.0', '1.0.0'));
    }

    public function test_versionNotSupporting()
    {
        $this->assertFalse(MobileAppVersion::isSupporting('1', ''));
        $this->assertFalse(MobileAppVersion::isSupporting('2', '1'));
        $this->assertFalse(MobileAppVersion::isSupporting('2.0.0', '1.0.1'));
        $this->assertFalse(MobileAppVersion::isSupporting('1.10.0', '1.9.0'));

        $this->assertTrue(MobileAppVersion::isExpired('2', '1'));
    }
}
