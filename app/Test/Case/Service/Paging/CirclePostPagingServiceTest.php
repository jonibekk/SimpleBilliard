<?php

App::uses('GoalousTestCase', 'Test');
App::import('Service/Paging', 'CirclePostPagingService');
App::uses('PagingRequest', 'Lib/Paging');
App::uses('Post', 'Model');
App::uses('TeamTranslationStatus', 'Model');
App::import('Service', 'TeamMemberService');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/06/20
 * Time: 11:24
 */
class CirclePostPagingServiceTest extends GoalousTestCase
{
    public $fixtures = [
        'app.team',
        'app.team_member',
        'app.user',
        'app.circle',
        'app.circle_member',
        'app.circle_pin',
        'app.post',
        'app.post_share_circle',
        'app.post_share_user',
        'app.comment',
        'app.local_name',
        'app.experiment',
        'app.post_like',
        'app.saved_post',
        'app.comment_like',
        'app.attached_file',
        'app.post_file',
        'app.comment_file',
        'app.team_translation_status',
        'app.team_translation_language',
        'app.mst_translation_language',
        'app.translation',
        'app.post_resource',
    ];

    public function test_getCirclePost_success()
    {
        /** @var CirclePostPagingService $CirclePostPagingService */
        $CirclePostPagingService = new CirclePostPagingService();

        $cursor = new PagingRequest();
        $cursor->addResource('res_id', 3);
        $cursor->addResource('current_team_id', 1);
        $cursor->setCurrentUserId(1);

        $result = $CirclePostPagingService->getDataWithPaging($cursor, 1);

        $this->assertCount(1, $result['data']);
        $this->assertNotEmpty($result['cursor']);
        $this->assertNotEmpty($result['count']);
    }

    public function test_getCirclePostWithCursor_success()
    {
        /** @var CirclePostPagingService $CircleFeedPaging */
        $CircleFeedPaging = new CirclePostPagingService();

        $cursor = new PagingRequest();
        $cursor->addResource('res_id', 3);
        $cursor->addResource('current_team_id', 1);
        $cursor->setCurrentUserId(1);

        $result = $CircleFeedPaging->getDataWithPaging($cursor, 1);
        $this->assertNotEmpty($result['cursor']);
        $this->assertNotEmpty($result['count']);

        $pagingRequest = PagingRequest::decodeCursorToObject($result['cursor']);
        $pagingRequest->addResource('res_id', 1);
        $pagingRequest->addResource('current_team_id', 1);
        $pagingRequest->setCurrentUserId(1);

        $secondResult = $CircleFeedPaging->getDataWithPaging($pagingRequest, 2);

        $this->assertCount(1, $secondResult['data']);
        $this->assertEmpty($secondResult['cursor']);
        $this->assertNotEmpty($secondResult['count']);
    }

    public function test_getCirclePostWithUserExtension_success()
    {
        /** @var CirclePostPagingService $CirclePostPagingService */
        $CirclePostPagingService = new CirclePostPagingService();
        $cursor = new PagingRequest();
        $cursor->addResource('res_id', 1);
        $cursor->addResource('current_user_id', 1);
        $cursor->addResource('current_team_id', 1);
        $result = $CirclePostPagingService->getDataWithPaging($cursor, 1, CirclePostExtender::EXTEND_USER);

        $this->assertCount(1, $result['data']);

        $postData = $result['data'][0];

        $this->assertNotEmpty($postData['user']);
    }

    public function test_getCirclePostWithCircleExtension_success()
    {
        /** @var CirclePostPagingService $CirclePostPagingService */
        $CirclePostPagingService = new CirclePostPagingService();
        $cursor = new PagingRequest();
        $cursor->addResource('res_id', 3);
        $cursor->addResource('current_user_id', 1);
        $cursor->addResource('current_team_id', 1);
        $result = $CirclePostPagingService->getDataWithPaging($cursor, 10, CirclePostExtender::EXTEND_CIRCLE);

        $this->assertCount(2, $result['data']);

        //Loop since not all post has circle_id
        foreach ($result['data'] as $post) {
            if (!empty($post['circle_id'])) {
                $this->assertNotEmpty($post['circle']);
            }
        }
    }

    public function test_getCirclePostWithCommentsExtension_success()
    {
        /** @var CirclePostPagingService $CirclePostPagingService */
        $CirclePostPagingService = new CirclePostPagingService();
        $cursor = new PagingRequest();
        $cursor->addResource('res_id', 1);
        $cursor->addResource('current_user_id', 1);
        $cursor->addResource('current_team_id', 1);
        $result = $CirclePostPagingService->getDataWithPaging($cursor, 1, CirclePostExtender::EXTEND_COMMENTS);

        $this->assertCount(1, $result['data']);

        $postData = $result['data'][0];

        $this->assertNotEmpty($postData['comments']);
    }

