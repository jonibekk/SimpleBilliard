<?php
App::import('Service/Request/KeyResults', 'ResourceRequest');
App::import('Service', 'GoalService');
App::import('Service', 'AppService');

/**
 * Class KrProgressService
 */
class KrProgressService extends AppService
{
    /** @var int **/
    private $userId;
    /** @var int **/
    private $teamId;
    /** @var int **/
    private $goalId;
    /** @var string **/
    private $listId;
    /** @var FindForKeyResultListRequest **/
    private $request;
    /** @var boolean **/
    private $withKrProgressGraphValues;

    const MY_KR_ID = 'my_krs';


    function __construct(CakeRequest $request, int $userId, int $teamId, string $listId)
    {
        $this->withKrProgressGraphValues = boolval($request->query('with_kr_progress_graph_values'));
        $limit = intval($request->query('limit'));
        $requestGoalId = $request->query('goal_id');

        $this->userId = $userId;
        $this->teamId = $teamId;
        $this->listId = $listId;
        $this->goalId = $requestGoalId ? intval($requestGoalId) : null;
        $currentTerm = $this->initializeTerm();

        $this->request = new FindForKeyResultListRequest(
            $this->userId,
            $this->teamId,
            $currentTerm
        );
        $this->request->setLimit($limit);
    }

    function initializeTerm(): array
    {
        /** @var Term $Term */
        $Term = ClassRegistry::init("Term");
        $Term->Team->current_team_id = $this->teamId;
        $Term->Team->my_uid = $this->userId;
        $Term->current_team_id = $this->teamId;
        $Term->my_uid = $this->userId;
        $currentTerm = $Term->getCurrentTermData();
        return $currentTerm;
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

        $response = $this->formatResponse($allKrs, $krs);

        if ($this->withKrProgressGraphValues) {
            $response = $this->appendProgressGraph($response);
        }

        return $response;
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
            $post = $Post->getByActionResultId($actionResult['id'], $this->teamId);
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
            $this->userId,
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
            $graphRange['graphEndDate'],
            $graphRange['plotDataEndDate']
        );
    }

    private function generateGraphRange(): array {
        /** @var GoalService $GoalService */
        $GoalService = ClassRegistry::init('GoalService');
        $currentTerm = $this->request->getCurrentTermModel();

        $todayDate = AppUtil::dateYmd(REQUEST_TIMESTAMP + $currentTerm['timezone'] * HOUR);
        return $GoalService->getGraphRange(
            $todayDate,
            GoalService::GRAPH_TARGET_DAYS,
            GoalService::GRAPH_MAX_BUFFER_DAYS
        );
    }

    private function periodFrom()
    {
        return GoalousDateTime::now()->copy()->startOfDay()->subDays(7);
    }
}
