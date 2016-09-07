<?php App::uses('GoalousTestCase', 'Test');
App::uses('MemberType', 'Model');

/**
 * MemberType Test Case
 *
 * @property MemberType $MemberType
 */
class MemberTypeTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.member_type',
        'app.team',
        'app.badge',
        'app.user',
        'app.email',
        'app.notify_setting',
        'app.comment_like',
        'app.comment',
        'app.post',
        'app.goal',

        'app.goal_category',
        'app.key_result',
        'app.action_result',
        'app.collaborator',
        'app.follower',
        'app.post_share_user',
        'app.post_share_circle',
        'app.circle',
        'app.circle_member',
        'app.post_like',
        'app.post_read',
        'app.comment_mention',
        'app.given_badge',
        'app.post_mention',
        'app.comment_read',

        'app.oauth_token',
        'app.team_member',
        'app.group',
        'app.job_category',
        'app.local_name',
        'app.invite',
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->MemberType = ClassRegistry::init('MemberType');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->MemberType);

        parent::tearDown();
    }

    function testGetByName()
    {
        $this->_setDefault();
        $this->assertEmpty($this->MemberType->getByName('test'));
        $this->MemberType->saveNewType('test');
        $this->assertNotEmpty($this->MemberType->getByName('test'));
    }

    function testSaveNewType()
    {
        $this->_setDefault();
        $actual = $this->MemberType->saveNewType('test');
        $this->assertNotEmpty($actual);
    }

    function testGetByNameIfNotExistsSave()
    {
        $this->_setDefault();
        $init_count = $this->MemberType->find('count');
        $actual = $this->MemberType->getByNameIfNotExistsSave('test1');
        $count = $this->MemberType->find('count');
        $this->assertNotEmpty($actual);
        $this->assertEquals(1, $count - $init_count);

        $init_count = $this->MemberType->find('count');
        $actual = $this->MemberType->getByNameIfNotExistsSave('test1');
        $count = $this->MemberType->find('count');
        $this->assertNotEmpty($actual);
        $this->assertEquals($init_count, $count);
    }

    function _setDefault()
    {
        $this->MemberType->current_team_id = 1;
        $this->MemberType->my_uid = 1;
    }

}
