<?php

App::import('Lib/Paging', 'BasePagingService');
App::uses('PagingRequest', 'Lib/Paging');
//App::uses('PostResource', 'Model');
//App::uses('PostShareCircle', 'Model');
//App::uses('PostFile', 'Model');
//App::uses('AttachedFile', 'Model');
App::uses('SearchPostFile', 'Model');
//App::uses('Post', 'Model');
App::import('Lib/DataExtender', 'SearchPostFileExtender');
App::import('Service', 'SearchPostFileService');

//use Goalous\Enum\Model\Post\PostResourceType;

class CircleFilesPagingService extends BasePagingService
{
    const MAIN_MODEL = 'SearchPostFile';

    protected function readData(PagingRequest $pagingRequest, int $limit): array
    {
        $options = $this->createQueryCondition($pagingRequest);

        $options['limit'] = $limit;
        $options['order'] = $pagingRequest->getOrders();
        $options['conditions'][] = $pagingRequest->getPointersAsQueryOption();

        /** @var SearchPostFile $SearchPostFile */
        $SearchPostFile = ClassRegistry::init('SearchPostFile');

        $result = $SearchPostFile->useType()->find('all', $options);

        GoalousLog::error('\n\n\n[JGJGJG]\n(((\n'.print_r($result,true).'\n)))END');

        return Hash::extract($result, '{n}.SearchPostFile');
    }

    protected function countData(PagingRequest $request): int
    {
        return -1;
    }

    protected function extendPagingResult(array &$data, PagingRequest $request, array $options = [])
    {
        $userId = $request->getCurrentUserId();
        $teamId = $request->getCurrentTeamId();

        /** @var SearchPostFileExtender $SearchPostFileExtender */
        $SearchPostFileExtender = ClassRegistry::init('SearchPostFileExtender');
        $data = $SearchPostFileExtender->extendMulti($data, $userId, $teamId, $options);
    }

    /**
     * Create the SQL query for getting the circle posts
     *
     * @param PagingRequest $request
     *
     * @return array
     */
    private function createQueryCondition(PagingRequest $request): array
    {
        $conditions = $request->getConditions(true);

        $circleId = $request->getResourceId();
        $teamId = $request->getCurrentTeamId();

        if (empty($circleId)) {
            GoalousLog::error("Missing circle ID for post paging", $conditions);
            throw new InvalidArgumentException("Missing circle ID");
        }

        $options = [
            'conditions' => [
                'SearchPostFile.team_id' => $teamId,
                'SearchPostFile.circle_id' => $circleId,
                'OR' => [
                    'AttachedFile.del_flg' => false,
                    'VideoStream.del_flg' => false,
                ],
            ],
            'joins'      => [
                [
                    'type'       => 'LEFT',
                    'table'      => 'attached_files',
                    'alias'      => 'AttachedFile',
                    'conditions' => [
                        'AttachedFile.id = SearchPostFile.attached_file_id',
                        //'AttachedFile.del_flg' => false,
                    ]
                ],
                [
                    'type'       => 'LEFT',
                    'table'      => 'video_streams',
                    'alias'      => 'VideoStream',
                    'conditions' => [
                        'VideoStream.id = SearchPostFile.video_stream_id',
                        //'VideoStream.del_flg' => false,
                ]
                ],
            ],
            'order' => [
                'SearchPostFile.post_id' => 'desc'
            ]

        ];

        return $options;
    }

    protected function createPointer(
        array $lastElement,
        array $headNextElement = [],
        PagingRequest $pagingRequest = null
    ): PointerTree {
        return new PointerTree([SearchPostFile::class . '.id', "<", $lastElement['id']]);
    }
}
