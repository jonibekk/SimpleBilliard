<?php
App::import('Lib/Paging', 'BasePagingService');
App::import('Lib/Paging', 'PagingRequest');
App::import('Service', 'CirclePinService');
App::uses('Circle', 'Model');
App::uses('CircleMember', 'Model');
App::import('Lib/DataExtender', 'CircleExtender');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/06/28
 * Time: 11:23
 */
class CircleListPagingService extends BasePagingService
{
    const MAIN_MODEL = 'Circle';

    /**
     * Get all circles and not including with paging data
     *
     * @param       $pagingRequest
     * @param array $extendFlags
     *
     * @return array
     */
    public function getAllData(
        $pagingRequest,
        $extendFlags = []
    ): array
    {
        // Check whether exist current user id and team id
        $this->validatePagingResource($pagingRequest);

        $finalResult = [
            'data' => [],
            'paging' => '',
            'count' => 0
        ];

        //If only 1 flag is given, make it an array
        if (!is_array($extendFlags)) {
            $extendFlags = [$extendFlags];
        }

        $this->beforeRead($pagingRequest);
        $pagingRequest = $this->addDefaultValues($pagingRequest);

        $queryResult = $this->readData($pagingRequest, 0);
        $finalResult['count'] = count($queryResult);

        if (!empty($extendFlags) && !empty($queryResult)) {
            $this->extendPagingResult($queryResult, $pagingRequest, $extendFlags);
        }

        $this->afterRead($queryResult, $pagingRequest);

        $finalResult['data'] = $queryResult;

        return $finalResult;
    }

    protected function readData(PagingRequest $pagingRequest, int $limit): array
    {
        $options = $this->createSearchCondition($pagingRequest);

        $options['limit'] = $limit == 0 ? null : $limit;
        $options['conditions'][] = $pagingRequest->getPointersAsQueryOption();

        /** @var Circle $Circle */
        $Circle = ClassRegistry::init('Circle');

        $result = $Circle->useType()->find('all', $options);

        return Hash::extract($result, '{n}.Circle');
    }

    private function createSearchCondition(PagingRequest $pagingRequest)
    {
        $conditions = $pagingRequest->getConditions(true);

        //Get user ID from given resource ID. If not exist, use current user's ID
        $userId = $pagingRequest->getResourceId() ?: $pagingRequest->getCurrentUserId();
        $teamId = $pagingRequest->getCurrentTeamId();

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

        return $searchConditions;
    }

    /**
     * Add condition for pinned circles
     *
     * @param array $searchConditions
     * @param int $userId
     * @param int $teamId
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
     * @param int $userId
     * @param int $teamId
     * @param bool $joinedFlag
     *
     * @return array
     */
    private function addSearchConditionForJoined(
        array $searchConditions,
        int $userId,
        int $teamId,
        bool $joinedFlag
    ): array
    {
        /** @var CircleMember $CircleMember */
        $CircleMember = ClassRegistry::init('CircleMember');

        $db = $CircleMember->getDataSource();

        $subQuery = $db->buildStatement([
            'conditions' => [
                'CircleMember.user_id' => $userId,
                'CircleMember.del_flg' => false,
            ],
            'fields' => [
                'CircleMember.circle_id'
            ],
            'table' => 'circle_members',
            'alias' => 'CircleMember'
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

    protected function countData(PagingRequest $request): int
    {
        $options = $this->createSearchCondition($request);

        /** @var Circle $Circle */
        $Circle = ClassRegistry::init('Circle');

        return (int)$Circle->find('count', $options);
    }

    protected function createPointer(
        array $lastElement,
        array $headNextElement = [],
        PagingRequest $pagingRequest = null
    ): PointerTree
    {

        $prevLatestPost = $pagingRequest->getPointer('latest_post_created')[2] ?? -1;

        if ($lastElement['latest_post_created'] == $headNextElement['latest_post_created'] ||
            $lastElement['latest_post_created'] == $prevLatestPost) {
            $orCondition = new PointerTree('OR', [static::MAIN_MODEL . '.id', '<', $lastElement['id']]);
            $condition = new PointerTree('AND', $orCondition,
                ['latest_post_created', '<=', $lastElement['latest_post_created']]);
            return $condition;
        } else {
            return new PointerTree(['latest_post_created', '<', $lastElement['latest_post_created']]);
        }
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
        $pagingRequest->addQueriesToCondition(['joined', 'public_only', 'pinned']);
        return $pagingRequest;
    }

    protected function addDefaultValues(PagingRequest $pagingRequest): PagingRequest
    {
        $pagingRequest->addOrder('latest_post_created');
        $pagingRequest->addOrder('id');
        return $pagingRequest;
    }

}
