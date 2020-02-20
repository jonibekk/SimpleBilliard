<?php
App::import('Lib/Paging', 'BasePagingService');
App::import('Lib/Paging', 'PagingRequest');
App::import('Service', 'CirclePinService');
App::uses('Circle', 'Model');
App::uses('CircleMember', 'Model');
App::import('Lib/DataExtender', 'CircleExtender');

class CircleListPagingService extends BasePagingService
{
    const MAIN_MODEL = 'Circle';

    protected function readData(PagingRequest $pagingRequest, int $limit): array
    {

        foreach($pagingRequest as $cond => $val) {
            CakeLog::info(sprintf('page: %s , %s', $cond, $val));
        }
        $options = $this->createSearchCondition($pagingRequest);

        if ($limit) {
            $options['limit'] = $limit;
        }
        $options['conditions'][] = $pagingRequest->getPointersAsQueryOption();

        GoalousLog::info("options: ", $options);

        /** @var Circle $Circle */
        $Circle = ClassRegistry::init('Circle');

        $result = $Circle->useType()->find('all', $options);

        return Hash::extract($result, '{n}.Circle');
    }

    protected function countData(PagingRequest $request): int
    {
        $options = $this->createSearchCondition($request);

        /** @var Circle $Circle */
        $Circle = ClassRegistry::init('Circle');

        return (int)$Circle->find('count', $options);
    }

    private function createSearchCondition(PagingRequest $pagingRequest)
    {
        $conditions = $pagingRequest->getConditions(true);

        //Get user ID from given resource ID. If not exist, use current user's ID
        $userId = $pagingRequest->getResourceId() ?: $pagingRequest->getCurrentUserId();
        $teamId = $pagingRequest->getCurrentTeamId();


        foreach($conditions as $cond => $val) {
            CakeLog::info(sprintf('cond: %s , %s', $cond, $val));
        }

        $searchConditions = [
            'conditions' => [
                'Circle.team_id' => $teamId,
                'Circle.del_flg' => false
            ],
        ];
        $publicOnlyFlag = boolval(Hash::get($conditions, 'public_only', false));
        if ($publicOnlyFlag === true) {
            $searchConditions['conditions']['Circle.public_flg'] = $publicOnlyFlag;
        }

        /* filter pinned  */
        // filtering pinned is more prioritize than filtering joined
        // ※ pinned circles means already joined.
        $pinnedFlag = boolval(Hash::get($conditions, 'pinned', false));
        if ($pinnedFlag) {
            return $this->addSearchConditionForPinned($searchConditions, $userId, $teamId);
        }

        /* filter joined  */
        $joinedFlag = boolval(Hash::get($conditions, 'joined', true));
        $searchConditions = $this->addSearchConditionForJoined($searchConditions, $userId, $teamId, $joinedFlag);
        $searchConditions['order'] = $pagingRequest->getOrders();

        /* filter new created */
        $newcreatedFlag = boolval(Hash::get($conditions, 'newcreated', false));
        if ($newcreatedFlag) {
            GoalousLog::info("newcreatedFlag: ");
            return $this->addSearchConditionForNewCreated($searchConditions);
        }
        return $searchConditions;
    }

    /**
     * Add condition for filter new created circle
     *
     * @param array $searchConditions
     *
     * @return array
     */
    private function addSearchConditionForNewCreated(array $searchConditions): array
    {
        $searchConditions['conditions']['Circle.created >'] = GoalousDateTime::now()->subDays(30)->getTimestamp();
        return $searchConditions;
    }

    /**
     * Add condition for pinned circles
     *
     * @param array $searchConditions
     * @param int   $userId
     * @param int   $teamId
     *
     * @return array
     */
    private function addSearchConditionForPinned(array $searchConditions, int $userId, int $teamId): array
    {
        /** @var CirclePinService $CirclePinService */
        $CirclePinService = ClassRegistry::init('CirclePinService');
        $circleIds = $CirclePinService->getPinnedCircleIds($userId, $teamId);
        $searchConditions['conditions']['Circle.id'] = $circleIds;
        $searchConditions['order'] = "FIELD(Circle.id, " . implode($circleIds, ',') . ")";
        return $searchConditions;
    }

