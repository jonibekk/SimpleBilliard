<?php App::uses('GoalousTestCase', 'Test');
App::uses('PlainCircle', 'Model');
App::uses('CircleMember', 'Model');
App::uses('TeamMember', 'Model');
App::uses('User', 'Model');

use Goalous\Enum as Enum;

/**
 * PlainCircle Test Case
 *
 * @property PlainCircle  $PlainCircle
 * @property CircleMember $CircleMember
 * @property TeamMember   $TeamMember
 * @property User         $User
 */
class PlainCircleTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.team_member',
        'app.user',
        'app.team',
        'app.circle',
        'app.circle_member',
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->PlainCircle = ClassRegistry::init('PlainCircle');
        $this->CircleMember = ClassRegistry::init('CircleMember');
        $this->TeamMember = ClassRegistry::init('TeamMember');
        $this->User = ClassRegistry::init('User');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->PlainCircle);

        parent::tearDown();
    }

    public function test_getMembers()
    {
        $teamId = 1;
        $allMemberIds = $this->CircleMember->find('all', [
            'fields'     => ['circle_id', 'user_id'],
            'conditions' => ['team_id' => $teamId]
        ]);
//print_r($allMemberIds);

        // Public circle
        $circleId = 1;
        $res = $this->PlainCircle->getMembers($circleId);
        $this->assertEquals($res, [1, 2, 12]);

        // Secret circle
        $circleId = 4;
        $res = $this->PlainCircle->getMembers($circleId);
        $this->assertEquals($res, [1, 2]);

        /* Team member status: inactive */
        $this->TeamMember->updateAll(
            ['status' => Enum\Model\TeamMember\Status::INACTIVE],
            ['user_id' => 1, 'team_id' => 1]
        );

        // Public circle
        $circleId = 1;
        $res = $this->PlainCircle->getMembers($circleId);
        $this->assertEquals($res, [2, 12]);

        // Secret circle
        $circleId = 4;
        $res = $this->PlainCircle->getMembers($circleId);
        $this->assertEquals($res, [2]);

        /* Team member status: invited */
        $this->TeamMember->updateAll(
            ['status' => Enum\Model\TeamMember\Status::INVITED],
            ['user_id' => 1, 'team_id' => 1]
        );

        // Public circle
        $circleId = 1;
        $res = $this->PlainCircle->getMembers($circleId);
        $this->assertEquals($res, [2, 12]);

        // Secret circle
        $circleId = 4;
        $res = $this->PlainCircle->getMembers($circleId);
        $this->assertEquals($res, [2]);

        /* Team member status: invited */
        $this->TeamMember->updateAll(
            ['status' => Enum\Model\TeamMember\Status::ACTIVE, 'del_flg' => true],
            ['user_id' => 1, 'team_id' => 1]
        );

        // Public circle
        $circleId = 1;
        $res = $this->PlainCircle->getMembers($circleId);
        $this->assertEquals($res, [2, 12]);

        // Secret circle
        $circleId = 4;
        $res = $this->PlainCircle->getMembers($circleId);
        $this->assertEquals($res, [2]);

        /* User is not active */
        $this->User->updateAll(
            ['active_flg' => false],
            ['id' => 2]
        );

        // Public circle
        $circleId = 1;
        $res = $this->PlainCircle->getMembers($circleId);
        $this->assertEquals($res, [12]);

        // Secret circle
        $circleId = 4;
        $res = $this->PlainCircle->getMembers($circleId);
        $this->assertEquals($res, []);

        /* User is deleted */
        $this->User->updateAll(
            ['active_flg' => true, 'del_flg' => true],
            ['id' => 2]
        );

        // Public circle
        $circleId = 1;
        $res = $this->PlainCircle->getMembers($circleId);
        $this->assertEquals($res, [12]);

        // Secret circle
        $circleId = 4;
        $res = $this->PlainCircle->getMembers($circleId);
        $this->assertEquals($res, []);
    }
}
