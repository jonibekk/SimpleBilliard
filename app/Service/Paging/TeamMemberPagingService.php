<?php
App::uses('TeamMember', 'Model');
App::uses('User', 'Model');
App::import('Lib/DataExtender', 'UserDataExtender');
App::import('Lib/Paging', 'BasePagingService');
App::import('Model/Entity', 'TeamMemberEntity');

use Goalous\Enum as Enum;

/**
 * Created by PhpStorm.
 * User: Stephen Raharja
 * Date: 11/28/2018
 * Time: 3:42 PM
 */
class TeamMemberPagingService extends BasePagingService
{
    const MAIN_MODEL = "TeamMember";
    const EXTEND_ALL = "ext:team_member:all";
    const EXTEND_USER = "ext:team_member:user";

    protected function readData(PagingRequest $pagingRequest, int $limit): array
    {
        $conditions = $this->createSearchCondition($pagingRequest);

        $conditions['limit'] = $limit;
        $conditions['order'] = $pagingRequest->getOrders();
        $conditions['conditions'][] = $pagingRequest->getPointersAsQueryOption();

        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init('TeamMember');

        $result = $TeamMember->useType()->find('all', $conditions);

        return Hash::extract($result, '{n}.' . static::MAIN_MODEL);
    }

    protected function countData(PagingRequest $request): int
    {
        $condition = $this->createSearchCondition($request);

        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init('TeamMember');

        return (int)$TeamMember->find('count', $condition);
    }

    protected function createPointer(
        array $lastElement,
        array $headNextElement = [],
        PagingRequest $pagingRequest = null
    ): PointerTree
    {
        $prevLastLogin = $pagingRequest->getPointer(static::MAIN_MODEL . '.last_login')[2] ?? -1;

        if ($lastElement['last_login'] == $headNextElement['last_login'] ||
            $lastElement['last_login'] == $prevLastLogin) {
            $condition = new PointerTree('OR', [static::MAIN_MODEL . '.id', '<', $lastElement['id']],
                [static::MAIN_MODEL . '.last_login', '<=', $lastElement['last_login']]);
            return $condition;
        } else {
            return new PointerTree([static::MAIN_MODEL . '.last_login', '<', $lastElement['last_login']]);
        }
    }

    protected function extendPagingResult(array &$resultArray, PagingRequest $request, array $options = [])
    {
        //TODO
    }

    private function createSearchCondition(PagingRequest $request): array
    {
        $pagingCondition = $request->getConditions(true);

        /** @var User $User */
        $User = ClassRegistry::init('User');

        $excludedIds = Hash::get($pagingCondition, 'excluded_ids', []);
        $keyword = Hash::get($pagingCondition, 'keyword');
        $language = Hash::get($pagingCondition, 'lang');
        $activeOnly = Hash::get($pagingCondition, 'active_only', false);

        //If keyword is defined, create search condition & automatically exclude current user's ID
        if (!empty($keyword)) {
            $keywordCondition = $User->makeUserNameConditions($keyword);
            $excludedIds[] = $request->getCurrentUserId();
        } else {
            $keywordCondition = [];
        }

        $condition = [
            'conditions' => [
                'TeamMember.team_id' => $request->getCurrentTeamId(),
                'TeamMember.del_flg' => false,
                'OR'                 => $keywordCondition
            ],
            'table'      => 'team_members',
            'alias'      => 'TeamMember',
            'fields'     => [
                'TeamMember.id',
                'TeamMember.user_id',
                'TeamMember.last_login'
            ],
            'joins'      => [
                [
                    'type'       => 'INNER',
                    'table'      => 'users',
                    'alias'      => 'User',
                    'conditions' => [
                        'TeamMember.user_id = User.id',
                        'User.del_flg' => false,
                        'NOT'          => [
                            'User.id' => $excludedIds
                        ]
                    ]
                ],
                [
                    'type'       => 'LEFT',
                    'table'      => 'local_names',
                    'alias'      => 'SearchLocalName',
                    'conditions' => [
                        'SearchLocalName.user_id = User.id',
                        'SearchLocalName.language' => $language,
                    ],
                ]
            ]
        ];

        if ($activeOnly) {
            $condition['conditions']['TeamMember.status'] = Enum\Model\TeamMember\Status::ACTIVE;
        } else {
            $condition['conditions']['TeamMember.status != '] = Enum\Model\TeamMember\Status::INVITED;
        }

        return $condition;
    }

    protected function beforeRead(PagingRequest $pagingRequest)
    {
        $pagingRequest->addQueriesToCondition(['excluded_ids', 'active_only']);
        return $pagingRequest;
    }

    protected function addDefaultValues(PagingRequest $pagingRequest): PagingRequest
    {
        $pagingRequest->addOrder(static::MAIN_MODEL . '.last_login');
        $pagingRequest->addOrder(static::MAIN_MODEL . '.id');

        return $pagingRequest;
    }

}