    public function test_getCirclePostWithPostLikeExtension_success()
    {
        /** @var CirclePostPagingService $CirclePostPagingService */
        $CirclePostPagingService = new CirclePostPagingService();
        $cursor = new PagingRequest();
        $cursor->addResource('res_id', 1);
        $cursor->addResource('current_user_id', 1);
        $cursor->addResource('current_team_id', 1);
        $result = $CirclePostPagingService->getDataWithPaging($cursor, 1, CirclePostExtender::EXTEND_LIKE);

        $this->assertCount(1, $result['data']);

        $postData = $result['data'][0];
        $this->assertInternalType('bool', $postData['is_liked']);
    }

    public function test_getCirclePostWithPostSavedExtension_success()
    {
        /** @var CirclePostPagingService $CirclePostPagingService */
        $CirclePostPagingService = new CirclePostPagingService();
        $cursor = new PagingRequest();
        $cursor->addResource('res_id', 1);
        $cursor->addResource('current_user_id', 1);
        $cursor->addResource('current_team_id', 1);
        $result = $CirclePostPagingService->getDataWithPaging($cursor, 1, CirclePostExtender::EXTEND_SAVED);

        $this->assertCount(1, $result['data']);

        $postData = $result['data'][0];
        $this->assertInternalType('bool', $postData['is_saved']);
    }

    public function test_getCirclePostWithPostFileExtension_success()
    {
        /** @var CirclePostPagingService $CirclePostPagingService */
        $CirclePostPagingService = new CirclePostPagingService();
        $cursor = new PagingRequest();
        $cursor->addResource('res_id', 1);
        $cursor->addResource('current_user_id', 1);
        $cursor->addResource('current_team_id', 1);
        $result = $CirclePostPagingService->getDataWithPaging($cursor, 1, CirclePostExtender::EXTEND_RESOURCES);

        $this->assertCount(1, $result['data']);

        $postData = $result['data'][0];
        $this->assertArrayHasKey('resources', $postData);
    }

    public function test_getCirclePostWithTranslationLanguage_success()
    {
        /** @var TeamTranslationStatus $TeamTranslationStatus */
        $TeamTranslationStatus = ClassRegistry::init('TeamTranslationStatus');

        $teamId = 1;
        $userId = 1;

        $TeamTranslationStatus->createEntry($teamId);

        $this->insertTranslationLanguage($teamId, "en");
        $this->insertTranslationLanguage($teamId, "ja");
        $this->insertTranslationLanguage($teamId, "de");

        /** @var CirclePostPagingService $CirclePostPagingService */
        $CirclePostPagingService = new CirclePostPagingService();
        $cursor = new PagingRequest();
        $cursor->addResource('res_id', 1);
        $cursor->addResource('current_user_id', $userId);
        $cursor->addResource('current_team_id', $teamId);
        $result = $CirclePostPagingService->getDataWithPaging($cursor, 1, [CirclePostExtender::EXTEND_TRANSLATION_LANGUAGE]);

        $postData = $result['data'][0];

        $this->assertArrayHasKey('translation_limit_reached', $postData);
        $this->assertFalse($postData['translation_limit_reached']);
        $this->assertArrayHasKey('translation_languages', $postData);
        $this->assertCount(3, $postData['translation_languages']);
        foreach ($postData['translation_languages'] as $translationLanguage) {
            $this->assertNotEmpty($translationLanguage['language']);
            $this->assertNotEmpty($translationLanguage['intl_name']);
            $this->assertNotEmpty($translationLanguage['local_name']);
        }

        // Post's language is not included
        /** @var Post $Post */
        $Post = ClassRegistry::init('Post');
        $Post->updateLanguage(1, "ja");
        $result = $CirclePostPagingService->getDataWithPaging($cursor, 1, [CirclePostExtender::EXTEND_TRANSLATION_LANGUAGE]);

        $postData = $result['data'][0];

        $this->assertFalse($postData['translation_limit_reached']);
        $this->assertCount(2, $postData['translation_languages']);
        foreach ($postData['translation_languages'] as $translationLanguage) {
            $this->assertNotEquals("ja", $translationLanguage['language']);
        }

        //If user has same language, don't include language
        /** @var TeamMemberService $TeamMemberService */
        $TeamMemberService = ClassRegistry::init('TeamMemberService');
        $TeamMemberService->setDefaultTranslationLanguage($teamId, $userId, "de");
        $Post->updateLanguage(1, "de");

        $result = $CirclePostPagingService->getDataWithPaging($cursor, 1, [CirclePostExtender::EXTEND_TRANSLATION_LANGUAGE]);
        $postData = $result['data'][0];

        $this->assertFalse($postData['translation_limit_reached']);
        $this->assertArrayHasKey('translation_languages', $postData);
        $this->assertEmpty($postData['translation_languages']);

        //If limit reached
        $TeamTranslationStatus->incrementActionCommentCount($teamId, 100000);
        $result = $CirclePostPagingService->getDataWithPaging($cursor, 1, [CirclePostExtender::EXTEND_TRANSLATION_LANGUAGE]);

        $this->assertCount(1, $result['data']);

        $postData = $result['data'][0];

        $this->assertTrue($postData['translation_limit_reached']);
        $this->assertArrayHasKey('translation_languages', $postData);
        $this->assertEmpty($postData['translation_languages']);
    }
}
