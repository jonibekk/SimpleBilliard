<?php
App::import('Service/Request/KeyResults', 'ResourceRequest');
App::import('Service', 'AppService');
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
    /** @var FindForKeyResultListRequest **/
    private $request;
    /** @var boolean **/
    private $withKrProgressGraphValues;


    function __construct(CakeRequest $request, int $userId, int $teamId)
    {
        $this->withKrProgressGraphValues = boolval($request->query('with_kr_progress_graph_values'));
        $goalIdSelected = intval($request->query('goal_id'));
        $limit = intval($request->query('limit'));

        $this->userId = $userId;
        $this->teamId = $teamId;
        $currentTerm = $this->initializeTerm();

        $this->request = new FindForKeyResultListRequest(
            $this->userId,
            $this->teamId,
            $currentTerm
        );
        $this->request->setGoalIdSelected($goalIdSelected);
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

    function process(): array
    {
        $results = $this->findKrs();

        $krs = [];
        foreach ($results as $idx => $kr) {
            array_push($krs, $this->processKr($kr));
        };

        $response = $this->formatResponse($krs);

        if ($this->withKrProgressGraphValues) {
            $response = $this->appendProgressGraph($response);
        }

        return $response;
    }

    function findKrs(): array
    {
        /** @var KeyResultService $KeyResultService */
        $KeyResultService = ClassRegistry::init("KeyResultService");
        return $KeyResultService->findForKeyResultList($this->request);
    }

    function processKr($keyResult)
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

    function formatResponse(array $krs): array
    {
        $periodFrom = $this->periodFrom();
        $periodTo = GoalousDateTime::now();

        /** @var GoalService $GoalService */
        $GoalService = ClassRegistry::init('GoalService');
        /** @var KeyResultService $KeyResultService */
        $KeyResultService = ClassRegistry::init("KeyResultService");

        $currentTerm = $this->request->getCurrentTermModel();

        return [
            'data' => [
                'period_kr_collection' => [
                    'from' => $periodFrom->getTimestamp(),
                    'to' => $periodTo->getTimestamp(),
                ],
                'krs_total' => $KeyResultService->countMine($goalIdSelected ?? null, false, $this->userId),
                'krs' => $krs,
                'goals' => $GoalService->findNameListAsMember($this->userId, $currentTerm['start_date'], $currentTerm['end_date']),
            ],
        ];
    }

    function appendProgressGraph(array $response): array
    {
        /** @var GoalService $GoalService */
        $GoalService = ClassRegistry::init('GoalService');
        $currentTerm = $this->request->getCurrentTermModel();

        $todayDate = AppUtil::dateYmd(REQUEST_TIMESTAMP + $currentTerm['timezone'] * HOUR);
        $graphRange = $GoalService->getGraphRange(
            $todayDate,
            GoalService::GRAPH_TARGET_DAYS,
            GoalService::GRAPH_MAX_BUFFER_DAYS
        );
        $progressGraph = $GoalService->getUserAllGoalProgressForDrawingGraph(
            $this->userId,
            $graphRange['graphStartDate'],
            $graphRange['graphEndDate'],
            $graphRange['plotDataEndDate'],
            true
        );
        $TimeEx = new TimeExHelper(new View());
        $krProgressGraphValues = [
            'data'       => [
                'sweet_spot_top' => $progressGraph[0],
                'sweet_spot_bottom' => $progressGraph[1],
                'data' => $progressGraph[2],
                'x' => $progressGraph[3],
            ],
            'start_date' => $TimeEx->formatDateI18n(strtotime($graphRange['graphStartDate'])),
            'end_date'   => $TimeEx->formatDateI18n(strtotime($graphRange['graphEndDate'])),
        ];
        $response['data']['kr_progress_graph'] = $krProgressGraphValues;
        return $response;
    }

    private function periodFrom()
    {
        return GoalousDateTime::now()->copy()->startOfDay()->subDays(7);
    }
}
