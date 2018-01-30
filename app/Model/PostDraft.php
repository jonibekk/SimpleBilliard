<?php
App::uses('AppModel', 'Model');
App::uses('PostResource', 'Model');

use Goalous\Model\Enum as Enum;

/**
 * Class PostDraft
 */
class PostDraft extends AppModel
{
    function getByUserIdAndTeamId(int $userId, int $teamId): array
    {
        $options = [
            'fields'     => [
                '*'
            ],
            'conditions' => [
                'user_id' => $userId,
                'team_id' => $teamId,
            ],
            'order' => [
                'id' => 'desc',
            ]
        ];
        $postDrafts = $this->find('all', $options);
        if (is_null($postDrafts)) {
            GoalousLog::error('finding post draft in error');
            return [];
        }
        $postDrafts = Hash::extract($postDrafts, '{n}.PostDraft');

        /** @var PostResource $PostResource */
        $PostResource = ClassRegistry::init('PostResource');

        foreach ($postDrafts as $i => $postDraft) {
            $postDraft['data'] = json_decode($postDraft['draft_data'], true);
            $postDraft['post_resources'] = $PostResource->getResourcesByPostDraftId($postDraft['id']);
            $postDrafts[$i] = $postDraft;
        }
        return $postDrafts;
    }

    private function getQueryByResourceTypeAndResourceId(Enum\Post\PostResourceType $postResourceType, int $resourceId): array
    {
        return [
            'joins' => [
                [
                    'type'       => 'INNER',
                    'table'      => 'post_resources',
                    'alias'      => 'PostResource',
                    'conditions' => [
                        'PostResource.post_draft_id = PostDraft.id',
                    ]
                ]
            ],
            'fields'     => [
                'PostDraft.id',
                'PostDraft.user_id',
                'PostDraft.team_id',
                'PostDraft.post_id',
                'PostDraft.draft_data',
            ],
            'conditions' => [
                'PostResource.resource_type' => $postResourceType->getValue(),
                'PostResource.resource_id'   => $resourceId,
                'PostResource.del_flg'       => false,
            ],
        ];
    }

    /**
     * @param Enum\Post\PostResourceType $postResourceType
     * @param int                        $resourceId
     *
     * @return array
     */
    function getByResourceTypeAndResourceId(Enum\Post\PostResourceType $postResourceType, int $resourceId): array
    {
        $result = $this->find('all', $this->getQueryByResourceTypeAndResourceId($postResourceType, $resourceId));
        return Hash::extract($result, '{n}.PostDraft');
    }

    /**
     * @param Enum\Post\PostResourceType $postResourceType
     * @param int                        $resourceId
     *
     * @return array
     */
    function getFirstByResourceTypeAndResourceId(Enum\Post\PostResourceType $postResourceType, int $resourceId): array
    {
        $result = $this->find('first', $this->getQueryByResourceTypeAndResourceId($postResourceType, $resourceId));
        if (empty($result)) {
            return [];
        }
        return reset($result);
    }
}
