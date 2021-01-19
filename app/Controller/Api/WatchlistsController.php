<?php
App::uses('BasePagingController', 'Controller/Api');
App::uses('Group', 'Model');
App::uses('Term', 'Model');
App::uses('KeyResult', 'Model');
App::import('Service', 'WatchlistService');
App::import('Service', 'KrProgressService');
App::import('Service', 'TermService');
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
        // @var TermService ;
        $TermService = ClassRegistry::init("TermService");
        // @var WatchlistService ;
        $WatchlistService = ClassRegistry::init("WatchlistService");

        $term = $TermService->getCurrentTerm($this->getTeamId());
        $data = $WatchlistService->getForTerm(
            $this->getUserId(),
            $this->getTeamId(),
            $term['id']
        );

        return ApiResponse::ok()->withData($data)->getResponse();
    }

    public function get_detail(string $id)
    {
        try {
            if ($id !== KrProgressService::MY_KR_ID) {
                $watchlist = $this->findWatchlist($id);
                $this->authorize('read', $watchlist);
            }
        } catch (Exception $e) {
            return $this->generateResponseIfException($e);
        }

        /** @var KrProgressService */
        $KrProgressService = ClassRegistry::init('KrProgressService');

        $opts = [
            'listId' => $id,
            'termId' => $this->request->query('term_id'),
            'goalId' => $this->request->query('goal_id'),
            'limit' => intval($this->request->query('limit'))
        ];



        $findKrRequest = new FindForKeyResultListRequest(
            $this->getUserId(),
            $this->getTeamId(),
            $opts
        );
        $findKrRequest =FindForKeyResultListRequest::initializePeriod($findKrRequest);

        $results = $KrProgressService->getWithGraph($findKrRequest);

        $response = [
            'id' => $id,
            'is_my_krs' => $id === KrProgressService::MY_KR_ID,
            'term_id' => $findKrRequest->getTerm()['id'],
            'kr_count' => $results['data']['krs_total'],
            'kr_with_progress' => $results['data']
        ];

        return ApiResponse::ok()->withData($response)->getResponse();
    }

    public function get_by_term() 
    {
        // @var WatchlistService ;
        $WatchlistService = ClassRegistry::init("WatchlistService");
        // @var Term ;
        $Term = ClassRegistry::init("Term");

        $opts = [
            'conditions' => [
                'team_id' => $this->getTeamId(),
                'start_date <=' => GoalousDateTime::now(),
            ],
            'order' => ['start_date' => 'desc'],
        ];

        $rows = $Term->find('all', $opts);
        $terms = Hash::extract($rows, '{n}.Term');
        $processed = [];

        foreach ($terms as $term) {
            $term['watchlists'] = $WatchlistService->getForTerm(
                $this->getUserId(),
                $this->getTeamId(),
                $term['id'],
                $this->request
            );

            $processed[] = $term;
        }

        return ApiResponse::ok()->withData($processed)->getResponse();
    }

    private function findWatchlist(int $watchlistId): array
    {
        /** @var Watchlist $Watchlist */
        $Watchlist = ClassRegistry::init("Watchlist");
        $watchlist = $Watchlist->getById($watchlistId);

        if (empty($watchlist)) {
            throw new GlException\GoalousNotFoundException(__("This watchlist doesn't exist."));
        }

        return $watchlist;
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
