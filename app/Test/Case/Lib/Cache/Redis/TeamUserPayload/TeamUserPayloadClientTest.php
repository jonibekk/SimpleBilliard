<?php

App::uses('GoalousTestCase', 'Test');
App::uses('TeamUserPayloadClient', 'Lib/Cache/Redis/TeamUserPayload');

/**
 * Class TeamUserPayloadClientTest
 */
class TeamUserPayloadClientTest extends GoalousTestCase
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

        // Removing Redis keys related to test
        /** @var $redis Redis */
        $redis = ConnectionManager::getDataSource('redis');
        $prefix = $redis->getOption(Redis::OPT_PREFIX);
        $keys = $redis->keys('*:team_user_payload:*');
        foreach ($keys as $i => $key) {
            if (0 === strpos($key, $prefix)) {
                $keys[$i] = substr($key, strlen($prefix));
            }
        }
        $redis->del($keys);
    }

    function testSimple()
    {
        $client = new TeamUserPayloadClient();
        $key = new TeamUserPayloadRedisKey($teamId = 1, $userId = 1);

        $payload = $client->read($key);
        $this->assertNull($payload->get(TeamUserPayloadType::BANNER_ALERT_SERVICE_USE_STATUS_HIDE()));
        $this->assertTrue($payload->get(TeamUserPayloadType::BANNER_ALERT_SERVICE_USE_STATUS_HIDE(), true));
        $this->assertFalse($payload->get(TeamUserPayloadType::BANNER_ALERT_SERVICE_USE_STATUS_HIDE(), false));

        $client->write($key, TeamUserPayloadType::BANNER_ALERT_SERVICE_USE_STATUS_HIDE(), true);

        $payload = $client->read($key);
        $this->assertTrue($payload->get(TeamUserPayloadType::BANNER_ALERT_SERVICE_USE_STATUS_HIDE()));
        $this->assertTrue($payload->get(TeamUserPayloadType::BANNER_ALERT_SERVICE_USE_STATUS_HIDE(), true));
        $this->assertTrue($payload->get(TeamUserPayloadType::BANNER_ALERT_SERVICE_USE_STATUS_HIDE(), false));

        $client->write($key, TeamUserPayloadType::BANNER_ALERT_SERVICE_USE_STATUS_HIDE(), false);

        $payload = $client->read($key);
        $this->assertFalse($payload->get(TeamUserPayloadType::BANNER_ALERT_SERVICE_USE_STATUS_HIDE()));
        $this->assertFalse($payload->get(TeamUserPayloadType::BANNER_ALERT_SERVICE_USE_STATUS_HIDE(), false));
        $this->assertFalse($payload->get(TeamUserPayloadType::BANNER_ALERT_SERVICE_USE_STATUS_HIDE(), true));
    }

    function testValueMultipleTeamUser()
    {
        $client = new TeamUserPayloadClient();
        $keyTeam1User1 = new TeamUserPayloadRedisKey($teamId = 1, $userId = 1);
        $keyTeam1User2 = new TeamUserPayloadRedisKey($teamId = 1, $userId = 2);
        $keyTeam2User1 = new TeamUserPayloadRedisKey($teamId = 2, $userId = 1);

        $client->write($keyTeam1User1, TeamUserPayloadType::BANNER_ALERT_SERVICE_USE_STATUS_HIDE(), true);

        $payload = $client->read($keyTeam1User1);
        $this->assertTrue($payload->get(TeamUserPayloadType::BANNER_ALERT_SERVICE_USE_STATUS_HIDE()));
        $payload = $client->read($keyTeam1User2);
        $this->assertNull($payload->get(TeamUserPayloadType::BANNER_ALERT_SERVICE_USE_STATUS_HIDE()));
        $payload = $client->read($keyTeam2User1);
        $this->assertNull($payload->get(TeamUserPayloadType::BANNER_ALERT_SERVICE_USE_STATUS_HIDE()));
    }

    function testValueMultipleValues()
    {
        $client = new TeamUserPayloadClient();
        $key = new TeamUserPayloadRedisKey($teamId = 1, $userId = 1);

        // Assert empty
        $payload = $client->read($key);
        $this->assertNull($payload->get(TeamUserPayloadType::BANNER_ALERT_SERVICE_USE_STATUS_HIDE()));
        $this->assertNull($payload->get(TeamUserPayloadType::GOAL_CREATE_GUIDE_HIDE()));

        // Assert both true
        $client->write($key, TeamUserPayloadType::BANNER_ALERT_SERVICE_USE_STATUS_HIDE(), true);
        $client->write($key, TeamUserPayloadType::GOAL_CREATE_GUIDE_HIDE(), true);
        $payload = $client->read($key);
        $this->assertTrue($payload->get(TeamUserPayloadType::BANNER_ALERT_SERVICE_USE_STATUS_HIDE()));
        $this->assertTrue($payload->get(TeamUserPayloadType::GOAL_CREATE_GUIDE_HIDE()));

        // Assert true & false
        $client->write($key, TeamUserPayloadType::BANNER_ALERT_SERVICE_USE_STATUS_HIDE(), false);
        $payload = $client->read($key);
        $this->assertFalse($payload->get(TeamUserPayloadType::BANNER_ALERT_SERVICE_USE_STATUS_HIDE()));
        $this->assertTrue($payload->get(TeamUserPayloadType::GOAL_CREATE_GUIDE_HIDE()));

        // Assert both false
        $client->write($key, TeamUserPayloadType::GOAL_CREATE_GUIDE_HIDE(), false);
        $payload = $client->read($key);
        $this->assertFalse($payload->get(TeamUserPayloadType::BANNER_ALERT_SERVICE_USE_STATUS_HIDE()));
        $this->assertFalse($payload->get(TeamUserPayloadType::GOAL_CREATE_GUIDE_HIDE()));
    }
}
