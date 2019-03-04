<?php
App::uses('GoalousTestCase', 'Test');
App::import('Service', 'PostService');
App::import('Service', 'UploadService');
App::import('Service', 'CircleService');
App::uses('PostFile', 'Model');
App::uses('AttachedFile', 'Model');
App::uses('PostShareCircle', 'Model');
App::uses('PostShareUser', 'Model');
App::uses('Post', 'Model');
App::uses('CircleMember', 'Model');
App::uses('Circle', 'Model');
App::uses('PostResource', 'Model');
App::uses('PostDraft', 'Model');
App::import('Model/Entity', 'PostEntity');
App::import('Service/Request/Resource', 'PostResourceRequest');

use Goalous\Enum as Enum;

/**
 * Created by PhpStorm.
 * User: stephen
 * Date: 19/02/27
 * Time: 10:24
 */
class PostResourceServiceTest extends GoalousTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.post',
        'app.team',
        'app.user',
        'app.post_file',
        'app.attached_file',
        'app.circle',
        'app.post_share_circle',
        'app.post_share_user',
        'app.circle_member',
        'app.circle',
        'app.video',
        'app.video_stream',
        'app.post_resource',
        'app.post_draft',
        'app.post_like',
        'app.post_mention',
        'app.post_read',
        'app.post_shared_log',
        'app.comment',
        'app.comment_like',
        'app.comment_read',
        'app.saved_post',
        'app.action_result',
        'app.action_result_file',
        'app.key_result',
        'app.goal',
        'app.post_file',
        'app.comment_file'
    ];

    public function test_deleteResourceVideoStreamUnique_success()
    {
        $circleId = 1;
        $userId = 1;
        $teamId = 1;

        list($newPostId, $newFiles, $newVideos) = $this->createNewCirclePost($circleId, $userId, $teamId, 1, 1);
        /** @var PostResource $PostResource */
        $PostResource = ClassRegistry::init('PostResource');
        /** @var PostResourceService $PostResourceService */
        $PostResourceService = ClassRegistry::init('PostResourceService');
        /** @var VideoStream $VideoStream */
        $VideoStream = ClassRegistry::init('VideoStream');

        $videoStreamId = $newVideos[0]['id'];

        $ids = $PostResource->getPostResourceId($newVideos[0]['id'], Enum\Model\Post\PostResourceType::VIDEO_STREAM());

        $this->assertCount(1, $ids);

        $PostResourceService->deleteResources($ids);

        $this->assertEmpty($PostResource->getEntity($ids[0])->toArray());

        $this->assertEmpty($VideoStream->getEntity($videoStreamId)->toArray());
    }

    public function test_deleteResourceVideoStreamNotUnique_success()
    {
        $circleId = 1;
        $userId = 1;
        $teamId = 1;

        list($newPostId, $newFiles, $newVideos) = $this->createNewCirclePost($circleId, $userId, $teamId, 1, 1);

        /** @var PostResource $PostResource */
        $PostResource = ClassRegistry::init('PostResource');
        /** @var PostResourceService $PostResourceService */
        $PostResourceService = ClassRegistry::init('PostResourceService');
        /** @var VideoStream $VideoStream */
        $VideoStream = ClassRegistry::init('VideoStream');

        $videoStreamId = $newVideos[0]['id'];

        $PostResource->create();
        $PostResource->save([
            'post_id'       => 1,
            'resource_id'   => $videoStreamId,
            'resource_type' => Enum\Model\Post\PostResourceType::VIDEO_STREAM
        ], false);
        $ids = $PostResource->getPostResourceId($videoStreamId, Enum\Model\Post\PostResourceType::VIDEO_STREAM());

        $this->assertCount(2, $ids);

        $PostResourceService->deleteResources([$ids[0]]);

        $this->assertEmpty($PostResource->getEntity($ids[0])->toArray());
        $this->assertNotEmpty($PostResource->getEntity($ids[1])->toArray());

        $this->assertNotEmpty($VideoStream->getEntity($videoStreamId)->toArray());
    }

}