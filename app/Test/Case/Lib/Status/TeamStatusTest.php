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
        $teamStatus->setIsTeamPaidPlusPlan(true);
        $teamStatus->setEnabledVideoPostInEnvironment(true);

        $this->assertFalse($teamStatus->isTeamPlanRegular());
        $this->assertTrue($teamStatus->isTeamPaid());
        $this->assertTrue($teamStatus->getTeamPlan()->equals(Enum\TeamPlan::PAID_PLUS()));
        $this->assertTrue($teamStatus->isAbleToPostVideo());
        $this->assertTrue($teamStatus->getTranscodeQuality()->equals(Enum\TranscodePattern::FULL()));
    }

    function test_regular()
    {
        $teamStatus = TeamStatus::getCurrentTeam();
        $teamStatus->setServiceUseStatus(Enum\Team\ServiceUseStatus::FREE_TRIAL());
        $teamStatus->setIsTeamCampaign(false);
        $teamStatus->setIsTeamPaidPlusPlan(false);
        $teamStatus->setEnabledVideoPostInEnvironment(true);

        $this->assertTrue($teamStatus->isTeamPlanRegular());
        $this->assertFalse($teamStatus->isTeamPaid());
        $this->assertTrue($teamStatus->getTeamPlan()->equals(Enum\TeamPlan::REGULAR()));
        $this->assertTrue($teamStatus->isAbleToPostVideo());
        $this->assertTrue($teamStatus->getTranscodeQuality()->equals(Enum\TranscodePattern::LIMITED()));
    }

    function test_regular_disable_video_post()
    {
        $teamStatus = TeamStatus::getCurrentTeam();
        $teamStatus->setServiceUseStatus(Enum\Team\ServiceUseStatus::FREE_TRIAL());
        $teamStatus->setIsTeamCampaign(false);
        $teamStatus->setIsTeamPaidPlusPlan(false);
        $teamStatus->setEnabledVideoPostInEnvironment(false);

        $this->assertTrue($teamStatus->isTeamPlanRegular());
        $this->assertFalse($teamStatus->isTeamPaid());
        $this->assertTrue($teamStatus->getTeamPlan()->equals(Enum\TeamPlan::REGULAR()));
        $this->assertFalse($teamStatus->isAbleToPostVideo());
        $this->assertTrue($teamStatus->getTranscodeQuality()->equals(Enum\TranscodePattern::LIMITED()));
    }
}
