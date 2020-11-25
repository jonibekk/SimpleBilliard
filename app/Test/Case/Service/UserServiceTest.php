<?php
App::uses('GoalousTestCase', 'Test');
App::uses('User', 'Model');
App::import('Service', 'UserService');

/**
 * Class UserServiceTest
 * @property UserService $UserService
 * @property User $User
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
        'app.team_config',
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->UserService = ClassRegistry::init('UserService');
        $this->User = ClassRegistry::init('User');
    }

    function test_get()
    {
        $modelName = 'User';
        $id = 1;
        $teamId = 1;

        /* First data: auth user */
        // Save cache
        $req = new UserResourceRequest($id, $teamId, true);
        $data = $this->UserService->get($req);
        $this->assertNotEmpty($data);
        $this->assertSame($data['display_username'], 'firstname lastname');
        $this->assertSame($data['birth_day'], '2014-05-22');
        $cacheList = $this->UserService->getCacheList();

        // Check if basic data and cache data are same
        list($ret1, $ret2) = $this->getUserDataForCompare($data, $cacheList[$modelName][$id], $this->User->loginUserFields);
        $ret2['language'] = LangUtil::convertISOFrom3to2($ret2['language']);
        $this->assertSame($ret1, $ret2);
        // Check if extend data is correct
        $this->assertSame($data['current_team_id'], 1);

        // Check data is as same as data getting from db directly
        $ret = $this->User->useType()->find('first', [
            'conditions' => ['id' => $id],
            'fields' => $this->User->loginUserFields
        ])[$modelName];
        // Extract only db record columns(exclude additional data. e.g. img_url)
        $tmp = array_intersect_key($data, $ret);
        $ret['language'] = LangUtil::convertISOFrom3to2($ret['language']);
        $this->assertSame($tmp, $ret);


        // Get from cache
        $data = $this->UserService->get($req);
        list($ret1, $ret2) = $this->getUserDataForCompare($data, $cacheList[$modelName][$id], $this->User->loginUserFields);
        $ret2['language'] = LangUtil::convertISOFrom3to2($ret2['language']);
        $this->assertSame($ret1, $ret2);

        /* Not auth user */
        $id = 2;
        $req = new UserResourceRequest($id, $teamId, false);
        $data = $this->UserService->get($req);
        $this->assertNotEmpty($data);
        $this->assertSame($data['display_username'], 'firstname lastname');
        $this->assertArrayNotHasKey('birth_day', $data);
        $cacheList = $this->UserService->getCacheList();
        $this->assertArrayNotHasKey('birth_day', $cacheList);
        list($ret1, $ret2) = $this->getUserDataForCompare($data, $cacheList[$modelName][$id], $this->User->profileFields);
        $this->assertSame($ret1, $ret2);

        // Get from cache
        $data = $this->UserService->get($req);
        list($ret1, $ret2) = $this->getUserDataForCompare($data, $cacheList[$modelName][$id], $this->User->loginUserFields);
        $this->assertSame($ret1, $ret2);


        /* Empty */
        $id = 0;
        $req = new UserResourceRequest($id, $teamId, true);
        $data = $this->UserService->get($req);
        $this->assertSame($data, []);
        $cacheList = $this->UserService->getCacheList();
        $this->assertArrayNotHasKey($id, $cacheList[$modelName]);

        $id = 9999999;
        $req = new UserResourceRequest($id, $teamId, true);
        $data = $this->UserService->get($req);
        $this->assertSame($data, []);
        $cacheList = $this->UserService->getCacheList();
        $this->assertSame($data, $cacheList[$modelName][$id]);
    }

    private function getUserDataForCompare($data, $cachedData, $fields)
    {
        $baseData = array_fill_keys($fields, null);
        $fieldsData = array_intersect_key($data, $baseData);
        $cachedFieldsData = array_intersect_key($cachedData, $baseData);

        return [
            $fieldsData,
            $cachedFieldsData
        ];
    }

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
