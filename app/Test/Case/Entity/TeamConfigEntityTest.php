<?php
App::uses('GoalousTestCase', 'Test');
App::uses('TeamConfigEntity', 'Model/Entity');
App::uses('TeamConfig', 'Model');

class TeamConfigEntityTest extends GoalousTestCase
{
    public $fixtures = array(
        'app.team_config',
    );

    function test_TeamConfigUpdateRemove()
    {
        /** @var TeamConfig $TeamConfig */
        $TeamConfig = ClassRegistry::init('TeamConfig');

        $teamId = 1;
        $duration = 60;

        $this->assertCount(0, $TeamConfig->find('all'));

        for ($i = 0; $i < 3; $i++) {
            // Get config
            $teamConfig = $TeamConfig->getConfig($teamId);
            $this->assertNull($teamConfig->getVideoDurationMaxSecond());

            $durationUpdated = $duration + $i;
            // Update config
            $teamConfig->setVideoDurationMaxSecond($durationUpdated);
            $TeamConfig->updateConfig($teamId, $teamConfig);

            // Check updated
            $teamConfig = $TeamConfig->getConfig($teamId);
            $this->assertSame($durationUpdated, $teamConfig->getVideoDurationMaxSecond());

            // Removing config
            $teamConfig->setVideoDurationMaxSecond(null);
            $TeamConfig->updateConfig($teamId, $teamConfig);
        }

        $this->assertCount(1, $TeamConfig->find('all'));
    }

    /**
     * @expectedException RuntimeException
     */
    function test_ParsingErrorJson()
    {
        $teamConfigValue = TeamConfigValues::createFromJsonString('abcdef');
    }

    function test_ErroredConfig()
    {
        $teamId = 1;
        /** @var TeamConfig $TeamConfig */
        $TeamConfig = ClassRegistry::init('TeamConfig');
        $TeamConfig->create();
        $TeamConfig->save([
            'team_id' => $teamId,
            'config' => 'invalid_json_string'
        ]);
        $teamConfig = $TeamConfig->getConfig($teamId);
        $this->assertTrue($teamConfig->isErroredConfig());


        $teamId = 2;
        /** @var TeamConfig $TeamConfig */
        $TeamConfig = ClassRegistry::init('TeamConfig');
        $TeamConfig->create();
        $TeamConfig->save([
            'team_id' => $teamId,
            'config' => '{}'
        ]);
        $teamConfig = $TeamConfig->getConfig($teamId);
        $this->assertFalse($teamConfig->isErroredConfig());
    }

    function test_TeamConfigValues()
    {
        $fileSizeMaxMbVideo = 300;
        $videoDurationMaxSecond = 1200;

        $teamConfigValue = new TeamConfigValues();
        $teamConfigValue->setFileSizeMbMaxVideo($fileSizeMaxMbVideo);
        $teamConfigValue->setVideoDurationMaxSecond($videoDurationMaxSecond);
        $jsonString = json_encode($teamConfigValue);

        $teamConfigValueDecoded = TeamConfigValues::createFromJsonString($jsonString);
        $this->assertSame($fileSizeMaxMbVideo, $teamConfigValueDecoded->getFileSizeMbMaxVideo());
        $this->assertSame($videoDurationMaxSecond, $teamConfigValueDecoded->getVideoDurationMaxSecond());
        $this->assertSame($jsonString, json_encode($teamConfigValueDecoded));
    }

    function test_TeamConfigValues_empty()
    {
        $teamConfigValue = new TeamConfigValues();
        $jsonString = json_encode($teamConfigValue);

        $teamConfigValueDecoded = TeamConfigValues::createFromJsonString($jsonString);
        $this->assertNull($teamConfigValueDecoded->getFileSizeMbMaxVideo());
        $this->assertNull($teamConfigValueDecoded->getVideoDurationMaxSecond());
        $this->assertSame($jsonString, json_encode($teamConfigValueDecoded));
    }

    function test_a()
    {
        $teamConfigValue = new TeamConfigValues();
        $teamConfigValue->setFileSizeMbMaxVideo(300);
        $teamConfigValue->setVideoDurationMaxSecond(1200);
    }
}
