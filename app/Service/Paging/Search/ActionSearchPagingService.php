<?php
App::uses('AttachedFile', 'Model');
App::uses('Comment', 'Model');
App::uses('Post', 'Model');
App::uses('PostResource', 'Model');
App::uses('SavedPost', 'Model');
App::uses('UploadHelper', 'View/Helper');
App::import('Lib/DataExtender/Extension', 'UserExtension');
App::import('Lib/ElasticSearch', "ESClient");
App::import('Lib/ElasticSearch', "ESSearchResponse");
App::import('Model/Entity', 'PostEntity');
App::import('Service/Paging/Search', 'BaseSearchPagingService');
App::uses('TimeExHelper', 'View/Helper');

/**
 * Created by PhpStorm.
 * User: Stephen Raharja
 * Date: 11/21/2018
 * Time: 2:43 PM
 */
class ActionSearchPagingService extends BaseSearchPagingService
{
    const ES_SEARCH_PARAM_MODEL = 'action';

    protected function setCondition(ESPagingRequest $pagingRequest): ESPagingRequest
    {
        $pagingRequest->addQueryToCondition('keyword', false);
        $pagingRequest->addQueryToCondition('limit', false, self::DEFAULT_PAGE_LIMIT);
        $pagingRequest->addQueryToCondition("file_name", false, 0);

        return $pagingRequest;
    }

    protected function fetchData(ESPagingRequest $pagingRequest): ESSearchResponse
    {
        $ESClient = new ESClient();

        $query = $pagingRequest->getCondition('keyword');

        $teamId = $pagingRequest->getTempCondition('team_id');

        $params[static::ES_SEARCH_PARAM_MODEL] = [
            'pn'        => intval($pagingRequest->getCondition('pn')),
            'rn'        => intval($pagingRequest->getCondition('limit')),
            'file_name' => intval($pagingRequest->getCondition('file_name')),
        ];

        return $ESClient->search($query, $teamId, $params);
    }

    protected function extendData(array $baseData, ESPagingRequest $request): array
    {
        $postIds = Hash::extract($baseData, '{n}.id');
        $commentIds = Hash::extract($baseData, '{n}.comment_id');

        if (empty($postIds)) {
            return $baseData;
        }
        //Extend post data
        $postData = $this->bulkFetchPost($postIds);
        $postData = $this->bulkExtendPost($postData, $request);
        foreach ($baseData as &$data) {
            foreach ($postData as $post) {
                if ($data['id'] == $post['id']) {
                    $data['post'] = $post;
                    break;
                }
            }
        }
        //Extend comment data
        $commentData = $this->bulkFetchComment($commentIds);
        $commentData = $this->bulkExtendComment($commentData, $request);
        foreach ($baseData as &$data) {
            if (!empty($data['comment_id'])) {
                foreach ($commentData as $comment) {
                    if ($data['comment_id'] == $comment['id']) {
                        $data['comment'] = $comment;
                        break;
                    }
                }
            }
        }

        //Extend IMG
        $baseData = $this->extendImage($baseData, $request);

        //Extend display created
        $TimeEx = new TimeExHelper(new View());
        foreach ($baseData as &$data) {
            if (empty($data['comment_id'])) {
                $data['display_created'] = $TimeEx->elapsedTime($data['post']['created'], 'rough', false);
            } else {
                $data['display_created'] = $TimeEx->elapsedTime($data['comment']['created'], 'rough', false);
            }
        }

        return $baseData;
    }

    /**
     * Load multiple posts at once
     *
     * @param array $postIds
     *
     * @return array
     */
    private function bulkFetchPost(array $postIds): array
    {
        $condition = [
            'conditions' => [
                'Post.id'      => $postIds,
                'Post.del_flg' => false
            ]
        ];

        /** @var Post $Post */
        $Post = ClassRegistry::init('Post');

        return Hash::extract($Post->find('all', $condition), '{n}.Post');
    }

