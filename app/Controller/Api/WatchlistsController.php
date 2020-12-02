<?php
App::uses('BasePagingController', 'Controller/Api');
App::uses('Group', 'Model');
App::uses('KeyResult', 'Model');
App::import('Service', 'WatchlistService');
App::import('Service', 'KrProgressService');
App::import('Controller/Traits/Notification', 'TranslationNotificationTrait');
App::import('Service', 'ImageStorageService');
App::import('Policy', 'WatchlistPolicy');

use Goalous\Exception as GlException;

class WatchlistsController extends BasePagingController
{
    use TranslationNotificationTrait;

    public $components = [
        'NotifyBiz',
    ];

    public function get_list()
    {
        // @var WatchlistService ;
        $WatchlistService = ClassRegistry::init("WatchlistService");
        // @var Watchlist ;
        $Watchlist = ClassRegistry::init("Watchlist");

        $userId = $this->getUserId();
        $teamId = $this->getTeamId();

        // TODO: Remove once we enter phase 2
        // Creates the Important list for users if they haven't watched any KR before
        $WatchlistService->findOrCreateWatchlist($userId, $teamId);

        $policy = new WatchlistPolicy($userId, $teamId);
        $scope = $policy->scope();
        $results = $Watchlist->findWithKrCount($scope);
        $watchlists = Hash::extract($results, '{n}.Watchlist');

        $krProgressService = new KrProgressService($this->request, $userId, $teamId);
        $myKrsCount = count($krProgressService->findKrs('my_krs'));

        $myKrsList = [
            'id' => KrProgressService::MY_KR_ID,
            'kr_count' => $myKrsCount,
        ];

        $data =  array_merge([$myKrsList], $watchlists);

        return ApiResponse::ok()->withData($data)->getResponse();
    }

    public function get_detail(string $id)
    {
        if ($id !== KrProgressService::MY_KR_ID) {
            // @var Watchlist ;
            $Watchlist = ClassRegistry::init("Watchlist");
            $watchlist = $Watchlist->findById($id)['Watchlist'];
            $this->authorize('read', $watchlist);
        }

        $krProgressService = new KrProgressService($this->request, $this->getUserId(), $this->getTeamId());
        $krs = $krProgressService->findKrs($id);
        $result = $krProgressService->processKeyResults($krs);
        $response = [
            'id' => $id,
            'kr_count' => count($krs),
            'kr_with_progress' => $result['data']
        ];

        return ApiResponse::ok()->withData($response)->getResponse();
    }

    public function authorize(string $method, array $watchlist): void
    {
        $policy = new WatchlistPolicy($this->getUserId(), $this->getTeamId());

        switch ($method) {
            case 'read':
                if (!$policy->read($watchlist)) {
                    throw new GlException\Auth\AuthFailedException(__("You don't have permission to access this watchlist"));
                }
                break;
        }
    }
}
