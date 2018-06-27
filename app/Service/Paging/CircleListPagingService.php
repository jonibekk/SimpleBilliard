<?php
App::import('Lib/Paging', 'BasePagingService');
App::uses('PagingCursor', 'Lib/Paging');
App::uses('Circle', 'Model');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/06/28
 * Time: 11:23
 */
class CircleListPagingService extends BasePagingService
{
    protected function readData(PagingCUrsor $pagingCursor, int $limit): array
    {
        $options = $this->createSearchCondition($pagingCursor->getConditions());

        $options['limit'] = $limit;
        $options['order'] = $pagingCursor->getOrders();
        $options['conditions']['AND'][] = $pagingCursor->getPointersAsQueryOption();
        $options['conversion'] = true;

        /** @var Circle $Circle */
        $Circle = ClassRegistry::init('Circle');

        $result = $Circle->find('all', $options);

        return Hash::extract($result, '{n}.Circle');
    }

    protected function countData(array $conditions): int
    {
        $options = $this->createSearchCondition($conditions);

        /** @var Circle $Circle */
        $Circle = ClassRegistry::init('Circle');

        return (int)$Circle->find('count', $options);
    }

    private function createSearchCondition(array $conditions)
    {
        $userId = Hash::get($conditions, 'user_id');
        $teamId = Hash::get($conditions, 'team_id');
        $publicOnlyFlag = Hash::get($conditions, 'public_only_flg') ?? true;
        $joinedFlag = Hash::get($conditions, 'joined') ?? true;

        if (empty($userId) || empty($teamId)) {
            GoalousLog::error("Missing parameter for circle list paging", $conditions);
            throw new RuntimeException("Missing param");
        }

        $conditions = [
            'conditions' => [
                'Circle.team_id' => $teamId,
                'Circle.del_flg' => false
            ],
            'joins'      => [
                [
                    'type'       => 'INNER',
                    'table'      => 'circle_members',
                    'alias'      => 'CircleMember',
                    'conditions' => [
                        'Circle.id' . ($joinedFlag) ? '=' : '!=' . 'CircleMember.circle_id',
                        'CircleMember.user_id' => $userId,
                        'CircleMember.del_flg' => false
                    ]
                ]
            ]
        ];
        if ($publicOnlyFlag) {
            $conditions['conditions']['Circle.public_flg'] = $publicOnlyFlag;
        }

        return $conditions;
    }

    protected function getEndPointerValue($lastElement)
    {
        return ['latest_post_created', "<", $lastElement['latest_post_created']];
    }

    protected function getStartPointerValue($firstElement)
    {
        return ['latest_post_created', ">", $firstElement['latest_post_created']];
    }

}