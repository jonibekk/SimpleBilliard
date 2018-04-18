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
     * @expectedException JwtSignatureException
     * @expectedExceptionMessage Signature verification failed
     */
    function test_tokenEditedHeader()
    {
        $jwtToken = new JwtAuthentication($userId = 1, $teamId = 1);
        $correctToken = $jwtToken->token();
        $separated = explode('.', $correctToken);
        $jsonStringHeader = base64_decode($separated[0]);// Getting header
        $headerData = json_decode($jsonStringHeader, true);
        $headerData['typ'] = 'NOT_JWT';
        $editedJsonStringHeader = json_encode($headerData);
        $editedHeader = base64_encode($editedJsonStringHeader);
        $separated[0] = $editedHeader;

        JwtAuthentication::decode(implode('.', $separated));
    }

    /**
     * @expectedException JwtSignatureException
     * @expectedExceptionMessage Signature verification failed
     */
    function test_tokenEditedPayload()
    {
        $jwtToken = new JwtAuthentication($userId = 1, $teamId = 1);
        $correctToken = $jwtToken->token();
        $separated = explode('.', $correctToken);
        $jsonStringPayload = base64_decode($separated[1]);// Getting Payload
        $payload = json_decode($jsonStringPayload, true);
        $payload[JwtAuthentication::PAYLOAD_NAMESPACE]['user_id'] = ($editedUserId = 2);
        $editedJsonStringPayload = json_encode($payload);
        $editedPayload = base64_encode($editedJsonStringPayload);
        $separated[1] = $editedPayload;

        JwtAuthentication::decode(implode('.', $separated));
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
