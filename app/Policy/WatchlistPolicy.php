<?php
App::uses('Watchlist', 'Model');
App::import('Policy', 'BasePolicy');

/**
 * Class WatchlistPolicy
 */
class WatchlistPolicy extends BasePolicy
{
    public function read($watchlist): bool
    {
        return $this->userId === (int) $watchlist['user_id'];
    }

    public function scope($type = 'read'): array
    {
        return [
            'conditions' => [
                'Watchlist.user_id' => $this->userId,
                'Watchlist.team_id' => $this->teamId,
            ]
        ];
    }
}
