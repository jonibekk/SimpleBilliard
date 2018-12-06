<?php

App::uses('GoalousTestCase', 'Test');
App::uses('AccessTokenClient', 'Lib/Cache/Redis/AccessToken');

/**
 * Class AccessTokenClientTest
 */
class AccessTokenClientTest extends GoalousTestCase
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

        $client = new AccessTokenClient();
        $client->del(new AccessTokenKey('*', '*', '*'));
    }

    function testSimpleReadWrite()
    {
        // create client, key, data
        $client = new AccessTokenClient();
        $key = new AccessTokenKey($userId = 1, $teamId = 2, 'uuid' . rand());
        $data = new AccessTokenData();

        // write
        $this->assertTrue($client->write($key, $data));

        // read (will found)
        $readData = $client->read($key);
        $this->assertNotNull($readData);

        // read (not found)
        $readData = $client->read(new AccessTokenKey(1,3, 'uuid' . rand()));
        $this->assertNull($readData);
    }

    function testStoredDataCheck()
    {
        // create client, key, data
        $client = new AccessTokenClient();
        $key = new AccessTokenKey($userId = 1, $teamId = 2, 'uuid' . rand());
        $data = new AccessTokenData();
        $data->withUserAgent('user_agent');

        // write
        $this->assertTrue($client->write($key, $data));

        // read (will found)
        $readData = $client->read($key);
        $this->assertNotNull($readData);
        $this->assertSame('user_agent', $readData->getUserAgent());
    }
}
