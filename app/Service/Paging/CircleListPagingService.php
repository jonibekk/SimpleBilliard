<?php
App::import('Lib/Paging', 'BasePagingService');
App::import('Lib/Paging', 'PagingRequest');
App::import('Service', 'ImageStorageService');
App::import('Lib/DataExtender', 'CircleMemberInfoDataExtender');
App::uses('Circle', 'Model');
App::uses('CircleMember', 'Model');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/06/28
 * Time: 11:23
 */
class CircleListPagingService extends BasePagingService
{
    const EXTEND_ALL = 'ext:circle:all';
    const EXTEND_MEMBER_INFO = 'ext:circle:member_info';

    protected function readData(PagingRequest $pagingRequest, int $limit): array
    {
        $options = $this->createSearchCondition($pagingRequest);

        $options['limit'] = $limit;
        $options['order'] = $pagingRequest->getOrders();
        $options['conditions']['AND'][] = $pagingRequest->getPointersAsQueryOption();

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

        $publicOnlyFlag = boolval(Hash::get($conditions, 'public_only', true));
        $joinedFlag = boolval(Hash::get($conditions, 'joined', true));

        if (empty($userId)) {
            GoalousLog::error("Missing user ID for circle list paging", $conditions);
            throw new InvalidArgumentException("Missing user ID");
        }
        if (empty($teamId)) {
            GoalousLog::error("Missing team ID for circle list paging", $conditions);
            throw new InvalidArgumentException("Missing team ID");
        }

        $conditions = [
            'conditions' => [
                'Circle.team_id' => $teamId,
                'Circle.del_flg' => false
            ],
            'conversion' => true
        ];

        if ($publicOnlyFlag === true) {
            $conditions['conditions']['Circle.public_flg'] = $publicOnlyFlag;
        }

        /** @var CircleMember $CircleMember */
        $CircleMember = ClassRegistry::init('CircleMember');

        $db = $CircleMember->getDataSource();

        $subQuery = $db->buildStatement([
            'conditions' => [
                'CircleMember.user_id' => $userId,
                'CircleMember.del_flg' => false
            ],
            'fields'     => [
                'CircleMember.circle_id'
            ],
            'table'      => 'circle_members',
            'alias'      => 'CircleMember'
        ], $CircleMember);
        $subQuery = 'Circle.id ' . (($joinedFlag) ? 'IN' : 'NOT IN') . ' (' . $subQuery . ') ';

        $subQueryExpression = $db->expression($subQuery);
        $conditions['conditions'][] = $subQueryExpression;

        return $conditions;
    }

    protected function countData(PagingRequest $request): int
    {
        $options = $this->createSearchCondition($request);

        /** @var Circle $Circle */
        $Circle = ClassRegistry::init('Circle');

        return (int)$Circle->find('count', $options);
    }

    protected function getEndPointerValue($lastElement)
    {
        return [
            ['latest_post_created', "<=", $lastElement['latest_post_created']],
            ['id', '!=', $lastElement['id']]
        ];
    }

    protected function getStartPointerValue($firstElement)
    {
        return [
            ['latest_post_created', ">=", $firstElement['latest_post_created']],
            ['id', '!=', $firstElement['id']]
        ];
    }

    protected function extendPagingResult(array &$resultArray, PagingRequest $request, array $options = [])
    {
        // Set image url each circle
        /** @var ImageStorageService $ImageStorageService */
        $ImageStorageService = ClassRegistry::init('ImageStorageService');
        foreach ($resultArray as $i => $v) {
            $resultArray[$i]['img_url'] = $ImageStorageService->getImgUrlEachSize($resultArray[$i], 'Circle');
        }

        if (in_array(self::EXTEND_ALL, $options) || in_array(self::EXTEND_MEMBER_INFO, $options)) {

            $userId = $request->getResourceId() ?: $request->getCurrentUserId();

            /** @var CircleMemberInfoDataExtender $CircleMemberInfoDataExtender */
            $CircleMemberInfoDataExtender = ClassRegistry::init('CircleMemberInfoDataExtender');

            if (empty($userId)) {
                GoalousLog::error("Missing User ID for data extension");
                throw new InvalidArgumentException("Missing User ID for data extension");
            }

            $CircleMemberInfoDataExtender->setUserId($userId);
            $resultArray = $CircleMemberInfoDataExtender->extend($resultArray, "{n}.id", "circle_id");

        }
    }

    protected function beforeRead(PagingRequest $pagingRequest)
    {
        $pagingRequest->addQueriesToCondition(['joined', 'public_only']);
    }

    protected function addDefaultValues(PagingRequest $pagingRequest): PagingRequest
    {
        $pagingRequest->addOrder('latest_post_created');
        return $pagingRequest;
    }

}
