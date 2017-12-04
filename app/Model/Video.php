<?php
App::uses('AppModel', 'Model');

/**
 * Class Video
 */
class Video extends AppModel
{
    public function getByUserIdAndTeamIdAndHash(int $userId, int $teamId, string $hash): array
    {
        $options = [
            'conditions' => [
                'user_id' => $userId,
                'team_id' => $teamId,
                'hash'    => $hash,
                'del_flg' => 0,
            ],
        ];

        $result = $this->find('first', $options);
        if (empty($result)) {
            return [];
        }
        return reset($result);
    }
}
