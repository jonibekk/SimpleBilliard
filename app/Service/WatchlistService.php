
<?php
App::import('Service', 'AppService');
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
    }

    public function remove(int $userId, int $teamId, int $krId)
    {
    }

    private function findOrCreateWatchlist(int $userId, int $teamId): array
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
            $watchlist = $entity->toArray();
        }

        GoalousLog::info("watchlist", [$watchlist]);

        return $watchlist;
    }
}
