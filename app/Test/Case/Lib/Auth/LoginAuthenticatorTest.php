<?php

App::uses('GoalousTestCase', 'Test');
App::uses('LoginAuthenticator', 'Lib/Auth');

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
     * @expectedException RuntimeException
     */
    function test_aaa()
    {
        // TODO:
        LoginAuthenticator::auth('NOT_A_VALID_TOKEN');
    }


    function testAuthorizeAndVerifyLogin()
    {
        $loginAuthentication = LoginAuthenticator::authorize($userId = 1, $teamId = 2);

        $this->assertEquals($userId, $loginAuthentication->getUserId());
        $this->assertEquals($teamId, $loginAuthentication->getTeamId());

        $loginAgainAuthentication = LoginAuthenticator::auth($loginAuthentication->token());

        $this->assertSame($loginAuthentication->getUserId(), $loginAgainAuthentication->getUserId());
        $this->assertSame($loginAuthentication->getTeamId(), $loginAgainAuthentication->getTeamId());
        $this->assertSame($loginAuthentication->token(), $loginAgainAuthentication->token());
        $this->assertSame(
            $loginAuthentication->getJwtAuthentication()->getJwtId(),
            $loginAgainAuthentication->getJwtAuthentication()->getJwtId()
        );
    }
}
