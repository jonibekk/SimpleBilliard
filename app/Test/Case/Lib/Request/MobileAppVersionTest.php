<?php
App::uses('GoalousTestCase', 'Test');
App::uses('MobileAppVersion', 'Request');

class MobileAppVersionTest extends GoalousTestCase
{
    public function test_versionGuaranteed()
    {
        $this->assertTrue(MobileAppVersion::isGuaranteed('1.0.0', '1.0.0'));
        $this->assertTrue(MobileAppVersion::isGuaranteed('10.0.0', '10.0.1'));
        $this->assertTrue(MobileAppVersion::isGuaranteed('1.0.10', '1.0.11'));
        $this->assertTrue(MobileAppVersion::isGuaranteed('1.20.', '1.20.1'));
        $this->assertTrue(MobileAppVersion::isGuaranteed('1.0.0.0', '1.0.0.1'));
    }

    public function test_versionNotGuaranteed()
    {
        $this->assertFalse(MobileAppVersion::isGuaranteed('1', ''));
        $this->assertFalse(MobileAppVersion::isGuaranteed('2', '1'));
        $this->assertFalse(MobileAppVersion::isGuaranteed('2.0.0', '1.0.1'));
        $this->assertFalse(MobileAppVersion::isGuaranteed('1.10.0', '1.9.0'));
    }
}
