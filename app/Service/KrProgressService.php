<?php
App::import('Service/Request/KeyResults', 'ResourceRequest');
App::uses('Team', 'Model');
App::import('Service', 'GoalService');
App::import('Service', 'AppService');
App::import('Service', 'TermService');

/**
 * Class KrProgressService
 */
class KrProgressService extends AppService
{
    /** @var int **/
    private $goalId;
    /** @var string **/
    private $listId;
    /** @var FindForKeyResultListRequest **/
    private $request;

    const MY_KR_ID = 'my_krs';


    function __construct(
        CakeRequest $request, 
        int $userId, 
        int $teamId, 
        string $listId,
        int $termId = null
    )
    {
        // @var TermService ;
        $TermService = ClassRegistry::init("TermService");

        // do not get intval because goal_id can be null
        $requestGoalId = $request->query('goal_id');
        $limit = intval($request->query('limit'));

        $this->listId = $listId;
        $this->goalId = $requestGoalId ? intval($requestGoalId) : null;

        $this->request = new FindForKeyResultListRequest(
            $userId,
            $teamId,
            $TermService->getCurrentTerm($teamId),
            ['limit' => $limit]
        );
    }

    function processKeyResults(array $allKrs): array
    {
        $krs = [];

        foreach ($allKrs as $kr) {
            if ($this->goalId !== null && $this->goalId !== $kr['Goal']['id']) {
                continue;
            } 

            array_push($krs, $this->extendKr($kr));
        };

        return $this->formatResponse($allKrs, $krs);
    }

    function findKrs(): array
    {
        /** @var KeyResultService $KeyResultService */
        $KeyResultService = ClassRegistry::init("KeyResultService");

        if ($this->listId === self::MY_KR_ID) {
            return $KeyResultService->findForKeyResultList($this->request);
        } else {
            return $KeyResultService->findForWatchlist($this->request, $this->listId);
        }
    }

    function extendKr($keyResult)
    {
        /** @var ActionResult $ActionResult */
        $ActionResult = ClassRegistry::init("ActionResult");
        /** @var KrProgressLog $KrProgressLog */
        $KrProgressLog = ClassRegistry::init('KrProgressLog');
        /** @var UserExtension $UserExtension */
        $UserExtension = ClassRegistry::init('UserExtension');
        /** @var GoalExtension $UserExtension */
        $GoalExtension = ClassRegistry::init('GoalExtension');
        /** @var Post $Post */
        $Post = ClassRegistry::init("Post");

        // Find action that has filtered by period
        $actionResults = $ActionResult->getByKrIdAndCreatedFrom($keyResult['KeyResult']['id'], $this->periodFrom());
        $actionResults = Hash::extract($actionResults, "{n}.ActionResult");

        // Total action progress in period
        $changeValueTotal = 0;
        foreach ($actionResults as $i => $actionResult) {
            $krProgressLog = $KrProgressLog->getByActionResultId($actionResult['id'])->toArray();
            $actionResults[$i]['kr_progress_log'] = $krProgressLog;
            $changeValueTotal += $krProgressLog['change_value'];

            // Need a post_id to make link to action detail post.
            $post = $Post->getByActionResultId($actionResult['id'], $this->request->getTeamId());
            $actionResults[$i]['post_id'] = $post['Post']['id'];
            $actionResults[$i] = $UserExtension->extend($actionResults[$i], 'user_id');
        }

        $keyResult['KeyResult'] = $GoalExtension->extend($keyResult['KeyResult'], 'goal_id');

        return array_merge(
            $keyResult['KeyResult'],
            [
                'progress_log_recent_total' => [
                    'change_value' => $changeValueTotal,
                ],
                'action_results' => $actionResults,
            ]
        );
    }

    function formatResponse(array $allKrs, array $krs): array
    {
        $periodFrom = $this->periodFrom();
        $periodTo = GoalousDateTime::now();

        $goals = array_reduce($allKrs, function($acc, $kr) {
            $goalName = $kr['Goal']['name'];
            if (!array_key_exists($goalName, $acc)) {
                $acc[$goalName] = $kr['Goal'];
            }
            return $acc;
        }, []);

        ksort($goals);

        return [
            'data' => [
                'period_kr_collection' => [
                    'from' => $periodFrom->getTimestamp(),
                    'to' => $periodTo->getTimestamp(),
                ],
                'krs_total' => count($allKrs),
                'krs' => $krs,
                'goals' => array_values($goals),
            ],
        ];
    }

    function appendProgressGraph(array $response): array
    {
        $graphRange = $this->generateGraphRange();
        $TimeEx = new TimeExHelper(new View());

        if ($this->listId === self::MY_KR_ID) {
            $progressGraph = $this->generateMyKrProgressGraph($graphRange);
        } else {
            $progressGraph = $this->generateWatchlistGraph($graphRange);
        }

        $krProgressGraph =  [
            'data'       => [
                'sweet_spot_top' => $progressGraph[0],
                'sweet_spot_bottom' => $progressGraph[1],
                'data' => $progressGraph[2],
                'x' => $progressGraph[3],
            ],
            'start_date' => $TimeEx->formatDateI18n(strtotime($graphRange['graphStartDate'])),
            'end_date'   => $TimeEx->formatDateI18n(strtotime($graphRange['graphEndDate'])),
        ];

        $response['data']['kr_progress_graph'] = $krProgressGraph;
        return $response;
    }

    private function generateMyKrProgressGraph($graphRange)
    {
        /** @var GoalService $GoalService */
        $GoalService = ClassRegistry::init('GoalService');

        return $GoalService->getUserAllGoalProgressForDrawingGraph(
            $this->request->getUserId(),
            $graphRange['graphStartDate'],
            $graphRange['graphEndDate'],
            $graphRange['plotDataEndDate'],
            true
        );
    }

    private function generateWatchlistGraph($graphRange)
    {
        /** @var WatchlistService */
        $WatchlistService = ClassRegistry::init('WatchlistService');

        return $WatchlistService->getWatchlistProgressForGraph(
            $this->listId,
            $graphRange['graphStartDate'],
            $graphRange['graphEndDate']
        );
    }

    private function generateGraphRange(): array {
        if ($this->isPastTerm()) {
            // default to entire term length if checking a past term
            return [
                'graphStartDate'  => $this->request->getTerm()['start_date'],
                'graphEndDate'    => $this->request->getTerm()['end_date'],
                'plotDataEndDate' => $this->request->getTerm()['end_date'],
            ];
            
        } else {
            /** @var GoalService $GoalService */
            $GoalService = ClassRegistry::init('GoalService');
            return $GoalService->getGraphRange(
                $this->request->getTodayDate(),
                GoalService::GRAPH_TARGET_DAYS,
                GoalService::GRAPH_MAX_BUFFER_DAYS
            );
        }
    }

    private function isPastTerm(): bool
    {
        return $this->request->getTerm()['end_date'] < $this->request->getTodayDate();
    }

    private function periodFrom()
    {
        return GoalousDateTime::now()->copy()->startOfDay()->subDays(7);
    }
}
