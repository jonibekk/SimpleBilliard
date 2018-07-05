<?php
App::import('Lib/Paging', 'BasePagingService');
App::import('Lib/Paging', 'PagingRequest');
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
        $pagingRequest->addQueriesToCondition(['joined', 'public_only']);
        $options = $this->createSearchCondition($pagingRequest->getConditions(true));

        $options['limit'] = $limit;
        $options['order'] = $pagingRequest->getOrders();
        $options['conditions']['AND'][] = $pagingRequest->getPointersAsQueryOption();
        $options['conversion'] = true;

        /** @var Circle $Circle */
        $Circle = ClassRegistry::init('Circle');

        $result = $Circle->find('all', $options);

        return Hash::extract($result, '{n}.Circle');
    }

    private function createSearchCondition(array $conditions)
    {
        //Get user ID from given resource ID. If not exist, use current user's ID
        $userId = Hash::get($conditions, 'res_id') ?? Hash::get($conditions, 'current_user_id');
        $teamId = Hash::get($conditions, 'current_team_id');
        $publicOnlyFlag = boolval(Hash::get($conditions, 'public_only', true));
        $joinedFlag = boolval(Hash::get($conditions, 'joined', true));

        if (empty($userId) || empty($teamId)) {
            GoalousLog::error("Missing parameter for circle list paging", $conditions);
            throw new RuntimeException("Missing parameter for circle list paging");
        }

        $conditions = [
            'conditions' => [
                'Circle.team_id' => $teamId,
                'Circle.del_flg' => false
            ],
            'conversion' => true
        ];

        /** @var Circle $Circle */
        $Circle = ClassRegistry::init('Circle');

        $db = $Circle->getDataSource();

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
        ], $Circle);
        $subQuery = 'Circle.id ' . (($joinedFlag) ? 'IN' : 'NOT IN') . ' (' . $subQuery . ') ';

        $subQueryExpression = $db->expression($subQuery);
        $conditions['conditions'][] = $subQueryExpression;

        if ($publicOnlyFlag) {
            $conditions['conditions']['Circle.public_flg'] = $publicOnlyFlag;
        }

        return $conditions;
    }

    protected function countData(array $conditions): int
    {
        $options = $this->createSearchCondition($conditions);

        /** @var Circle $Circle */
        $Circle = ClassRegistry::init('Circle');

        return (int)$Circle->find('count', $options);
    }

    protected function getEndPointerValue($lastElement)
    {
        return ['latest_post_created', "<", $lastElement['latest_post_created']];
    }

    protected function getStartPointerValue($firstElement)
    {
        return ['latest_post_created', ">", $firstElement['latest_post_created']];
    }

    protected function extendPagingResult(&$resultArray, $conditions, $options = [])
    {
        if (in_array(self::EXTEND_ALL, $options) || in_array(self::EXTEND_MEMBER_INFO, $options)) {
            /** @var CircleMember $CircleMember */
            $CircleMember = ClassRegistry::init('CircleMember');

            $userId = Hash::get($conditions, 'res_id') ?? Hash::get($conditions, 'current_user_id');
            
            if (empty($userId)) {
                GoalousLog::error("Missing User ID for data extension");
                throw new RuntimeException("Missing User ID for data extension");
            }

            foreach ($resultArray as &$circle) {
                $options = [
                    'conditions' => [
                        'circle_id' => $circle['id'],
                        'user_id'   => $userId
                    ],
                    'fields'     => [
                        'unread_count',
                        'admin_flg'
                    ],
                ];
                $result = $CircleMember->useType()->find('first', $options);
                $memberInfo = Hash::get($result, 'CircleMember');

                $circle = array_merge($circle, $memberInfo);
            }
        }
    }

    protected function addDefaultValues(PagingRequest $pagingRequest)
    {
        $pagingRequest->addOrder('latest_post_created');
        return $pagingRequest;
    }

}