<?php
App::uses('GoalousTestCase', 'Test');
App::uses('ActionResultMember', 'Model');

/**
 * ActionResultFile Test Case
 *
 * @property ActionResultFile $ActionResultFile
 */
class ActionResultMemberTest extends GoalousTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.action_result_member',
    );

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
    }

    function test_addMember()
    {
        $actionResultId = 1;
        $teamId = 2;

        /** @var ActionResultMember $ActionResultMember */
        $ActionResultMember = ClassRegistry::init('ActionResultMember');

        $actionMembers = $ActionResultMember->getActionResultMembersByActionResultId($actionResultId);
        $this->assertCount(0, $actionMembers);

        $r = $ActionResultMember->addMember($actionResultId, 3, $teamId, true);

        $actionMembers = $ActionResultMember->getActionResultMembersByActionResultId($actionResultId);
        $this->assertCount(1, $actionMembers);

        $ActionResultMember->addMember($actionResultId, 4, $teamId, false);
        $ActionResultMember->addMember($actionResultId, 5, $teamId, false);

        $actionMembers = $ActionResultMember->getActionResultMembersByActionResultId($actionResultId);
        $this->assertCount(3, $actionMembers);
    }

    /**
     * @expectedException PDOException
     */
    function test_addMember_duplicate()
    {
        $actionResultId = 1;
        $teamId = 2;

        /** @var ActionResultMember $ActionResultMember */
        $ActionResultMember = ClassRegistry::init('ActionResultMember');

        $ActionResultMember->addMember($actionResultId, 1, $teamId, true);
        $ActionResultMember->addMember($actionResultId, 1, $teamId, true);
    }

}
