<?php
App::import('Lib/Paging', 'BasePagingService');
App::import('Lib/Paging', 'PagingRequest');
App::import('Lib/DataExtender', 'PostDraftExtender');
App::uses('PostDraft', 'Model');

/**
 * Created by PhpStorm.
 * User: stephen
 * Date: 19/01/23
 * Time: 23:15
 */
class PostDraftPagingService extends BasePagingService
{
    const MAIN_MODEL = 'PostDraft';
    protected function readData(PagingRequest $pagingRequest, int $limit): array
    {
        $options = $this->createSearchCondition($pagingRequest);

        $options['limit'] = $limit;
        $options['order'] = $pagingRequest->getOrders();
        $options['conditions'][] = $pagingRequest->getPointersAsQueryOption();

        /** @var PostDraft $PostDraft */
        $PostDraft = ClassRegistry::init('PostDraft');

        $result = $PostDraft->useType()->find('all', $options);

        //Remove 'PostDraft' from array
        return Hash::extract($result, '{n}.PostDraft');
    }

    protected function countData(PagingRequest $pagingRequest): int
    {
        $condition = $this->createSearchCondition($pagingRequest);

        /** @var PostDraft $PostDraft */
        $PostDraft = ClassRegistry::init('PostDraft');

        return (int)$PostDraft->find('count', $condition);
    }

    protected function extendPagingResult(array &$data, PagingRequest $request, array $options = [])
    {
        $userId = $request->getCurrentUserId();
        $teamId = $request->getCurrentTeamId();

        /** @var PostDraftExtender $PostDraftExtender */
        $PostDraftExtender = ClassRegistry::init('PostDraftExtender');

        $data = $PostDraftExtender->extendMulti($data, $userId, $teamId, $options);

        return $data;
    }

    /**
     * Create query conditions
     *
     * @param PagingRequest $pagingRequest
     *
     * @return array
     */
    private function createSearchCondition(PagingRequest $pagingRequest): array
    {
        $conditions = $pagingRequest->getConditions(true);

        $circleId = $pagingRequest->getResourceId();
        $userId = $pagingRequest->getCurrentUserId();

        if (empty($circleId)) {
            GoalousLog::error("Missing circle ID for post paging", $conditions);
            throw new InvalidArgumentException("Missing circle ID");
        }

        $condition = [
            'conditions' => [
                'PostDraft.user_id' => $userId,
                'PostDraft.del_flg' => false,
            ],
            'alias'      => 'PostDraft',
            'table'      => 'post_drafts',
            'joins'      => [
                [
                    'type'       => 'INNER',
                    'alias'      => 'PostShareCircle',
                    'table'      => 'post_share_circles',
                    'conditions' => [
                        'PostShareCircle.circle_id' => $circleId,
                        'PostShareCircle.post_id = PostDraft.post_id',
                        'PostShareCircle.del_flg'   => false
                    ]
                ]
            ]
        ];

        return $condition;
    }

    protected function afterRead(array $queryResult, PagingRequest $pagingRequest): array
    {
        foreach ($queryResult as &$result) {
            $draftData = json_decode($result['draft_data'], true);
            $result['body'] = $draftData['Post']['body'];
        }

        return $queryResult;
    }


}
