<?php
App::import('Lib/Paging', 'BasePagingService');
App::import('Lib/Paging', 'PagingRequest');
App::uses("CircleMember", 'Model');
App::uses("User", 'Model');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/08/23
 * Time: 14:00
 */
class CircleMemberPagingService extends BasePagingService
{
    const MAIN_MODEL = 'CircleMember';

    protected function readData(PagingRequest $pagingRequest, int $limit): array
    {
        $conditions = $this->createSearchCondition($pagingRequest);

        $conditions['limit'] = $limit;
        $conditions['order'] = $pagingRequest->getOrders();
        $conditions['conditions'][] = $pagingRequest->getPointersAsQueryOption();

        /** @var CircleMember $CircleMember */
        $CircleMember = ClassRegistry::init('CircleMember');

        $result = $CircleMember->useType()->find('all', $conditions);

        $userIds = Hash::extract($result, '{n}.CircleMember.user_id');

        if (empty($userIds)) {
            return [];
        }

        /** @var User $User */
        $User = ClassRegistry::init('User');

        $userCondition = [
            'conditions' => [
                'User.id' => $userIds
            ]
        ];

        $result = $User->useType()->find('all', $userCondition);

        return Hash::extract($result, '{n}.User');
    }

    protected function countData(PagingRequest $request): int
    {
        $conditions = $this->createSearchCondition($request);

        /** @var CircleMember $CircleMember */
        $CircleMember = ClassRegistry::init('CircleMember');

        return (int)$CircleMember->find('count', $conditions);
    }

    private function createSearchCondition(PagingRequest $pagingRequest)
    {
        $teamId = $pagingRequest->getCurrentTeamId();
        $circleId = $pagingRequest->getResourceId();

        $conditions = [
            'table'      => 'circle_members',
            'alias'      => 'CircleMember',
            'fields'     => [
                'CircleMember.user_id'
            ],
            'conditions' => [
                'CircleMember.team_id'   => $teamId,
                'CircleMember.circle_id' => $circleId
            ]
        ];

        return $conditions;
    }

    protected function addDefaultValues(PagingRequest $pagingRequest): PagingRequest
    {
        $pagingRequest->addOrder(static::MAIN_MODEL . 'last_posted');
        $pagingRequest->addOrder(static::MAIN_MODEL . 'id');
        return $pagingRequest;
    }

    protected function createPointer(
        array $lastElement,
        array $headNextElement = [],
        PagingRequest $pagingRequest = null
    ): PointerTree {
        $prevLastPosted = $pagingRequest->getPointer('last_posted')[2] ?? -1;

        if ($lastElement['last_posted'] == $headNextElement['last_posted'] ||
            $lastElement['last_posted'] == $prevLastPosted) {
            $orCondition = new PointerTree('OR', [static::MAIN_MODEL . '.id', '<', $lastElement['id']]);
            $condition = new PointerTree('AND', $orCondition,
                ['last_posted', '<=', $lastElement['last_posted']]);
            return $condition;
        } else {
            return new PointerTree(['last_posted', '<', $lastElement['last_posted']]);
        }
    }
}