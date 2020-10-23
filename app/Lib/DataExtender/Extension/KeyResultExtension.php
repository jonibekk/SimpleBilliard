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
                    ]
                ]
            ],
            'fields' => [
                'KeyResult.*',
                'Watchlist.id'
            ]
        ];

        $fetchedData = $KeyResult->useType()->find('all', $conditions);

        $processedData = array_map(function($row) {
            $watched = !empty($row['Watchlist']["id"]);
            $row['KeyResult']['is_watched'] = $watched;
            return $row;
        }, $fetchedData);

        return $processedData;
    }
}
