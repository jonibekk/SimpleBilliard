<?php
App::import('Lib/Paging', 'BasePagingService');
App::import('Lib/Paging', 'PagingRequest');
App::uses('Circle', 'Model');
App::import('Lib/DataExtender', 'CircleExtender');

class RecentCircleListPagingService extends BasePagingService
{
    const MAIN_MODEL = 'Circle';

    protected function readData(PagingRequest $pagingRequest, int $limit): array
    {
        $conditions = $this->createCondition($pagingRequest);

        $conditions['limit'] = $limit;
        $conditions['order'] = $pagingRequest->getOrders();
        $conditions['conditions'][] = $pagingRequest->getPointersAsQueryOption();

        /** @var Circle $Circle */
        $Circle = ClassRegistry::init('Circle');

        $result = $Circle->useType()->find('all', $conditions);

        return Hash::extract($result, '{n}.Circle');
    }

    protected function countData(PagingRequest $request): int
    {
        return -1;
    }

    protected function createPointer(
        array $lastElement,
        array $headNextElement = [],
        PagingRequest $pagingRequest = null
    ): PointerTree {
        $prevLastPosted = $pagingRequest->getPointer('latest_post_created')[2] ?? -1;

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

    protected function extendPagingResult(array &$data, PagingRequest $request, array $options = [])
    {
        $userId = $request->getCurrentUserId();
        $teamId = $request->getCurrentTeamId();

        /** @var CircleExtender $CircleExtender */
        $CircleExtender = ClassRegistry::init('CircleExtender');
        $data = $CircleExtender->extendMulti($data, $userId, $teamId, $options);
    }

    protected function addDefaultValues(PagingRequest $pagingRequest): PagingRequest
    {
        $pagingRequest->addOrder(static::MAIN_MODEL . '.latest_post_created');
        $pagingRequest->addOrder(static::MAIN_MODEL . '.id');

        return $pagingRequest;
    }

    private function createCondition(PagingRequest $pagingRequest): array
    {
        $userId = $pagingRequest->getCurrentUserId();
        $teamId = $pagingRequest->getCurrentTeamId();

        /** @var CircleMember $CircleMember */
        $CircleMember = ClassRegistry::init('CircleMember');

        $db = $CircleMember->getDataSource();

        $subQuery = $db->buildStatement([
            'conditions' => [
                'CircleMember.user_id' => $userId,
                'CircleMember.team_id' => $teamId,
                'CircleMember.del_flg' => false,
                'CircleMember.get_notification_flg' => true
            ],
            'fields'     => [
                'CircleMember.circle_id'
            ],
            'table'      => 'circle_members',
            'alias'      => 'CircleMember'
        ], $CircleMember);
        $subQuery = 'Circle.id IN (' . $subQuery . ') ';
        $subQueryExpression = $db->expression($subQuery);

        return ['conditions' => [$subQueryExpression]];
    }
}
