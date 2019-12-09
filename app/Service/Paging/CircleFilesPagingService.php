<?php

App::import('Lib/Paging', 'BasePagingService');
App::uses('PagingRequest', 'Lib/Paging');
App::uses('SearchPostFile', 'Model');
App::import('Lib/DataExtender', 'SearchPostFileExtender');
App::import('Service', 'SearchPostFileService');

class CircleFilesPagingService extends BasePagingService
{
    const MAIN_MODEL = 'SearchPostFile';

    const FILTER_DOCUMENTS = 1;
    const FILTER_IMAGES = 2;
    const FILTER_VIDEOS = 3;

    private $filterType = self::FILTER_DOCUMENTS;

    public function setFilter( int $filterType ) {
        $this->filterType = $filterType;
    }

    protected function readData(PagingRequest $pagingRequest, int $limit): array
    {
        $options = $this->createQueryCondition($pagingRequest);

        $options['limit'] = $limit;
        $options['order'] = $this->createOrder( $pagingRequest );
        $options['conditions'][] = $pagingRequest->getPointersAsQueryOption();

        /** @var SearchPostFile $SearchPostFile */
        $SearchPostFile = ClassRegistry::init('SearchPostFile');

        $result = $SearchPostFile->useType()->find('all', $options);

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

        $filterAttachedFile = [AttachedFile::TYPE_FILE_DOC];
        if( $this->filterType === self::FILTER_VIDEOS ) {
            $filterAttachedFile = [AttachedFile::TYPE_FILE_VIDEO];
        } else if( $this->filterType === self::FILTER_IMAGES ) {
            $filterAttachedFile = [AttachedFile::TYPE_FILE_IMG];
        } else {
            //keep default
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
                        'AttachedFile.file_type' => $filterAttachedFile,
                    ]
                ],
                [
                    'type'       => 'LEFT',
                    'table'      => 'video_streams',
                    'alias'      => 'VideoStream',
                    'conditions' => [
                        'VideoStream.id = SearchPostFile.video_stream_id',
                ]
                ],
            ]

        ];

        return $options;
    }

    protected function createPointer(
        array $lastElement,
        array $headNextElement = [],
        PagingRequest $pagingRequest = null
    ): PointerTree {
        //Query params for: ORDER BY post_id desc, id desc
        $first = new PointerTree([SearchPostFile::class . '.post_id', "<", $lastElement['post_id']]);
        $secondPart1 = new PointerTree([SearchPostFile::class . '.post_id', "=", $lastElement['post_id']]);
        $secondPart2 = new PointerTree([SearchPostFile::class . '.id', "<", $lastElement['id']]);
        $second = new PointerTree('AND', $secondPart1, $secondPart2);
        return new PointerTree('OR', $first, $second);
    }

    private function createOrder( PagingRequest $pagingRequest = null ) : array {
        return [
            'SearchPostFile.post_id' => 'desc',
            'SearchPostFile.id' => 'desc',
        ];
        //the order is always the same. not $pagingRequest->getOrders();
    }
}
