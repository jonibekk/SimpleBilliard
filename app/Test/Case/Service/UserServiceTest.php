<?php
App::uses('GoalousTestCase', 'Test');
App::uses('User', 'Model');
App::import('Service', 'UserService');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/10/01
 * Time: 15:20
 */
class UserServiceTest extends GoalousTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.user',
        'app.team',
        'app.team_member',
    );

    public function test_updateDefaultTeam_success()
    {
        $userId = 1;
        $newTeamId = 1931;

        /** @var User $User */
        $User = ClassRegistry::init('User');

        /** @var UserService $UserService */
        $UserService = ClassRegistry::init('UserService');

        $UserService->updateDefaultTeam($userId, $newTeamId);

        $user = $User->getById($userId);

        $this->assertEquals($newTeamId, $user['default_team_id']);
    }
}