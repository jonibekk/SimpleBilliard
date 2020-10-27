<?php
App::uses("KeyResult", "Model");
App::import('Lib/DataExtender/Extension', 'DataExtension');

class KeyResultExtension extends DataExtension
{
    /** @var int */
    private $userId;

    /** @var int */
    private $teamId;

    /**
     * Set user ID for the extender function
     *
     * @param int $userId
     */
    public function setUserId(int $userId)
    {
        $this->userId = $userId;
    }

    public function setTeamId(int $teamId)
    {
        $this->teamId = $teamId;
    }

    protected function fetchData(array $keys): array
    {
        /** @var KeyResult $KeyResult */
        $KeyResult = ClassRegistry::init("KeyResult");

        //Remove null values
        $uniqueKeys = $this->filterKeys($keys);

        $conditions = [
            'conditions' => [
                'KeyResult.id' => $uniqueKeys
            ],
        ];

        $fetchedData = $KeyResult->useType()->find('all', $conditions);
        $isWatchedResults = $this->appendIsWatched($uniqueKeys);

        return array_map(function($row) use ($isWatchedResults) {
            $krId = $row['KeyResult']['id'];
            $row['KeyResult']['is_watched'] = $isWatchedResults[$krId];
            return $row;
        }, $fetchedData);
    }

    private function appendIsWatched($krIds)
    {
        /** @var KeyResult $KeyResult */
        $KeyResult = ClassRegistry::init("KeyResult");

        $conditions = [
            'conditions' => [
                'KeyResult.id' => $krIds
            ],
            'joins' => [
                [
                    'alias' => 'KrWatchlist',
                    'table' => 'kr_watchlists',
                    'type' => 'LEFT',
                    'conditions' => [
                        'KrWatchlist.key_result_id = KeyResult.id',
                        'KrWatchlist.del_flg != 1'
                    ]
                ],
                [
                    'alias' => 'Watchlist',
                    'table' => 'watchlists',
                    'type' => 'LEFT',
                    'conditions' => [
                        'Watchlist.id = KrWatchlist.watchlist_id',
                        'Watchlist.user_id' => $this->userId,
                        'Watchlist.team_id' => $this->teamId,
                        'Watchlist.del_flg != 1'
                    ]
                ]
            ],
            'fields' => [
                'KeyResult.id',
                'Watchlist.id'
            ]
        ];

        $results = $KeyResult->find('all', $conditions);

        return array_reduce($results, function ($acc, $row) {
            $acc[$row['KeyResult']['id']] = !empty($row['Watchlist']['id']);
            return $acc;
        }, []);
    }
}
