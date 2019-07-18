<?php
App::uses('GoalousTestCase', 'Test');
App::import('Model/Entity', 'PostEntity');
App::import('Model/Entity', 'PostDraftEntity');
App::import('Service/Paging', 'PostDraftPagingService');
App::import('Lib/Paging', 'PagingRequest');
App::uses('Post', 'Model');
App::uses('PostDraft', 'Model');
App::uses('PostPostShareCircle', 'Model');


/**
 * Created by PhpStorm.
 * User: stephen
 * Date: 19/01/23
 * Time: 23:43
 */
class PostDraftPagingServiceTest extends GoalousTestCase
{
    public $fixtures = [
        'app.post',
        'app.user',
        'app.team',
        'app.post_draft',
        'app.post_share_circle'
    ];

    public function test_getFirstPage_success()
    {
        $circleId = 123;
        $userId = 12;
        $teamId = 92;

        $this->insertNewData($userId, $teamId, $circleId);
        $this->insertNewData($userId, $teamId, $circleId);
        $this->insertNewData($userId, $teamId, $circleId);

        /** @var PostDraftPagingService $PostDraftPagingService */
        $PostDraftPagingService = ClassRegistry::init('PostDraftPagingService');

        $cursor = new PagingRequest();
        $cursor->setResourceId($circleId);
        $cursor->setCurrentUserId($userId);
        $cursor->setCurrentTeamId($teamId);

        $result = $PostDraftPagingService->getDataWithPaging($cursor, 2);

        $this->assertCount(2, $result['data']);
        $this->assertEquals(3, $result['count']);
        $this->assertNotEmpty($result['cursor']);

        $singleData = $result['data'][0];
        $this->assertNotEmpty($singleData['body']);
    }

    public function test_getNextPage_success(){
        $circleId = 123;
        $userId = 12;
        $teamId = 92;

        $this->insertNewData($userId, $teamId, $circleId);
        $this->insertNewData($userId, $teamId, $circleId);
        $this->insertNewData($userId, $teamId, $circleId);

        /** @var PostDraftPagingService $PostDraftPagingService */
        $PostDraftPagingService = ClassRegistry::init('PostDraftPagingService');

        $cursor = new PagingRequest();
        $cursor->setResourceId($circleId);
        $cursor->setCurrentUserId($userId);
        $cursor->setCurrentTeamId($teamId);

        $result = $PostDraftPagingService->getDataWithPaging($cursor, 2);

        $cursor = PagingRequest::decodeCursorToObject($result['cursor']);
        $cursor->setResourceId($circleId);
        $cursor->setCurrentUserId($userId);
        $cursor->setCurrentTeamId($teamId);

        $result = $PostDraftPagingService->getDataWithPaging($cursor, 2);
        $this->assertCount(1, $result['data']);
        $this->assertEquals(3, $result['count']);
        $this->assertEMpty($result['cursor']);

    }

    private function insertNewData(int $userId, int $teamId, int $circleId): PostDraftEntity
    {
        //Insert post
        $newPost = [
            'user_id' => $userId,
            'team_id' => $teamId,
            'body'    => "User $userId team $teamId circle $circleId",
            'type'    => Post::TYPE_NORMAL
        ];
        /** @var Post $Post */
        $Post = ClassRegistry::init('Post');
        $Post->create();
        $post = $Post->save($newPost, false);
        $postId = $post['Post']['id'];

        //Insert post_share_circle
        $newPostShareCircle = [
            'post_id'    => $postId,
            'circle_id'   => $circleId,
            'team_id'    => $teamId,
            'share_type' => PostShareCircle::SHARE_TYPE_SHARED
        ];
        /** @var PostShareCircle $PostShareCircle */
        $PostShareCircle = ClassRegistry::init('PostShareCircle');
        $PostShareCircle->create();
        $PostShareCircle->save($newPostShareCircle, false);

        $draftData = [
            'Post' => [
                'body' => "Draft for post $postId user $userId team $teamId circle $circleId"
            ]
        ];
        //Insert post_draft
        $newPostDraft = [
            'user_id'    => $userId,
            'team_id'    => $teamId,
            'draft_data' => json_encode($draftData),
            'post_id'    => $postId
        ];
        /** @var PostDraft $PostDraft */
        $PostDraft = ClassRegistry::init('PostDraft');
        /** @var PostDraftEntity $postDraft */
        $PostDraft->create();
        $postDraft = $PostDraft->useType()->useEntity()->save($newPostDraft, false);

        return $postDraft;
    }
}