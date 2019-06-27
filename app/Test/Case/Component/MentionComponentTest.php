<?php
App::uses('GoalousTestCase', 'Test');
App::uses('User', 'Model');
App::uses('LocalName', 'Model');
App::uses('CircleMember', 'Model');
App::uses('TeamMember', 'Model');
App::import('Model/Entity', 'UserEntity');
App::uses('MentionComponent', 'Controller/Component');

use Goalous\Enum as Enum;
/**
 * User Test Case
 *
 * @property User $User
 * @property LocalName $LocalName
 * @property CircleMember $CircleMember
 * @property TeamMember $TeamMember
 * @property MentionComponent $MentionComponent
 */
class MentionComponentTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.local_name',
        'app.user',
        'app.team',
        'app.circle',
        'app.team_member',
        'app.circle',
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->MentionComponent = new MentionComponent(new ComponentCollection());

        $this->User = ClassRegistry::init('User');
        $this->LocalName = ClassRegistry::init('LocalName');
        $this->CircleMember = ClassRegistry::init('CircleMember');
        $this->TeamMember = ClassRegistry::init('TeamMember');
        $this->TeamMember = ClassRegistry::init('TeamMember');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->User);
        parent::tearDown();
    }

    function test_replaceMentionToSimpleReadable()
    {
        $res = MentionComponent::replaceMentionToSimpleReadable('');
        $this->assertSame($res, '');

        $res = MentionComponent::replaceMentionToSimpleReadable('%%%user_1:山田 太郎%%% いいね！');
        $this->assertEquals($res, ' いいね！');

        $res = MentionComponent::replaceMentionToSimpleReadable('%%%user_1:山田 太郎%%% いいね！');
        $this->assertEquals($res, ' いいね！');

        $res = MentionComponent::replaceMentionToSimpleReadable('%%%circle_104:テストサークル%%% %%%user_1:山田 太郎%%% いいね！');
        $this->assertEquals($res, '  いいね！');
    }

    function test_getTargetIdsEachType()
    {
        // No mention
        $res = $this->MentionComponent->getTargetIdsEachType('', 1);
        $def = ['user' => [], 'circle' => []];
        $this->assertEquals($res, $def);

        $res = $this->MentionComponent->getTargetIdsEachType('あああ', 1);
        $def = ['user' => [], 'circle' => []];
        $this->assertEquals($res, $def);


        /* Only user mention */
        // Single
        $res = $this->MentionComponent->getTargetIdsEachType('%%%user_12%%% ああああ', 1);
        $this->assertEquals($res, ['circle' => [], 'user' => [12]]);

        // Multiple
        $res = $this->MentionComponent->getTargetIdsEachType('%%%user_12%%%  ああああ %%%user_1%%%', 1);
        $this->assertEquals($res, ['circle' => [], 'user' => [1, 12]]);

        // Include missing user
        $res = $this->MentionComponent->getTargetIdsEachType('%%%user_12%%%  ああああ %%%user_9999999%%%', 1);
        $this->assertEquals($res, ['circle' => [], 'user' => [12]]);


        /* Only circle mention */
        // Single
        $res = $this->MentionComponent->getTargetIdsEachType('%%%circle_2%%% ああああ', 1);
        $this->assertEquals($res, ['circle' => [2], 'user' => []]);

        // Multiple
        $res = $this->MentionComponent->getTargetIdsEachType('テスト %%%circle_3%%%  ああああ %%%circle_2%%%', 1);
        $this->assertEquals($res, ['circle' => [2, 3], 'user' => []]);

        // Include missing circle
        $res = $this->MentionComponent->getTargetIdsEachType('テスト %%%circle_3%%%  ああああ %%%circle_2%%% %%%circle_9999999%%%', 1);
        $this->assertEquals($res, ['circle' => [2, 3], 'user' => []]);

        /* Mix circle and user mentions */
        $res = $this->MentionComponent->getTargetIdsEachType('%%%user_12%%%  ああああ %%%user_9999999%%%テスト %%%circle_3%%%  ああああ %%%circle_2%%% %%%circle_9999999%%%', 1);
        $this->assertEquals($res, ['circle' => [2, 3], 'user' => [12]]);

    }
}
