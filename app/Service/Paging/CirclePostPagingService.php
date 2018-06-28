<?php
App::import('Lib/Paging', 'BasePagingService');
App::uses('PagingCursor', 'Lib/Paging');
App::uses('Post', 'Model');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/06/20
 * Time: 9:39
 */
class CirclePostPagingService extends BasePagingService
{
    const EXTEND_ALL = "ext:circle_post:all";
    const EXTEND_USER = "ext:circle_post:user";
    const EXTEND_CIRCLE = "ext:circle_post:circle";
    const EXTEND_COMMENT = "ext:circle_post:comment";
    const EXTEND_POST_SHARE_CIRCLE = "ext:circle_post:share_circle";
    const EXTEND_POST_SHARE_USER = "ext:circle_post:share_user";
    const EXTEND_POST_FILE = "ext:circle_post:file";

    const DEFAULT_COMMENT_COUNT = 3;

    protected function readData(PagingCursor $pagingCursor, int $limit): array
    {
        $options = $this->createSearchCondition($pagingCursor->getConditions(true));

        $options['limit'] = $limit;
        $options['order'] = $pagingCursor->getOrders();
        $options['conditions']['AND'][] = $pagingCursor->getPointersAsQueryOption();
        $options['conversion'] = true;

        /** @var Post $Post */
        $Post = ClassRegistry::init('Post');

        $result = $Post->find('all', $options);

        //Remove 'Post' from array
        return Hash::extract($result, '{n}.Post');
    }

    protected function countData(array $conditions): int
    {
        $options = $this->createSearchCondition($conditions);

        /** @var Post $Post */
        $Post = ClassRegistry::init('Post');

        return (int)$Post->find('count', $options);
    }

    protected function extendPagingResult(&$resultArray, $conditions, $options = [])
    {
        //TODO Will implement in GL-7028
    }

    /**
     * Create the SQL query for getting the circle posts
     *
     * @param array $conditions
     *
     * @return array
     */
    private function createSearchCondition(array $conditions): array
    {
        $circleId = Hash::get($conditions, 'circle_id');

        if (empty($circleId)) {
            GoalousLog::error("Missing circle ID for post paging", $conditions);
            throw new RuntimeException("Missing circle ID");
        }

        $options = [
            'conditions' => [
                'Post.del_flg' => false,
                'Post.type'    => [Post::TYPE_NORMAL, Post::TYPE_CREATE_CIRCLE]
            ],
            'join'       => [
                [
                    'type'       => 'inner',
                    'table'      => 'post_share_circles',
                    'alias'      => 'PostShareCircle',
                    'conditions' => [
                        'PostShareCircle = Post.id',
                        'PostShareCircle.del_flg'   => false,
                        'PostShareCircle.circle_id' => $circleId
                    ]
                ]
            ]
        ];

        return $options;
    }

    protected function getEndPointerValue($lastElement)
    {
        return ['id', "<", $lastElement['id']];
    }

    protected function getStartPointerValue($firstElement)
    {
        return ['id', ">", $firstElement['id']];
    }

}