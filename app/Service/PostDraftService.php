<?php
App::import('Service', 'AppService');
App::uses('PostDraft', 'Model');
App::uses('PostResource', 'Model');

use Goalous\Model\Enum as Enum;

/**
 * Class PostDraftService
 */
class PostDraftService extends AppService
{
    /**
     * creating new post draft with resources
     *
     * @param array $postData
     * @param array $user
     * @param int $teamId
     * @param array $resources
     *
     * @return array|null
     */
    public function createPostDraftWithResources(array $postData, array $user, int $teamId, array $resources): array
    {
        /** @var PostDraft $PostDraft */
        $PostDraft = ClassRegistry::init('PostDraft');
        /** @var PostResource $PostResource */
        $PostResource = ClassRegistry::init("PostResource");

        $PostDraft->create([
            'user_id' => $user['id'],
            'team_id' => $teamId,
            'post_id' => null,
            'draft_data' => json_encode($postData),
        ]);
        $postDraft = $PostDraft->save();
        $postDraft = reset($postDraft);

        foreach ($resources as $resource) {
            $PostResource->create([
                'post_id' => null,
                'post_draft_id' => $postDraft['id'],
                // TODO: currently only resource type of video only
                // need to determine what type of resource is passed from arguments
                // (maybe should wrap by class, not simple array)
                // same as in Post::addNormal()
                'resource_type' => Enum\Post\PostResourceType::VIDEO_STREAM()->getValue(),
                'resource_id' => $resource['id'],
            ]);
            $postResource = $PostResource->save();
        }

        return $postDraft;
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
            $transcodeStatus = new Enum\Video\VideoTranscodeStatus(intval($resource['transcode_status']));
            if ($transcodeStatus->equals(Enum\Video\VideoTranscodeStatus::TRANSCODE_COMPLETE())) {
                continue;
            }
            return false;
        }

        return true;
    }
}
