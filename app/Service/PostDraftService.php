<?php
App::import('Service', 'AppService');
App::import('Service', 'PostResourceService');
App::uses('PostDraft', 'Model');
App::uses('PostResource', 'Model');
App::uses('Post', 'Model');

use Goalous\Enum as Enum;

/**
 * Class PostDraftService
 */
class PostDraftService extends AppService
{
    /**
     * Return PostDrafts data to show for top / circle feeds
     *
     * @param int   $userId
     * @param int   $teamId
     * @param int[] $limitByCircleIds
     *      Passing circles.id by int array to filter by its id
     *      If $limitByCircleIds is empty, returning all draft post of users.id + teams.id
     *
     * @return array
     */
    public function getPostDraftForFeed(int $userId, int $teamId, array $limitByCircleIds = []): array
    {
        /** @var PostDraft $PostDraft */
        $PostDraft = ClassRegistry::init("PostDraft");
        /** @var Post $Post */
        $Post = ClassRegistry::init("Post");

        $postDrafts = $PostDraft->getByUserIdAndTeamId($userId, $teamId);

        // get share circles/peoples
        foreach ($postDrafts as $key => $postDraft) {
            if (!isset($postDraft['data']['Post']['share'])
                || !is_string($postDraft['data']['Post']['share'])) {
                continue;
            }
            $shares = explode(',', $postDraft['data']['Post']['share']);
            list($userIds, $circleIds) = $Post->distributeShareToUserAndCircle($shares, $teamId);

            // Filter the targets circles to post
            // If $limitByCircleIds is empty, no filtering
            if (!empty($limitByCircleIds)) {
                // Comparing (array of circles.id) x 2
                // if having same circles.id in both array, the post_draft is to shown
                if (0 === count(array_intersect($circleIds, $limitByCircleIds))) {
                    unset($postDrafts[$key]);
                    continue;
                }
            }
            $postDraft['PostShareUser'] = [];
            $postDraft['PostShareCircle'] = [];
            foreach ($userIds as $userId) {
                $postDraft['PostShareUser'][] = [
                    'user_id' => $userId,
                ];
            }
            foreach ($circleIds as $circleId) {
                $postDraft['PostShareCircle'][] = [
                    'circle_id' => $circleId,
                ];
            }

            // has transcode failed video
            $hasTranscodeFailed = false;
            foreach ($postDraft['post_resources'] as $resource) {
                $transcodeStatus = new Goalous\Enum\Model\Video\VideoTranscodeStatus(intval($resource['transcode_status']));
                if ($transcodeStatus->equals(Enum\Model\Video\VideoTranscodeStatus::ERROR())) {
                    $hasTranscodeFailed = true;
                    break;
                }
            }
            $postDraft['hasTranscodeFailed'] = $hasTranscodeFailed;

            $postDrafts[$key] = $postDraft;
        }
        //１件のサークル名をランダムで取得
        $postDrafts = $Post->getRandomShareCircleNames($postDrafts);
        //１件のユーザ名をランダムで取得
        $postDrafts = $Post->getRandomShareUserNames($postDrafts);
        //シェアモードの特定
        $postDrafts = $Post->getShareMode($postDrafts);
        //シェアメッセージの特定
        $postDrafts = $Post->getShareMessages($postDrafts, false);

        return $postDrafts;
    }

    /**
     * creating new post draft with resources
     *
     * @param array $postData
     * @param int $userId
     * @param int $teamId
     * @param array $resources
     *
     * @return array|false
     */
    public function createPostDraftWithResources(array $postData, int $userId, int $teamId, array $resources)
    {
        /** @var PostDraft $PostDraft */
        $PostDraft = ClassRegistry::init('PostDraft');
        /** @var PostResourceService $PostResourceService */
        $PostResourceService = ClassRegistry::init('PostResourceService');
        /** @var Post $Post */
        $Post = ClassRegistry::init("Post");

        $Post->set($postData['Post']);
        if (!$Post->validates()) {
            return false;
        }

        try {
            $this->TransactionManager->begin();

            $PostDraft->create([
                'user_id' => $userId,
                'team_id' => $teamId,
                'post_id' => null,
                'draft_data' => json_encode($postData),
            ]);
            $postDraft = $PostDraft->save();
            if (false === $postDraft) {
                GoalousLog::error('failed to save post_draft', [
                    'users.id' => $userId,
                    'teams.id' => $teamId,
                    'Post'     => $postData,
                ]);
                throw new RuntimeException('failed to save post_draft');
            }
            $postDraft = reset($postDraft);
            foreach ($resources as $resource) {
                $PostResourceService->addResourceDraft($postDraft['id'],
                    Enum\Model\Post\PostResourceType::VIDEO_STREAM(),
                    $resource['id'],
                    $order = 0
                );
                if (false ===$postDraft) {
                    GoalousLog::error('failed to save post resource', [
                        'users.id' => $userId,
                        'teams.id' => $teamId,
                        'Post'     => $postData,
                    ]);
                    throw new RuntimeException('failed to save post resource');
                }
            }
            $this->TransactionManager->commit();
            return $postDraft;
        } catch (Exception $e) {
            $this->TransactionManager->rollback();
            return false;
        }

    }

    /**
     * Return true if target post_drafts.id is prepared to post
     *
     * @param int $postDraftId
     *
     * @return bool
     */
    public function isPreparedToPost(int $postDraftId): bool
    {
        /** @var PostDraft $PostDraft */
        $PostDraft = ClassRegistry::init('PostDraft');
        /** @var PostResource $PostResource */
        $PostResource = ClassRegistry::init("PostResource");

        $postDraft = $PostDraft->getById($postDraftId);
        if (empty($postDraft)) {
            GoalousLog::info('post_draft is checked to prepare for post, but record not found or deleted', [
                'post_drafts.id' => $postDraftId,
            ]);
            return false;
        }

        // deciding draft post is prepared to post not depends on TeamStatus (set $checkTeamStatus = false)
        $resources = $PostResource->getResourcesByPostDraftId([$postDraftId], $checkTeamStatus = false);
        if (!isset($resources[$postDraftId])) {
            // if no resource is found
            return true;
        }
        foreach ($resources[$postDraftId] as $resource) {
            // TODO: currently we have only type of video resource
            // should be wrapped by kind of resource class
            $transcodeStatus = new Enum\Model\Video\VideoTranscodeStatus(intval($resource['transcode_status']));
            if ($transcodeStatus->equals(Enum\Model\Video\VideoTranscodeStatus::TRANSCODE_COMPLETE())) {
                continue;
            }
            return false;
        }

        return true;
    }
}
