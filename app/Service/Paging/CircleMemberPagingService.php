<?php

App::import('Lib/Paging', 'BasePagingService');
App::import('Lib/Paging', 'PagingRequest');
App::uses("CircleMember", 'Model');
App::uses("User", 'Model');
App::import('Lib/DataExtender', 'CircleMemberExtender');

use Goalous\Enum as Enum;

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

        return Hash::extract($result, '{n}.CircleMember');
    }

    protected function countData(PagingRequest $request): int
    {
        $conditions = $this->createSearchCondition($request);

        /** @var CircleMember $CircleMember */
        $CircleMember = ClassRegistry::init('CircleMember');

        return (int)$CircleMember->find('count', $conditions);
    }

    protected function extendPagingResult(array &$data, PagingRequest $request, array $options = [])
    {
        $userId = $request->getCurrentUserId();
        $teamId = $request->getCurrentTeamId();

        /** @var CircleMemberExtender $CircleMemberExtender */
        $CircleMemberExtender = ClassRegistry::init('CircleMemberExtender');
        $data = $CircleMemberExtender->extendMulti($data, $userId, $teamId, $options);
    }

    private function createSearchCondition(PagingRequest $pagingRequest)
    {
        $teamId = $pagingRequest->getCurrentTeamId();
        $circleId = $pagingRequest->getResourceId();

        $conditions = [
            'fields'     => [
                'CircleMember.id',
                'CircleMember.user_id',
                'CircleMember.last_posted',
                'CircleMember.admin_flg',
                'CircleMember.created'
            ],
            'conditions' => [
                'CircleMember.team_id'   => $teamId,
                'CircleMember.circle_id' => $circleId
            ],
            'joins'      => [
                [
                    'type'       => 'INNER',
                    'table'      => 'team_members',
                    'alias'      => 'TeamMember',
                    'conditions' => [
                        'TeamMember.team_id = CircleMember.team_id',
                        'TeamMember.user_id = CircleMember.user_id',
                        'TeamMember.del_flg' => false,
                        'TeamMember.status'  => Enum\Model\TeamMember\Status::ACTIVE,
                    ]
                ]
            ]
        ];

        return $conditions;
    }

    protected function addDefaultValues(PagingRequest $pagingRequest): PagingRequest
    {
        $pagingRequest->addQueriesToCondition('admin_first');
        if (!empty($pagingRequest->getQuery('admin_first'))) {
            $pagingRequest->addOrder(static::MAIN_MODEL . '.admin_flg');
        }
        $pagingRequest->addOrder(static::MAIN_MODEL . '.last_posted');
        $pagingRequest->addOrder(static::MAIN_MODEL . '.id');

        return $pagingRequest;
    }

    protected function createPointer(
        array $lastElement,
        array $headNextElement = [],
        PagingRequest $pagingRequest = null
    ): PointerTree
    {
        $prevLastPosted = $pagingRequest->getPointer('last_posted')[2] ?? -1;

        $lastPostedTree = new PointerTree(['last_posted', '<', $lastElement['last_posted']]);

        if ($lastElement['last_posted'] == $headNextElement['last_posted'] ||
            $lastElement['last_posted'] == $prevLastPosted) {
            $orCondition = new PointerTree('OR', [static::MAIN_MODEL . '.id', '<', $lastElement['id']]);
            $lastPostedTree = new PointerTree('AND', $orCondition, ['last_posted', '<=', $lastElement['last_posted']]);
        }

        if (empty($pagingRequest->getCondition('admin_first'))) {
            return $lastPostedTree;
        }

        $lastAdmin = $lastElement['admin_flg'];

        $subCondition = new PointerTree('AND', $lastPostedTree, [static::MAIN_MODEL . '.admin_flg', '=', $lastAdmin]);

        if ($lastAdmin) {
            return new PointerTree('OR', $subCondition, [static::MAIN_MODEL . '.admin_flg', '=', false]);
        }

        return $subCondition;
    }
}
