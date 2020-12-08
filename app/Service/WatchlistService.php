
<?php
App::import('Service', 'AppService');
App::import('Service', 'GoalService');
App::uses('Watchlist', 'Model');
App::uses('KrWatchlist', 'Model');
App::import('Model/Entity', 'WatchlistEntity');

use Goalous\Enum as Enum;

/**
 * Class WatchlistService
 */
class WatchlistService extends AppService
{

    public function add(int $userId, int $teamId, int $krId)
    {
        /** @var KrWatchlist */
        $KrWatchlist = ClassRegistry::init("KrWatchlist");

        $watchlist = $this->findOrCreateWatchlist($userId, $teamId);
        $conditions = [
            "key_result_id" => (int)$krId, 
            "watchlist_id" => $watchlist['Watchlist']['id'], 
            "del_flg" => false
        ];

        if (empty($KrWatchlist->find("first", ["conditions" => $conditions]))) {
            $KrWatchlist->create();
            $KrWatchlist->save($conditions);
        }

        return $watchlist;
    }

    public function remove(int $userId, int $teamId, int $krId)
    {
        /** @var KrWatchlist */
        $KrWatchlist = ClassRegistry::init("KrWatchlist");

        $watchlist = $this->findOrCreateWatchlist($userId, $teamId);

        $KrWatchlist->deleteAll([
            "key_result_id" => $krId, 
            "watchlist_id" => $watchlist['Watchlist']['id']
        ]);

        return $watchlist;
    }

    public function findOrCreateWatchlist(int $userId, int $teamId): array
    {
        /** @var Watchlist */
        $Watchlist = ClassRegistry::init("Watchlist");

        // use 1 watchlist per user for phase 1, will allow configuration of watchlist in phase 2
        $defaultWatchlistName = "Important";
        $scope = ["conditions" => ["Watchlist.name" => $defaultWatchlistName]];
        $watchlist = $Watchlist->findByUserAndTeam($userId, $teamId, $scope);

        if (empty($watchlist)) {
            $data = [
                "name" => $defaultWatchlistName,
                "user_id" => $userId,
                "team_id" => $teamId,
            ];

            $Watchlist->create();
            $entity = $Watchlist->useType()->useEntity()->save($data, false);
            $watchlist = ["Watchlist" => $entity->toArray()];
        }

        return $watchlist;
    }

    public function getWatchlistProgressForGraph(
        $watchlistId,
        $graphStartDate,
        $graphEndDate,
        $plotEndDate
    ): array {
        /** @var GoalService */
        $GoalService = ClassRegistry::init('GoalService');
        $targetProgress = $this->findTargetProgress($watchlistId);
        $progressLogs = $this->findProgressLogsForList($watchlistId);
        $logsByDate = $this->sumDailyProgressLogs($progressLogs);
        $xLabels = ['x'];
        $datapoints = ['data'];

        $TimeEx = new TimeExHelper(new View());
        $timestamp = strtotime($graphStartDate);
        $graphEndTimestamp = strtotime($graphEndDate);
        $cummulativeVal = 0;

        while ($timestamp <= $graphEndTimestamp) {
            $xLabels[] = $TimeEx->formatDateI18n($timestamp, false);

            $strDate = date('Y-m-d', $timestamp);

            if (array_key_exists($strDate, $logsByDate)) {
                $cummulativeVal += ($logsByDate[$strDate] / $targetProgress * 100);
                $datapoints[] = $cummulativeVal;
            } else if ($timestamp > time()) {
                $datapoints[] = null;
            } 

            $timestamp = strtotime('+1 day', $timestamp);
        }

        $sweetSpot = $GoalService->getSweetSpot($graphStartDate, $graphEndDate);

        return [
            array_merge(['sweet_spot_top'], $sweetSpot['top']),
            array_merge(['sweet_spot_bottom'], $sweetSpot['bottom']),
            $datapoints,
            $xLabels,
        ];
    }

    private function findTargetProgress($watchlistId) {
        /** @var KrWatchlist */
        $KrWatchlist = ClassRegistry::init("KrWatchlist");
        $opts = [
            'conditions' => ['KrWatchlist.watchlist_id' => $watchlistId],
            'joins' => [
                [
                    'alias' => 'KeyResult',
                    'table' => 'key_results',
                    'conditions' => [
                        'KeyResult.id = KrWatchlist.key_result_id',
                        'KeyResult.del_flg != 1'
                    ]
                ]
            ],
            'fields' => [
                'SUM(COALESCE(KeyResult.target_value,0)) AS target_progress',
            ]
        ];

        $row = $KrWatchlist->find('first', $opts);
        return $row[0]['target_progress'];

    }

    private function findProgressLogsForList($watchlistId) 
    {
        /** @var KrWatchlist */
        $KrWatchlist = ClassRegistry::init("KrWatchlist");
        $opts = [
            'conditions' => ['KrWatchlist.watchlist_id' => $watchlistId],
            'joins' => [
                [
                    'alias' => 'KeyResult',
                    'table' => 'key_results',
                    'conditions' => [
                        'KeyResult.id = KrWatchlist.key_result_id',
                        'KeyResult.del_flg != 1'
                    ]
                ],
                [
                    'alias' => 'KrProgressLog',
                    'table' => 'kr_progress_logs',
                    'conditions' => 'KrProgressLog.key_result_id = KrWatchlist.key_result_id'
                ],
            ],
            'fields' => [
                'KrProgressLog.change_value',
                'KrProgressLog.created',
            ]
        ];

        $rows = $KrWatchlist->find('all', $opts);
        return Hash::extract($rows, '{n}.KrProgressLog');
    }

    private function sumDailyProgressLogs($progressLogs) 
    {
        return array_reduce($progressLogs, function($acc, $progressLog) {
            $logDate = AppUtil::dateYmd($progressLog['created']);

            if (!array_key_exists($logDate, $acc)) {
                $acc[$logDate] = 0;
            }

            $acc[$logDate] += $progressLog['change_value'];
            return $acc;
        }, []);
    }
}
