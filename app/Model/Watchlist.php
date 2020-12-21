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
        $options = [
            'conditions' => [
                'Watchlist.user_id' => $userId,
                'Watchlist.team_id' => $teamId,
                'Watchlist.del_flg' => false
            ]
        ];

        $fullOptions = array_merge_recursive($options, $scope);
        return $this->find("first", $fullOptions);
    }

    function findWithKrCount($scope) {
        $options = [
            'joins' => [
                [
                    'table' => 'kr_watchlists',
                    'type' => 'LEFT',
                    'conditions' => [
                        'kr_watchlists.watchlist_id = Watchlist.id',
                        'kr_watchlists.del_flg != 1',
                    ],
                ],
                [
                    'table' => 'key_results',
                    'type' => 'LEFT',
                    'conditions' => [
                        'kr_watchlists.key_result_id = key_results.id',
                        'key_results.del_flg != 1',
                    ],
                ],
            ],
            'fields' => [
                'Watchlist.*',
                'COALESCE(COUNT(key_results.id), 0) AS kr_count'
            ],
            'group' => [
                'Watchlist.id'
            ],
            
        ];

        $fullOptions = array_merge_recursive($options, $scope);
        $results = $this->find("all", $fullOptions);

        return array_map(
            function ($row) {
                $row['Watchlist']['kr_count'] = (int) $row['0']['kr_count'];
                return $row;
            },
            $results
        );
    }
}
