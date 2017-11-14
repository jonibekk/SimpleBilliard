<?php
App::import('Service', 'AppService');
App::uses('PostDraft', 'Model');

/**
 * Class PostDraftService
 */
class PostDraftService extends AppService
{
    /**
     * creating new post draft
     *
     * @param int $userId
     * @param int $teamId
     *
     * @return array|null
     */
    public function createPostDraft(int $userId, int $teamId)
    {
        /** @var PostDraft $PostDraft */
        $PostDraft = ClassRegistry::init("PostDraft");

        $PostDraft->create([
            'user_id' => $userId,
            'team_id' => $teamId,
            'post_id' => null,
            'draft_data' => json_encode([]),
        ]);
        $result = $PostDraft->save();
        return reset($result);
    }
}
