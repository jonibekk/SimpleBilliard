<?php
App::uses('GoalousTestCase', 'Test');
App::uses('AppUtil', 'Util');
App::uses('TeamStatus', 'Lib/Status');

use Goalous\Model\Enum as Enum;

/**
 * TeamStatusTest Test Case
 */
class TeamStatusTest extends GoalousTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.campaign_team',
        'app.team',
        'app.experiment',
    ];

    function tearDown()
    {
        parent::tearDown();
        TeamStatus::reset();
    }

    function test_paid()
    {
        $teamStatus = TeamStatus::getCurrentTeam();
        $teamStatus->setServiceUseStatus(Enum\Team\ServiceUseStatus::PAID());
        $teamStatus->setIsTeamCampaign(true);

        $this->assertFalse($teamStatus->isTeamPlanRegular());
        $this->assertTrue($teamStatus->isTeamPaid());
        $this->assertTrue($teamStatus->isTeamCampaign());
        $this->assertTrue($teamStatus->getTeamPlan()->equals(Enum\TeamPlan::PAID()));
        $this->assertTrue($teamStatus->getTranscodeQuality()->equals(Enum\TranscodePattern::LIMITED()));
    }

    function test_regular()
    {
        $teamStatus = TeamStatus::getCurrentTeam();
        $teamStatus->setServiceUseStatus(Enum\Team\ServiceUseStatus::FREE_TRIAL());
        $teamStatus->setIsTeamCampaign(false);

        $this->assertTrue($teamStatus->isTeamPlanRegular());
        $this->assertFalse($teamStatus->isTeamPaid());
        $this->assertFalse($teamStatus->isTeamCampaign());
        $this->assertTrue($teamStatus->getTeamPlan()->equals(Enum\TeamPlan::REGULAR()));
        $this->assertTrue($teamStatus->getTranscodeQuality()->equals(Enum\TranscodePattern::LIMITED()));
    }

    function test_regular_disable_video_post()
    {
        $teamStatus = TeamStatus::getCurrentTeam();
        $teamStatus->setServiceUseStatus(Enum\Team\ServiceUseStatus::FREE_TRIAL());
        $teamStatus->setIsTeamCampaign(false);

        $this->assertTrue($teamStatus->isTeamPlanRegular());
        $this->assertFalse($teamStatus->isTeamPaid());
        $this->assertFalse($teamStatus->isTeamCampaign());
        $this->assertTrue($teamStatus->getTeamPlan()->equals(Enum\TeamPlan::REGULAR()));
        $this->assertTrue($teamStatus->getTranscodeQuality()->equals(Enum\TranscodePattern::LIMITED()));
    }

    function test_initializeFromTeamId_regular()
    {
        $teamStatus = TeamStatus::getCurrentTeam();
        $teamStatus->initializeByTeamId(1);

        $this->assertFalse($teamStatus->isTeamPlanRegular());
        $this->assertTrue($teamStatus->isTeamPaid());
        $this->assertFalse($teamStatus->isTeamCampaign());
        $this->assertTrue($teamStatus->getTeamPlan()->equals(Enum\TeamPlan::PAID()));
        $this->assertTrue($teamStatus->getTranscodeQuality()->equals(Enum\TranscodePattern::LIMITED()));
    }

    function test_initializeFromTeamId_free_trial()
    {
        $teamId = $this->createTeam(['service_use_status' => Team::SERVICE_USE_STATUS_FREE_TRIAL]);
        $teamStatus = TeamStatus::getCurrentTeam();
        $teamStatus->initializeByTeamId($teamId);

        $this->assertTrue($teamStatus->isTeamPlanRegular());
        $this->assertFalse($teamStatus->isTeamPaid());
        $this->assertFalse($teamStatus->isTeamCampaign());
        $this->assertTrue($teamStatus->getTeamPlan()->equals(Enum\TeamPlan::REGULAR()));
        $this->assertTrue($teamStatus->getTranscodeQuality()->equals(Enum\TranscodePattern::LIMITED()));
    }

    function test_initializeFromTeamId_read_only()
    {
        $teamId = $this->createTeam(['service_use_status' => Team::SERVICE_USE_STATUS_READ_ONLY]);
        $teamStatus = TeamStatus::getCurrentTeam();
        $teamStatus->initializeByTeamId($teamId);

        $this->assertTrue($teamStatus->isTeamPlanRegular());
        $this->assertFalse($teamStatus->isTeamPaid());
        $this->assertFalse($teamStatus->isTeamCampaign());
        $this->assertTrue($teamStatus->getTeamPlan()->equals(Enum\TeamPlan::REGULAR()));
        $this->assertTrue($teamStatus->getTranscodeQuality()->equals(Enum\TranscodePattern::LIMITED()));
    }

    function test_initializeFromTeamId_cannot_use()
    {
        $teamId = $this->createTeam(['service_use_status' => Team::SERVICE_USE_STATUS_CANNOT_USE]);
        $teamStatus = TeamStatus::getCurrentTeam();
        $teamStatus->initializeByTeamId($teamId);

        $this->assertTrue($teamStatus->isTeamPlanRegular());
        $this->assertFalse($teamStatus->isTeamPaid());
        $this->assertFalse($teamStatus->isTeamCampaign());
        $this->assertTrue($teamStatus->getTeamPlan()->equals(Enum\TeamPlan::REGULAR()));
        $this->assertTrue($teamStatus->getTranscodeQuality()->equals(Enum\TranscodePattern::LIMITED()));
    }

    function test_initializeFromTeamId_campaign_team()
    {
        $teamId = 1;
        $this->createCampaignTeam($teamId, $pricePlanGroupId = 0);
        $teamStatus = TeamStatus::getCurrentTeam();
        $teamStatus->initializeByTeamId($teamId);

        $this->assertFalse($teamStatus->isTeamPlanRegular());
        $this->assertTrue($teamStatus->isTeamPaid());
        $this->assertTrue($teamStatus->isTeamCampaign());
        $this->assertTrue($teamStatus->getTeamPlan()->equals(Enum\TeamPlan::PAID()));
        $this->assertTrue($teamStatus->getTranscodeQuality()->equals(Enum\TranscodePattern::LIMITED()));
    }

    function test_videoPostAllDisable()
    {
        $teamId = 1;
        $teamStatus = TeamStatus::getCurrentTeam();
        $teamStatus->initializeByTeamId($teamId);
        $teamStatus->setEnableVideoPostTranscodingOnEnvironment(false);
        $teamStatus->setEnableVideoPostPlayOnEnvironment(false);

        $this->assertFalse($teamStatus->canVideoPostTranscode());
        $this->assertFalse($teamStatus->canVideoPostPlay());
    }

    function test_videoPostAllEnableByEnvironment()
    {
        $teamId = 1;
        $teamStatus = TeamStatus::getCurrentTeam();
        $teamStatus->initializeByTeamId($teamId);
        $teamStatus->setEnableVideoPostTranscodingOnEnvironment(true);
        $teamStatus->setEnableVideoPostPlayOnEnvironment(true);

        $this->assertTrue($teamStatus->canVideoPostTranscode());
        $this->assertTrue($teamStatus->canVideoPostPlay());
    }

    function test_videoPostAllEnabledByExperiments()
    {
        $this->createExperiments([
            [Experiment::NAME_ENABLE_VIDEO_POST_TRANSCODING, 1],
            [Experiment::NAME_ENABLE_VIDEO_POST_PLAY, 1],
        ]);

        $teamId = 1;
        $teamStatus = TeamStatus::getCurrentTeam();
        $teamStatus->initializeByTeamId($teamId);
        $teamStatus->setEnableVideoPostTranscodingOnEnvironment(false);
        $teamStatus->setEnableVideoPostPlayOnEnvironment(false);

        $this->assertTrue($teamStatus->canVideoPostTranscode());
        $this->assertTrue($teamStatus->canVideoPostPlay());
    }

    function test_videoPostAllEnabledByExperimentsAndEnvironment()
    {
        $this->createExperiments([
            [Experiment::NAME_ENABLE_VIDEO_POST_TRANSCODING, 1],
            [Experiment::NAME_ENABLE_VIDEO_POST_PLAY, 1],
        ]);

        $teamId = 1;
        $teamStatus = TeamStatus::getCurrentTeam();
        $teamStatus->initializeByTeamId($teamId);
        $teamStatus->setEnableVideoPostTranscodingOnEnvironment(true);
        $teamStatus->setEnableVideoPostPlayOnEnvironment(true);

        $this->assertTrue($teamStatus->canVideoPostTranscode());
        $this->assertTrue($teamStatus->canVideoPostPlay());
    }

    function test_videoPostAllEnabledOnlyTranscode()
    {
        $this->createExperiments([
            [Experiment::NAME_ENABLE_VIDEO_POST_TRANSCODING, 1],
        ]);

        $teamId = 1;
        $teamStatus = TeamStatus::getCurrentTeam();
        $teamStatus->initializeByTeamId($teamId);
        $teamStatus->setEnableVideoPostTranscodingOnEnvironment(true);
        $teamStatus->setEnableVideoPostPlayOnEnvironment(false);

        $this->assertTrue($teamStatus->canVideoPostTranscode());
        $this->assertFalse($teamStatus->canVideoPostPlay());
    }

    function test_videoPostAllEnabledOnlyPlay()
    {
        $this->createExperiments([
            [Experiment::NAME_ENABLE_VIDEO_POST_PLAY, 1],
        ]);

        $teamId = 1;
        $teamStatus = TeamStatus::getCurrentTeam();
        $teamStatus->initializeByTeamId($teamId);
        $teamStatus->setEnableVideoPostTranscodingOnEnvironment(false);
        $teamStatus->setEnableVideoPostPlayOnEnvironment(true);

        $this->assertFalse($teamStatus->canVideoPostTranscode());
        $this->assertTrue($teamStatus->canVideoPostPlay());
    }
}
