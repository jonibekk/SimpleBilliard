<?php
App::uses('BasePagingController', 'Controller/Api');
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
        // @var KrProgressService ;
        $Watchlist = ClassRegistry::init("Watchlist");
        // @var Watchlist ;
        $Watchlist = ClassRegistry::init("Watchlist");

        $policy = new WatchlistPolicy($this->getUserId(), $this->getTeamId());
        $scope = $policy->scope();
        $results = $Watchlist->findWithKrCount($scope);
        $watchlists = Hash::extract($results, '{n}.Watchlist');

        $krProgressService = new KrProgressService($this->request, $this->getUserId(), $this->getTeamId());
        $myKrsCount = count($krProgressService->findKrs());

        $myKrsList = [
            'id' => 'my_krs',
            'krCount' => $myKrsCount,
        ];

        $data =  array_merge([$myKrsList], $watchlists);

        return ApiResponse::ok()->withData($data)->getResponse();
    }

    public function get_detail(string $id)
    {
        try {
            // @var Watchlist ;
            $Watchlist = ClassRegistry::init("Watchlist");
            $watchlist = $Watchlist->findById($id);
            $this->authorize('read', $watchlist);
        } catch (Exception $e) {
            return $this->generateResponseIfException($e);
        }

        return ApiResponse::ok()->withData($watchlist)->getResponse();
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
