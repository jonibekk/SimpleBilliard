<?php

App::uses('GoalousTestCase', 'Test');
App::uses('LoginAuthenticator', 'Lib/Auth');
App::uses('JwtAuthentication', 'Lib/Jwt');

/**
 * Class JwtAuthenticationTest
 */
class LoginAuthenticatorTest extends GoalousTestCase
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
//        LoginAuthenticator::verify($validTokenButNotInRedis);
//    }

    /**
     * @expectedException AuthenticationOutOfTermException
     */
    function testExceptionThrowsOnExpiredToken()
    {
        $lastMonth = GoalousDateTime::now()->subMonth(1);
        GoalousDateTime::setTestNow($lastMonth);

        $loginAuthentication = LoginAuthenticator::publish($userId = 1, $teamId = 2);
        LoginAuthenticator::verify($loginAuthentication->token());
    }

    /**
     * @expectedException AuthenticationOutOfTermException
     */
    function testExceptionThrowsOnIssueBeforeStart()
    {
        $lastMonth = GoalousDateTime::now()->addDays(1);
        GoalousDateTime::setTestNow($lastMonth);

        $loginAuthentication = LoginAuthenticator::publish($userId = 1, $teamId = 2);
        LoginAuthenticator::verify($loginAuthentication->token());
    }

    /**
     * @expectedException AuthenticationException
     */
    function testExceptionThrowsFailedSignification()
    {
        $loginAuthentication = LoginAuthenticator::publish($userId = 1, $teamId = 2);
        LoginAuthenticator::verify($loginAuthentication->token()."A");
    }

    /**
     * @expectedException RuntimeException
     */
    function testExceptionThrowsOnInvalidToken()
    {
        LoginAuthenticator::verify('NOT_A_VALID_TOKEN');
    }

    function testAuthorizeAndVerifyLogin()
    {
        $loginAuthentication = LoginAuthenticator::publish($userId = 1, $teamId = 2);

        $this->assertEquals($userId, $loginAuthentication->getUserId());
        $this->assertEquals($teamId, $loginAuthentication->getTeamId());

        $loginAgainAuthentication = LoginAuthenticator::verify($loginAuthentication->token());

        $this->assertSame($loginAuthentication->getUserId(), $loginAgainAuthentication->getUserId());
        $this->assertSame($loginAuthentication->getTeamId(), $loginAgainAuthentication->getTeamId());
        $this->assertSame($loginAuthentication->token(), $loginAgainAuthentication->token());
        $this->assertSame(
            $loginAuthentication->getJwtAuthentication()->getJwtId(),
            $loginAgainAuthentication->getJwtAuthentication()->getJwtId()
        );
    }
}
