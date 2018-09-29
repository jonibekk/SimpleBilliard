<?php

App::uses('GoalousTestCase', 'Test');
App::uses('SampleRedisClient', 'Lib/Cache/Redis/Sample');

/**
 * Class SampleRedisClientTest
 */
class SampleRedisClientTest extends GoalousTestCase
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

        $client = new SampleRedisClient();
        $client->del(new SampleRedisKey('*', '*'));
    }

    function testSimpleReadWrite()
    {
        // create client, key, data
        $client = new SampleRedisClient();
        $key = new SampleRedisKey($userId = 1, $teamId = 2);
        $data = new SampleRedisData("data:" . time());

        // write
        $this->assertTrue($client->write($key, $data));

        // read
        $readData = $client->read($key);
        $this->assertSame($data->getStringData(), $readData->getStringData());
    }

    function testGetKeysWithWildCard()
    {
        $client = new SampleRedisClient();
        $data = new SampleRedisData("data:" . time());

        // creating some keys
        $keys = [];
        $userId = 1;
        foreach (range(1, 5) as $teamId) {
            array_push($keys, new SampleRedisKey($userId, $teamId));
        }

        // writing multiple keys with same data
        foreach ($keys as $key) {
            $client->write($key, $data);
        }

        foreach ($keys as $key) {
            $this->assertCount(1, $client->keys($key));
        }
        $this->assertCount(5, $client->keys(new SampleRedisKey('*', '*')));
    }

    function testWildCardReadDelete()
    {
        $client = new SampleRedisClient();

        // deleting all data before test
        $client->del(new SampleRedisKey('*', '*'));

        $data = new SampleRedisData("data:" . time());

        // creating some keys
        $keys = [];
        foreach (range(1, 5) as $userId) {
            foreach (range(1, 5) as $teamId) {
                array_push($keys, new SampleRedisKey($userId, $teamId));
            }
        }

        // writing multiple keys with same data
        foreach ($keys as $key) {
            $client->write($key, $data);
        }

        $allKey = new SampleRedisKey('*', '*');
        $this->assertSame(25, $client->count($allKey));

        // test single delete
        $this->assertSame(1, $client->del(new SampleRedisKey(1, 1)));
        $this->assertSame(24, $client->count($allKey));

        // test all user delete
        $this->assertSame(4, $client->del(new SampleRedisKey(1, '*')));
        $this->assertSame(20, $client->count($allKey));

        // test all team delete
        $this->assertSame(4, $client->del(new SampleRedisKey('*', 2)));
        $this->assertSame(16, $client->count($allKey));

        // test all delete
        $this->assertSame(16, $client->del($allKey));
        $this->assertSame(0, $client->count($allKey));
    }

    function testRemember()
    {
        $client = new SampleRedisClient();
        $key = new SampleRedisKey($userId = 1, $teamId = 2);

        // closure will call
        $called = false;
        $dataStringFirst = "remember:" . rand();
        $dataFirst = $client->remember($key, (function() use (&$called, $dataStringFirst) {
            $called = true;
            return new SampleRedisData($dataStringFirst);
        }));
        $this->assertTrue($called);
        $this->assertSame($dataFirst->getStringData(), $dataStringFirst);

        // closure will not call
        $called = false;
        $dataStringSecond = "remember:" . rand();
        $dataSecond = $client->remember($key, (function() use (&$called, $dataStringSecond) {
            $called = true;
            return new SampleRedisData($dataStringSecond);
        }));
        $this->assertFalse($called);
        $this->assertSame($dataSecond->getStringData(), $dataStringFirst);
        $this->assertNotSame($dataSecond->getStringData(), $dataStringSecond);

    }
}
