<?php
App::uses('GoalousTestCase', 'Test');
App::uses('UserAgent', 'Request');

class UserAgentTest extends GoalousTestCase
{
    public function test_UserAgentiOS()
    {
        $userAgent = 'Mozilla/5.0 (iPhone; CPU iPhone OS 10_3_2 like Mac OS X) AppleWebKit/603.2.4 (KHTML, like Gecko) Mobile/14F89 Goalous App iOS (Dev, 1.1.2)';
        $ua = UserAgent::detect($userAgent);
        $this->assertTrue($ua->isMobileAppAccess());
        $this->assertTrue($ua->isiOSApp());
        $this->assertFalse($ua->isAndroidApp());
        $this->assertEquals('Dev', $ua->getMobileAppEnvironment());
        $this->assertEquals('1.1.2', $ua->getMobileAppVersion());
    }

    public function test_UserAgentAndroid()
    {
        $userAgent = 'Mozilla/5.0 (Linux; Android 5.1.1; Nexus 5 Build/LMY48B; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Goalous App Android (Isao, 2.10.0)';
        $ua = UserAgent::detect($userAgent);
        $this->assertTrue($ua->isMobileAppAccess());
        $this->assertTrue($ua->isAndroidApp());
        $this->assertFalse($ua->isiOSApp());
        $this->assertEquals('Isao', $ua->getMobileAppEnvironment());
        $this->assertEquals('2.10.0', $ua->getMobileAppVersion());
    }

    public function test_UserAgentPC()
    {
        $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.115 Safari/537.36';
        $ua = UserAgent::detect($userAgent);
        $this->assertFalse($ua->isMobileAppAccess());
        $this->assertFalse($ua->isiOSApp());
        $this->assertFalse($ua->isAndroidApp());
        $this->assertEquals('', $ua->getMobileAppEnvironment());
        $this->assertEquals('', $ua->getMobileAppVersion());
    }

    public function test_UserAgentIrregular()
    {
        $userAgent = 'Mozilla/5.0 (iPhone; CPU iPhone OS 10_3_2 like Mac OS X) AppleWebKit/603.2.4 (KHTML, like Gecko) Mobile/14F89 Goalous App iOS                (     Dev   ,         1.0.0)';
        $ua = UserAgent::detect($userAgent);
        $this->assertTrue($ua->isiOSApp());
        $this->assertEquals('Dev', $ua->getMobileAppEnvironment());
        $this->assertEquals('1.0.0', $ua->getMobileAppVersion());

        $userAgent = 'Mozilla/5.0 (iPhone; CPU iPhone OS 10_3_2 like Mac OS X) AppleWebKit/603.2.4 (KHTML, like Gecko) Mobile/14F89 Goalous App iOS(Dev,2.0.0,,,)';
        $ua = UserAgent::detect($userAgent);
        $this->assertTrue($ua->isiOSApp());
        $this->assertEquals('Dev', $ua->getMobileAppEnvironment());
        $this->assertEquals('2.0.0', $ua->getMobileAppVersion());

        $userAgent = 'Mozilla/5.0 (iPhone; CPU iPhone OS 10_3_2 like Mac OS X) AppleWebKit/603.2.4 (KHTML, like Gecko) Mobile/14F89 Goalous App iOS';
        $ua = UserAgent::detect($userAgent);
        $this->assertTrue($ua->isMobileAppAccess());
        $this->assertTrue($ua->isiOSApp());
        $this->assertFalse($ua->isAndroidApp());
        $this->assertEquals('', $ua->getMobileAppEnvironment());
        $this->assertEquals('', $ua->getMobileAppVersion());
    }
}
