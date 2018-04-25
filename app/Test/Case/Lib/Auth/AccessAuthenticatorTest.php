<?php

App::uses('GoalousTestCase', 'Test');
App::uses('AccessAuthenticator', 'Lib/Auth');
App::uses('JwtAuthentication', 'Lib/Jwt');

/**
 * Class JwtAuthenticationTest
 */
class AccessAuthenticatorTest extends GoalousTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();
        GoalousDateTime::setTestNow();
    }

    /**
     * @expectedException AuthenticationNotManagedException
     */
//    function testExceptionThrowsNotManaged()
//    {
//        // TODO: write test if new Redis class is created
//        $jwtToken = new JwtAuthentication($userId = 1, $teamId = 1);
//        $validTokenButNotInRedis = $jwtToken->token();
//
//        AccessAuthenticator::verify($validTokenButNotInRedis);
//    }

    /**
     * @expectedException AuthenticationOutOfTermException
     */
    function testExceptionThrowsOnExpiredToken()
    {
        $lastMonth = GoalousDateTime::now()->subMonth(1);
        GoalousDateTime::setTestNow($lastMonth);

        $authorizedAccessInfo = AccessAuthenticator::publish($userId = 1, $teamId = 2);
        AccessAuthenticator::verify($authorizedAccessInfo->token());
    }

    /**
     * @expectedException AuthenticationOutOfTermException
     */
    function testExceptionThrowsOnIssueBeforeStart()
    {
        $lastMonth = GoalousDateTime::now()->addDays(1);
        GoalousDateTime::setTestNow($lastMonth);

        $authorizedAccessInfo = AccessAuthenticator::publish($userId = 1, $teamId = 2);
        AccessAuthenticator::verify($authorizedAccessInfo->token());
    }

    /**
     * @expectedException AuthenticationException
     */
    function testExceptionThrowsFailedSignification()
    {
        $authorizedAccessInfo = AccessAuthenticator::publish($userId = 1, $teamId = 2);
        AccessAuthenticator::verify($authorizedAccessInfo->token()."A");
    }

    /**
     * @expectedException RuntimeException
     */
    function testExceptionThrowsOnInvalidToken()
    {
        AccessAuthenticator::verify('NOT_A_VALID_TOKEN');
    }

    function testAuthorizeAndVerifyLogin()
    {
        $authorizedAccessInfo = AccessAuthenticator::publish($userId = 1, $teamId = 2);

        $this->assertEquals($userId, $authorizedAccessInfo->getUserId());
        $this->assertEquals($teamId, $authorizedAccessInfo->getTeamId());

        $authorizedAccessInfo2 = AccessAuthenticator::verify($authorizedAccessInfo->token());

        $this->assertSame($authorizedAccessInfo->getUserId(), $authorizedAccessInfo2->getUserId());
        $this->assertSame($authorizedAccessInfo->getTeamId(), $authorizedAccessInfo2->getTeamId());
        $this->assertSame($authorizedAccessInfo->getEnvName(), $authorizedAccessInfo2->getEnvName());
        $this->assertSame($authorizedAccessInfo->token(), $authorizedAccessInfo2->token());

        $this->assertSame(
            $authorizedAccessInfo->getJwtAuthentication()->getJwtId(),
            $authorizedAccessInfo2->getJwtAuthentication()->getJwtId()
        );
    }

    function testTokenShouldNotChangeWhenCurrentDateChanged()
    {
        $authorizedAccessInfo = AccessAuthenticator::publish($userId = 1, $teamId = 2);
        $token = $authorizedAccessInfo->token();
        GoalousDateTime::setTestNow(GoalousDateTime::now()->addDay(1));
        $this->assertSame($token, $authorizedAccessInfo->token());

        GoalousDateTime::setTestNow(GoalousDateTime::now()->addDay(1));
        $this->assertSame($token, $authorizedAccessInfo->token());

        GoalousDateTime::setTestNow(GoalousDateTime::now()->addDay(1));
        $this->assertSame($token, $authorizedAccessInfo->token());
    }
}
