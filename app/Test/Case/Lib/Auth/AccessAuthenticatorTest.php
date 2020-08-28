<?php

App::uses('GoalousTestCase', 'Test');
App::uses('AccessAuthenticator', 'Lib/Auth');
App::uses('JwtAuthentication', 'Lib/Jwt');
App::uses('AccessTokenClient', 'Lib/Cache/Redis/AccessToken');

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

        $client = new AccessTokenClient();
        $client->del(new AccessTokenKey('*', '*', '*'));
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

    function testCheckTokenStoredInRedis()
    {
        $authorizedAccessInfo = AccessAuthenticator::publish($userId = 1, $teamId = 2);

        $client = new AccessTokenClient();
        $this->assertCount(1, $client->keys(new AccessTokenKey('*', '*', '*')));

        // add three more token to users.id = 2 to several teams.id
        for ($teamId = 1; $teamId < 4; $teamId++) {
            AccessAuthenticator::publish($userId = 2, $teamId);
        }

        $this->assertCount(4, $client->keys(new AccessTokenKey('*', '*', '*')));
        $this->assertCount(1, $client->keys(new AccessTokenKey(1, '*', '*')));
        $this->assertCount(3, $client->keys(new AccessTokenKey(2, '*', '*')));

        for ($teamId = 1; $teamId < 4; $teamId++) {
            $this->assertCount(1, $client->keys(new AccessTokenKey(2, $teamId, '*')));
        }
    }

    function testTtlLeftInRedis()
    {
        GoalousDateTime::setTestNow(GoalousDateTime::now());
        $authorizedAccessInfo = AccessAuthenticator::publish($userId = 1, $teamId = 2);
        $validTokenButNotInRedis = $authorizedAccessInfo->token();
        $jwtAuth = $authorizedAccessInfo->getJwtAuthentication();

        $client = new AccessTokenClient();
        $key = new AccessTokenKey(
            $authorizedAccessInfo->getUserId(),
            $authorizedAccessInfo->getTeamId(),
            $jwtAuth->getJwtId()
        );

        // Calling from \Redis class, ttl will not use in code currently
        $ttl = $client->getRedis()->ttl($key->get());
        $this->assertTrue(is_int($ttl));
    }

    /**
     * @expectedException \Goalous\Exception\Auth\AuthNotManagedException
     */
    function testExceptionThrowsNotManaged()
    {
        $authorizedAccessInfo = AccessAuthenticator::publish($userId = 1, $teamId = 2);
        $validTokenButNotInRedis = $authorizedAccessInfo->token();
        $jwtAuth = $authorizedAccessInfo->getJwtAuthentication();

        // This verify() will not throw any error
        try {
            AccessAuthenticator::verify($validTokenButNotInRedis);
            $this->assertTrue(true);
        } catch (Throwable $e) {
            $this->fail();
        }

        // Deleting the registered token in the Redis
        $client = new AccessTokenClient();
        $deletedCount = $client->del(new AccessTokenKey(
            $authorizedAccessInfo->getUserId(),
            $authorizedAccessInfo->getTeamId(),
            $jwtAuth->getJwtId()
        ));
        $this->assertEquals(1, $deletedCount);

        // verify will throw exception
        AccessAuthenticator::verify($validTokenButNotInRedis);
    }

    /**
     * @expectedException \Goalous\Exception\Auth\AuthOutOfTermException
     */
    function testExceptionThrowsOnExpiredToken()
    {
        $lastMonth = GoalousDateTime::now()->subMonth(1);
        GoalousDateTime::setTestNow($lastMonth);

        $authorizedAccessInfo = AccessAuthenticator::publish($userId = 1, $teamId = 2);
        AccessAuthenticator::verify($authorizedAccessInfo->token());
    }

    /**
     * @expectedException \Goalous\Exception\Auth\AuthOutOfTermException
     */
    function testExceptionThrowsOnIssueBeforeStart()
    {
        $lastMonth = GoalousDateTime::now()->addDays(1);
        GoalousDateTime::setTestNow($lastMonth);

        $authorizedAccessInfo = AccessAuthenticator::publish($userId = 1, $teamId = 2);
        AccessAuthenticator::verify($authorizedAccessInfo->token());
    }

    /**
     * @expectedException \Goalous\Exception\Auth\AuthFailedException
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
