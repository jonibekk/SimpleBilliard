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

        // TODO: 現状では動画1つだけなので, そうでなくなった場合を考慮して修正が必要
        $resource = reset($resources);

        $PostResource->create([
            'post_id' => null,
            'post_draft_id' => $postDraft['id'],
            'resource_type' => Enum\Post\PostResourceType::VIDEO_STREAM()->getValue(),
            'resource_id' => $resource['id'],
        ]);
        $postResource = $PostResource->save();

        return $postDraft;
    }
}
