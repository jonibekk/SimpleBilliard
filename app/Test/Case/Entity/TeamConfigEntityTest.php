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

            // Update config
            $teamConfig->setVideoDurationMaxSecond($duration);
            $TeamConfig->updateConfig($teamId, $teamConfig);

            // Check updated
            $teamConfig = $TeamConfig->getConfig($teamId);
            $this->assertSame($duration, $teamConfig->getVideoDurationMaxSecond());

            // Removing config
            $teamConfig->setVideoDurationMaxSecond(null);
            $TeamConfig->updateConfig($teamId, $teamConfig);
        }

        $this->assertCount(1, $TeamConfig->find('all'));
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
        var_dump(json_encode($teamConfigValue));
    }
}
