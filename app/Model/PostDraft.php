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
        ];
        $postDrafts = Hash::extract($this->find('all', $options), '{n}.PostDraft');

        /** @var PostResource $PostResource */
        $PostResource = ClassRegistry::init('PostResource');

        foreach ($postDrafts as $i => $postDraft) {
            $postDraft['data'] = json_decode($postDraft['draft_data'], true);
            $postDraft['post_resources'] = $PostResource->getResourcesByPostDraftId($postDraft['id']);
            $postDrafts[$i] = $postDraft;
        }
        return $postDrafts;
    }
}
