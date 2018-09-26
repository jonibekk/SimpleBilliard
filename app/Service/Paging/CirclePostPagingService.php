<?php
App::import('Lib/Paging', 'BasePagingService');
App::uses('PagingRequest', 'Lib/Paging');
App::import('Lib/DataExtender', 'UserDataExtender');
App::import('Lib/DataExtender', 'CircleDataExtender');
App::import('Lib/DataExtender', 'PostLikeDataExtender');
App::import('Lib/DataExtender', 'PostSavedDataExtender');
App::import('Lib/DataExtender', 'PostReadDataExtender');
App::import('Service/Paging', 'CommentPagingService');
App::import('Service', 'ImageStorageService');
App::import('Service', 'CircleService');
App::import('Service', 'PostService');
App::uses('PagingRequest', 'Lib/Paging');
App::uses('Comment', 'Model');
App::uses('Circle', 'Model');
App::uses('CircleMember', 'Model');
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
    const EXTEND_COMMENTS = "ext:circle_post:comments";
    const EXTEND_POST_SHARE_CIRCLE = "ext:circle_post:share_circle";
    const EXTEND_POST_SHARE_USER = "ext:circle_post:share_user";
    const EXTEND_POST_FILE = "ext:circle_post:file";
    const EXTEND_LIKE = "ext:circle_post:like";
    const EXTEND_SAVED = "ext:circle_post:saved";
    const EXTEND_READ = "ext:circle_post:read";

    const DEFAULT_COMMENT_COUNT = 3;
    const MAIN_MODEL = 'Post';

    protected function readData(PagingRequest $pagingRequest, int $limit): array
    {
        $options = $this->createSearchCondition($pagingRequest);

        $options['limit'] = $limit;
        $options['order'] = $pagingRequest->getOrders();
        $options['conditions'][] = $pagingRequest->getPointersAsQueryOption();
        $options['conversion'] = true;

        /** @var Post $Post */
        $Post = ClassRegistry::init('Post');

        $result = $Post->find('all', $options);

        //Remove 'Post' from array
        return Hash::extract($result, '{n}.Post');
    }

    protected function countData(PagingRequest $request): int
    {
        $options = $this->createSearchCondition($request);

        /** @var Post $Post */
        $Post = ClassRegistry::init('Post');

        return (int)$Post->find('count', $options);
    }

    protected function extendPagingResult(array &$resultArray, PagingRequest $request, array $options = [])
    {
        $userId = $request->getCurrentUserId();
        $teamId = $request->getCurrentTeamId();

        if ($this->includeExt($options, self::EXTEND_USER)) {
            /** @var UserDataExtender $UserDataExtender */
            $UserDataExtender = ClassRegistry::init('UserDataExtender');
            $resultArray = $UserDataExtender->extend($resultArray, "{n}.user_id");
        }
        if ($this->includeExt($options, self::EXTEND_CIRCLE)) {
            /** @var CircleDataExtender $CircleDataExtender */
            $CircleDataExtender = ClassRegistry::init('CircleDataExtender');
            $resultArray = $CircleDataExtender->extend($resultArray, "{n}.circle_id");
        }
        if ($this->includeExt($options, self::EXTEND_COMMENTS)) {
            /** @var CommentPagingService $CommentPagingService */
            $CommentPagingService = ClassRegistry::init('CommentPagingService');

            foreach ($resultArray as &$result) {
                $commentPagingRequest = new PagingRequest();
                $commentPagingRequest->setResourceId(Hash::get($result, 'id'));
                $commentPagingRequest->setCurrentUserId($userId);
                $commentPagingRequest->setCurrentTeamId($teamId);

                $comments = $CommentPagingService->getDataWithPaging($commentPagingRequest, self::DEFAULT_COMMENT_COUNT,
                    CommentPagingService::EXTEND_ALL);

                $result['comments'] = $comments;
            }
        }
        if ($this->includeExt($options, self::EXTEND_POST_FILE)) {
            // Set image url each post photo
            /** @var ImageStorageService $ImageStorageService */
            $ImageStorageService = ClassRegistry::init('ImageStorageService');

            /** @var PostService $PostService */
            $PostService = ClassRegistry::init('PostService');

            foreach ($resultArray as $index => $entry) {
                $attachedFile = $PostService->getAttachedFiles($entry['id']);

                if (empty($attachedFile)) {
                    $resultArray[$index]['attached_files'] = [];
                    continue;
                }
                /** @var AttachedFileEntity $file */
                foreach ($attachedFile as $file) {
                    if ($file['file_type'] == AttachedFile::TYPE_FILE_IMG) {
                        $file['file_url'] = $ImageStorageService->getImgUrlEachSize($file->toArray(), 'AttachedFile',
                            'attached');
                        $resultArray[$index]['attached_files'][] = $file->toArray();
                    }
                }
            }
        }
        if ($this->includeExt($options, self::EXTEND_LIKE)) {
            /** @var PostLikeDataExtender $PostLikeDataExtender */
            $PostLikeDataExtender = ClassRegistry::init('PostLikeDataExtender');
            $PostLikeDataExtender->setUserId($userId);
            $resultArray = $PostLikeDataExtender->extend($resultArray, "{n}.id", "post_id");
        }
        if ($this->includeExt($options, self::EXTEND_SAVED)) {
            /** @var PostSavedDataExtender $PostSavedDataExtender */
            $PostSavedDataExtender = ClassRegistry::init('PostSavedDataExtender');
            $PostSavedDataExtender->setUserId($userId);
            $resultArray = $PostSavedDataExtender->extend($resultArray, "{n}.id", "post_id");
        }
        if ($this->includeExt($options, self::EXTEND_READ)) {
            /** @var PostSavedDataExtender $PostSavedDataExtender */
            $PostReadDataExtender = ClassRegistry::init('PostReadDataExtender');
            $PostReadDataExtender->setUserId($userId);
            $resultArray = $PostReadDataExtender->extend($resultArray, "{n}.id", "post_id");
        }

    }

    /**
     * Create the SQL query for getting the circle posts
     *
     * @param PagingRequest $request
     *
     * @return array
     */
    private function createSearchCondition(PagingRequest $request): array
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
                'Post.del_flg' => false,
                'Post.team_id' => $teamId,
                'Post.type'    => [Post::TYPE_NORMAL, ]
            ],
            'joins'      => [
                [
                    'type'       => 'INNER',
                    'table'      => 'post_share_circles',
                    'alias'      => 'PostShareCircle',
                    'conditions' => [
                        'PostShareCircle.post_id = Post.id',
                        'PostShareCircle.del_flg'   => false,
                        'PostShareCircle.circle_id' => $circleId
                    ]
                ]
            ]
        ];

        return $options;
    }

    protected function createPointer(
        array $lastElement,
        array $headNextElement = [],
        PagingRequest $pagingRequest = null
    ): PointerTree {
        return new PointerTree([static::MAIN_MODEL . '.id', "<", $lastElement['id']]);
    }
}
