<?php

use Mockery as mock;

App::uses('GoalousTestCase', 'Test');
App::uses('ActionResultMember', 'Model');
App::import('Lib/Storage', 'UploadedFile');
App::import('Service', 'PostService');

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
        'app.term',
        'app.team',
        'app.attached_file',
        'app.term',
        'app.goal',
        'app.key_result',
        'app.action_result',
        'app.action_result_file',
        'app.kr_progress_log',
        'app.post',
        'app.goal_member',
        'app.team_member',
        'app.goal_label',
        'app.goal_category',
        'app.label',
        'app.user',
        'app.circle',
        'app.team_translation_language',
        'app.post_draft',
        'app.post_like',
        'app.post_read',
        'app.post_mention',
        'app.post_share_circle',
        'app.post_share_user',
        'app.translation',
        'app.post_resource',
        'app.cache_unread_circle_post',
        'app.kr_values_daily_log',
        'app.comment',
        'app.goal_group',
        'app.follower'
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

    function test_deleteAllByActionResultId()
    {
        /** @var ActionResult $ActionResult */
        $ActionResult = ClassRegistry::init('ActionResult');
        /** @var ActionResultMember $ActionResultMember */
        $ActionResultMember = ClassRegistry::init('ActionResultMember');

        $ActionResultMember->addMember($actionResultId = 1, 1, 1, true);
        $ActionResultMember->addMember($actionResultId = 2, 1, 1, true);

        $ActionResultMember->deleteAllByActionResultId(1);

        $this->assertCount(0, $ActionResultMember->getActionResultMembersByActionResultId(1));
        $this->assertCount(1, $ActionResultMember->getActionResultMembersByActionResultId(2));
    }

    private function createGoalKeyResultAction()
    {

        /** @var KeyResult $KeyResult */
        $KeyResult = ClassRegistry::init("KeyResult");
        /** @var Post $Post */
        $Post = ClassRegistry::init("Post");
        /** @var ActionResultMember $ActionResultMember */
        $ActionResultMember = ClassRegistry::init('ActionResultMember');
        /** @var ActionService $ActionService */
        $ActionService = ClassRegistry::init("ActionService");
        /** @var PostService $PostService */
        $PostService = ClassRegistry::init('PostService');

        $userId = 1;
        $teamId = 1;

        $returnValue = "5beb86de5430f7.89168598";
        $bufferClient = mock::mock('BufferStorageClient');
        $bufferClient->shouldReceive('save')
            ->once()
            ->andReturn(true);
        $bufferClient->shouldReceive('get')
            ->once()
            ->andReturn(new UploadedFile(base64_encode('1'), '1'));
        ClassRegistry::addObject(BufferStorageClient::class, $bufferClient);

        $this->setupTerm();
        $goalId = $this->createGoal($userId);
        $KeyResult->my_uid = $userId;
        $KeyResult->current_team_id = $teamId;
        $keyResult = Hash::get($KeyResult->getTkr($goalId), 'KeyResult');
        $fileIds = $this->prepareUploadImages();
        $data = [
            "goal_id"                  => $goalId,
            "team_id"                  => $teamId,
            "user_id"                  => $userId,
            "name"                     => "ああああ\nいいいいいいい",
            "key_result_id"            => $keyResult['id'],
            "key_result_current_value" => $keyResult['current_value'],
            'file_ids' => [$returnValue]
        ];

        $assetsClient = mock::mock('AssetsStorageClient');

        $assetsClient->shouldReceive('save')
            ->once()
            ->andReturn(true);
        $assetsClient->shouldReceive('delete')
            ->once()
            ->andReturn(true);
        $assetsClient->shouldReceive('bulkSave')
            ->once()
            ->andReturn(true);

        ClassRegistry::addObject(AssetsStorageClient::class, $assetsClient);

        $actionResultId = $ActionService->createAngular($data);

        /** @var ActionResult $ActionResult */
        $ActionResult = ClassRegistry::init('ActionResult');
        $this->assertCount(1, $ActionResultMember->getActionResultMembersByActionResultId($actionResultId));

        $post = $Post->find('first', [
            'conditions' => [
                'action_result_id' => $actionResultId
            ]
        ])['Post'];

        return [
            $actionResultId,
            $keyResult,
            $post,
        ];
    }

    function test_association_delete_post()
    {
        /** @var PostService $PostService */
        $PostService = ClassRegistry::init('PostService');
        /** @var ActionResultMember $ActionResultMember */
        $ActionResultMember = ClassRegistry::init('ActionResultMember');
        /** @var ActionResult $ActionResult */
        $ActionResult = ClassRegistry::init('ActionResult');

        list($actionResultId, $keyResult, $post) = $this->createGoalKeyResultAction();

        $this->assertCount(1, $ActionResultMember->getActionResultMembersByActionResultId($actionResultId));

        $PostService->softDelete($post['id']);

        $this->assertEmpty($ActionResult->getById($actionResultId));
        $this->assertCount(0, $ActionResultMember->getActionResultMembersByActionResultId($actionResultId));
    }

    function test_association_delete_kr()
    {
        /** @var KeyResultService $KeyResultService */
        $KeyResultService = ClassRegistry::init('KeyResultService');
        /** @var ActionResultMember $ActionResultMember */
        $ActionResultMember = ClassRegistry::init('ActionResultMember');
        /** @var ActionResult $ActionResult */
        $ActionResult = ClassRegistry::init('ActionResult');

        list($actionResultId, $keyResult, $post) = $this->createGoalKeyResultAction();

        $KeyResultService->delete($keyResult['id']);

        $this->assertNotEmpty($ActionResult->getById($actionResultId));
        $this->assertCount(1, $ActionResultMember->getActionResultMembersByActionResultId($actionResultId));
    }

    function test_association_delete_goal()
    {
        /** @var KeyResultService $KeyResultService */
        $KeyResultService = ClassRegistry::init('KeyResultService');
        /** @var PostService $PostService */
        $PostService = ClassRegistry::init('PostService');
        /** @var ActionResultMember $ActionResultMember */
        $ActionResultMember = ClassRegistry::init('ActionResultMember');
        /** @var ActionResult $ActionResult */
        $ActionResult = ClassRegistry::init('ActionResult');
        /** @var GoalService $GoalService */
        $GoalService = ClassRegistry::init('GoalService');
        /** @var Goal $Goal */
        $Goal = ClassRegistry::init('Goal');

        list($actionResultId, $keyResult, $post) = $this->createGoalKeyResultAction();

        $GoalService->delete($keyResult['goal_id']);

        $this->assertFalse($Goal->getById($keyResult['goal_id']));
        $this->assertFalse($ActionResult->getById($actionResultId));
        $this->assertCount(0, $ActionResultMember->getActionResultMembersByActionResultId($actionResultId));
    }
}