    /**
     * Add condition for joined circles
     *
     * @param array $searchConditions
     * @param int   $userId
     * @param int   $teamId
     * @param bool  $joinedFlag
     *
     * @return array
     */
    private function addSearchConditionForJoined(
        array $searchConditions,
        int $userId,
        int $teamId,
        bool $joinedFlag
    ): array {
        /** @var CircleMember $CircleMember */
        $CircleMember = ClassRegistry::init('CircleMember');

        $db = $CircleMember->getDataSource();

        $subQuery = $db->buildStatement([
            'conditions' => [
                'CircleMember.user_id' => $userId,
                'CircleMember.del_flg' => false,
            ],
            'fields'     => [
                'CircleMember.circle_id'
            ],
            'table'      => 'circle_members',
            'alias'      => 'CircleMember'
        ], $CircleMember);
        $subQuery = 'Circle.id ' . (($joinedFlag) ? 'IN' : 'NOT IN') . ' (' . $subQuery . ') ';
        $subQueryExpression = $db->expression($subQuery);
        $searchConditions['conditions'][] = $subQueryExpression;
        if (!$joinedFlag) {
            $searchConditions['conditions']['Circle.public_flg'] = true;
        }

        // Exclude pinned circles
        if ($joinedFlag) {
            /** @var CirclePinService $CirclePinService */
            $CirclePinService = ClassRegistry::init('CirclePinService');
            $circleIds = $CirclePinService->getPinnedCircleIds($userId, $teamId);
            $searchConditions['conditions'][] = ['Circle.id NOT IN' => $circleIds];
        }

        return $searchConditions;
    }

    protected function extendPagingResult(array &$data, PagingRequest $request, array $options = [])
    {
        $userId = $request->getResourceId() ?: $request->getCurrentUserId();
        $teamId = $request->getCurrentTeamId();

        /** @var CircleExtender $CircleExtender */
        $CircleExtender = ClassRegistry::init('CircleExtender');

        // Set data whether user joined all circles or not to extend list
        $joined = boolval(Hash::get($request->getConditions(), 'joined', true));
        $CircleExtender->joined = $joined;

        $data = $CircleExtender->extendMulti($data, $userId, $teamId, $options);
    }

    protected function beforeRead(PagingRequest $pagingRequest)
    {
        $pagingRequest->addQueriesToCondition(['joined', 'public_only', 'pinned', 'newcreated']);
        return $pagingRequest;
    }

    protected function addDefaultValues(PagingRequest $pagingRequest): PagingRequest
    {
        $conditions = $pagingRequest->getConditions();
        if (empty(Hash::get($conditions, 'pinned'))) {
            $pagingRequest->addOrder('Circle.latest_post_created');
            $pagingRequest->addOrder('Circle.id');
        }
        return $pagingRequest;
    }

    protected function createPointer(
        array $lastElement,
        array $headNextElement = [],
        PagingRequest $pagingRequest = null
    ): PointerTree {
        $conditions = $pagingRequest->getConditions();
        if (empty(Hash::get($conditions, 'pinned'))) {
            $prevLastPosted = $pagingRequest->getPointer('last_posted')[2] ?? -1;

            if ($lastElement['latest_post_created'] == $headNextElement['latest_post_created'] ||
                $lastElement['latest_post_created'] == $prevLastPosted) {
                $orCondition = new PointerTree('OR', [static::MAIN_MODEL . '.id', '<', $lastElement['id']]);
                $condition = new PointerTree('AND', $orCondition,
                    ['latest_post_created', '<=', $lastElement['latest_post_created']]);
                return $condition;
            } else {
                return new PointerTree(['latest_post_created', '<', $lastElement['latest_post_created']]);
            }
        }
        return new PointerTree();
    }

}
