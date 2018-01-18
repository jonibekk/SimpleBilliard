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
}
