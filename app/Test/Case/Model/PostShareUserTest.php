<?php App::uses('GoalousTestCase', 'Test');
App::uses('PostShareUser', 'Model');

/**
 * PostShareUser Test Case
 *
 * @property PostShareUser $PostShareUser
 */
class PostShareUserTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.post_share_user',
        'app.post',
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->PostShareUser = ClassRegistry::init('PostShareUser');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->PostShareUser);

        parent::tearDown();
    }

    public function testAdd()
    {
        $this->PostShareUser->my_uid = 1;
        $this->PostShareUser->current_team_id = 1;
        $this->PostShareUser->add(1, []);
        $this->PostShareUser->add(1, [1]);
    }

    public function testGetPostIdListByUserId()
    {
        $post_id = 999;
        $this->PostShareUser->current_team_id = 888;
        $user_id = 777;
        $data = [
            'post_id' => $post_id,
            'team_id' => 888,
            'user_id' => $user_id,
        ];
        $this->PostShareUser->save($data);
        $res = $this->PostShareUser->getPostIdListByUserId($user_id);
        $this->assertContains($post_id, $res);
    }
}
