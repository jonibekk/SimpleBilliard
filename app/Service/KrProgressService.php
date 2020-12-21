<?php
App::import('Service/Request/KeyResults', 'ResourceRequest');
App::uses('Team', 'Model');
App::import('Service', 'GoalService');
App::import('Service', 'AppService');
App::import('Service', 'TermService');
App::uses('Team', 'Model');

/**
 * Class KrProgressService
 */
class KrProgressService extends AppService
{
    const MY_KR_ID = 'my_krs';

    function getWithGraph(FindForKeyResultListRequest $request): array
    {
        $allKrs = $this->findKrs($request);
        $processedKrs = $this->processKeyResults($request, $allKrs);
        $response = $this->formatResponse($request, $allKrs, $processedKrs);
        return $this->appendProgressGraph($request, $response);
    }

    function processKeyResults(FindForKeyResultListRequest $request, array $allKrs): array
    {
        $limit = $request->getLimit();
        $goalId = $request->getGoalId();
        $krs = [];

        foreach ($allKrs as $idx => $kr) {
            if ($goalId !== null && $goalId !== $kr['Goal']['id']) {
                continue;
            } 
            if (empty($limit) || $idx < $limit) {
                array_push($krs, $this->extendKr($request, $kr));
            }
        };

        return $krs;
    }

    function findKrs(FindForKeyResultListRequest $request): array
    {
        /** @var KeyResultService $KeyResultService */
        $KeyResultService = ClassRegistry::init("KeyResultService");

        if ($request->getListId() === self::MY_KR_ID) {
            return $KeyResultService->findForKeyResultList($request);
        } else {
            return $KeyResultService->findForWatchlist($request);
        }
    }

    function extendKr(FindForKeyResultListRequest $request, array $keyResult)
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
            $post = $Post->getByActionResultId($actionResult['id'], $request->getTeamId());
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

    function formatResponse(FindForKeyResultListRequest $request, array $allKrs, array $krs): array
    {
        if ($request->isPastTerm()) {
            $periodFrom = $request->getTerm()['start_date'];
            $periodTo = $request->getTerm()['end_date'];
        } else {
            $periodFrom = $this->periodFrom()->getTimestamp();
            $periodTo = GoalousDateTime::now()->getTimestamp();
        }

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
                    'from' => $periodFrom,
                    'to' => $periodTo,
                ],
                'krs_total' => count($allKrs),
                'krs' => $krs,
                'goals' => array_values($goals),
            ],
        ];
    }

    function appendProgressGraph(FindForKeyResultListRequest $request, array $response): array
    {
        $graphRange = $this->generateGraphRange($request);
        $TimeEx = new TimeExHelper(new View());

        if ($request->getListId() === self::MY_KR_ID) {
            $progressGraph = $this->generateMyKrProgressGraph($request, $graphRange);
        } else {
            $progressGraph = $this->generateWatchlistGraph($request, $graphRange);
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

    private function generateMyKrProgressGraph(FindForKeyResultListRequest $request, array $graphRange)
    {
        /** @var GoalService $GoalService */
        $GoalService = ClassRegistry::init('GoalService');

        return $GoalService->getUserAllGoalProgressForDrawingGraph(
            $request->getUserId(),
            $graphRange['graphStartDate'],
            $graphRange['graphEndDate'],
            $graphRange['plotDataEndDate'],
            true,
            $request->getTerm()->toArray()
        );
    }

    private function generateWatchlistGraph(FindForKeyResultListRequest $request, array $graphRange)
    {
        /** @var WatchlistService */
        $WatchlistService = ClassRegistry::init('WatchlistService');

        return $WatchlistService->getWatchlistProgressForGraph(
            $request->getListId(),
            $graphRange['graphStartDate'],
            $graphRange['graphEndDate'],
            $request->getTerm()->toArray()
        );
    }

    private function generateGraphRange(FindForKeyResultListRequest $request): array {
        if ($request->isPastTerm()) {
            // default to entire term length if checking a past term
            return [
                'graphStartDate'  => $request->getTerm()['start_date'],
                'graphEndDate'    => $request->getTerm()['end_date'],
                'plotDataEndDate' => $request->getTerm()['end_date'],
            ];
            
        } else {
            /** @var GoalService $GoalService */
            $GoalService = ClassRegistry::init('GoalService');
            return $GoalService->getGraphRange(
                $request->getTodayDate(),
                GoalService::GRAPH_TARGET_DAYS,
                GoalService::GRAPH_MAX_BUFFER_DAYS
            );
        }
    }

    private function periodFrom()
    {
        return GoalousDateTime::now()->copy()->startOfDay()->subDays(7);
    }
}
