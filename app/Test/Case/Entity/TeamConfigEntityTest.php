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
}
