<?php
App::uses('AppModel', 'Model');

/**
 * Watchlist Model
 *
 * @property KrWatchlist       $KrWatchlist
 */
class Watchlist extends AppModel
{
    public $validate = [
        'del_flg'   => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
    ];

    public $hasMany = [
        'KrWatchlist',
    ];

    function findByUserAndTeam(int $userId, int $teamId, array $scope): array
    {
        $results = $this->find(
            'all',
            [
                'conditions' => [
                    'Watchlist.user_id' => $userId,
                    'Watchlist.team_id' => $teamId,
                    'Watchlist.del_flg' => false
                ]
            ]
        );

        return array_merge_recursive($results, $scope);
    }
}