    /**
     * Load multiple posts at once
     *
     * @param array $commentIds
     *
     * @return array
     */
    private function bulkFetchComment(array $commentIds): array
    {
        $condition = [
            'conditions' => [
                'Comment.id'      => $commentIds,
                'Comment.del_flg' => false
            ]
        ];

        /** @var Comment $Comment */
        $Comment = ClassRegistry::init('Comment');

        return Hash::extract($Comment->find('all', $condition), '{n}.Comment');
    }

    /**
     * Extend multiple posts.
     * As of 2018/11/20, this code is copy & paste from extension method in CirclePostPagingService to save time
     *
     * @param array           $rawData
     * @param ESPagingRequest $request
     *
     * @return array
     */
    private function bulkExtendPost(array $rawData, ESPagingRequest $request): array
    {
        /** @var UserExtension $UserExtension */
        $UserExtension = ClassRegistry::init('UserExtension');
        $resultArray = $UserExtension->extendMulti($rawData, "{n}.user_id");

        return $resultArray;
    }

    /**
     * Extend multiple posts.
     * As of 2018/11/20, this code is copy & paste from extension method
     *
     * @param array           $rawData
     * @param ESPagingRequest $request
     *
     * @return array
     */
    private function bulkExtendComment(array $rawData, ESPagingRequest $request): array
    {
        /** @var UserExtension $UserExtension */
        $UserExtension = ClassRegistry::init('UserExtension');
        $resultArray = $UserExtension->extendMulti($rawData, "{n}.user_id");

        return $resultArray;
    }

    /**
     * Add display image for each search result.
     * Order: video pic > first post image > OGP image > user profile image
     *
     * @param array           $rawData
     * @param ESPagingRequest $request
     *
     * @return array
     */
    private function extendImage(array $rawData, ESPagingRequest $request): array
    {
        $teamId = $request->getTempCondition("team_id");

        $actionIds = Hash::extract($rawData, '{n}.post.action_result_id');
        $commentIds = Hash::extract($rawData, '{n}.comment_id');

        /** @var AttachedFile $AttachedFile */
        $AttachedFile = ClassRegistry::init('AttachedFile');
        $Upload = new UploadHelper(new View());

        /* Bulk set saved item image each post */
        $attachedImgEachAction = $AttachedFile->findAttachedImgEachAction($teamId, $actionIds);
        $attachedImgEachAction = Hash::combine($attachedImgEachAction, '{n}.action_result_id', '{n}');

        $attachedImgEachComment = $AttachedFile->findAttachedImgEachComment($teamId, $commentIds);
        $attachedImgEachComment = Hash::combine($attachedImgEachComment, '{n}.comment_id', '{n}');

        foreach ($rawData as &$item) {
            if (!empty($item['comment_id'])) {
                //If result is comment, use comment's images
                $commentId = Hash::get($item, 'comment_id');
                // Attached image with post
                $attachedImg = Hash::get($attachedImgEachComment, $commentId);

                if (!empty($attachedImg)) {
                    $imgUrl = $Upload->uploadUrl($attachedImg,
                        "AttachedFile.attached",
                        ['style' => 'x_small']);
                }
                // Comment creator's profile image
                if (empty($imgUrl)) {
                    $imgUrl = $item['comment']['user']['profile_img_url']['medium'];
                }
            } else {
                $actionId = Hash::get($item, 'post.action_result_id');
                $attachedImg = Hash::get($attachedImgEachAction, $actionId);
                $imgUrl = $Upload->uploadUrl($attachedImg,
                    "AttachedFile.attached",
                    ['style' => 'x_small']);

                // Post creator's profile image
                if (empty($imgUrl)) {
                    $imgUrl = $item['post']['user']['profile_img_url']['medium'];
                }
            }

            $item['img_url'] = $imgUrl;
        }

        return $rawData;
    }
}
