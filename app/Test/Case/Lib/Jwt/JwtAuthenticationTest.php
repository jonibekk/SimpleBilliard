<?php

App::uses('GoalousTestCase', 'Test');
App::uses('JwtAuthentication', 'Lib/Jwt');

/**
 * Class JwtAuthenticationTest
 */
class JwtAuthenticationTest extends GoalousTestCase
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
     * test of encode/decode
     */
    function test_create_decode()
    {
        $jwtToken = new JwtAuthentication($userId = 1, $teamId = 1);
        $token = $jwtToken->token();

        $jwtTokenDecoded = JwtAuthentication::decode($token);

        $this->assertSame($jwtToken->getJwtId(), $jwtTokenDecoded->getJwtId());
        $this->assertSame($jwtToken->getTeamId(), $jwtTokenDecoded->getTeamId());
        $this->assertSame($jwtToken->getUserId(), $jwtTokenDecoded->getUserId());
        $this->assertSame($jwtToken->expireAt()->getTimestamp(), $jwtTokenDecoded->expireAt()->getTimestamp());
        $this->assertSame($jwtToken->createdAt()->getTimestamp(), $jwtTokenDecoded->createdAt()->getTimestamp());
        $this->assertSame($jwtTokenDecoded->token(), $token);
    }

    /**
     * @expectedException JwtSignatureException
     * @expectedExceptionMessage Signature verification failed
     */
    function test_invalidSignature()
    {
        $jwtToken = new JwtAuthentication($userId = 1, $teamId = 1);
        $correctToken = $jwtToken->token();
        $invalidSignatureToken = $correctToken.'A';

        JwtAuthentication::decode($invalidSignatureToken);
    }

    /**
     * @expectedException JwtExpiredException
     */
    function test_expire()
    {
        $lastMonth = GoalousDateTime::now()->subMonth(1);
        GoalousDateTime::setTestNow($lastMonth);
        $jwtToken = new JwtAuthentication($userId = 1, $teamId = 1);
        $oldToken = $jwtToken->token();

        JwtAuthentication::decode($oldToken);
    }

    /**
     * @expectedException JwtExpiredException
     */
    function test_tokenIsNotVerified()
    {
        $nextMonth = GoalousDateTime::now()->addMonth(1);
        GoalousDateTime::setTestNow($nextMonth);
        $jwtToken = new JwtAuthentication($userId = 1, $teamId = 1);
        $futureToken = $jwtToken->token();

        JwtAuthentication::decode($futureToken);
    }

    /**
     * @expectedException JwtException
     */
    function test_decodingNotToken()
    {
        JwtAuthentication::decode('NOT_A_VALID_TOKEN_STRING');
    }
}
