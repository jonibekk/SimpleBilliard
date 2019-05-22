<?php
App::import('Service/Api', 'ApiService');
App::import('Service', 'SavedPostService');
App::uses('SavedPost', 'Model');
App::uses('PostResource', 'Model');

/**
 * Class ApiSavedPostService
 */
class ApiSavedPostService extends ApiService
{
    const SAVED_POST_DEFAULT_LIMIT = 20;

    /**
     * Finding saved items. It will returns data as API response
     *
     * @param int      $teamId
     * @param int      $loginUserId
     * @param array    $conditions
     * @param int|null $cursor
     * @param int|null $limit
     *
     * @return array
     */
    function search(
        int $teamId,
        int $loginUserId,
        array $conditions = [],
        $cursor = null,
        $limit = null
    ): array {
        /** @var SavedPostService $SavedPostService */
        $SavedPostService = ClassRegistry::init('SavedPostService');

        // if no limit then it to be default limit
        if (!$limit) {
            $limit = self::SAVED_POST_DEFAULT_LIMIT;
        }

        // it's default that will be returned
        $res = [
            'data'   => [],
            'paging' => ['next' => null],
        ];

        // Get saved items
        // The reason why get $limit + 1 is to check whether next paging data exists
        $savedItems = $SavedPostService->search($teamId, $loginUserId, $conditions, $cursor, $limit + 1);
        // Format array structure for api response
        $res['data'] = $this->convertResponseForApi($savedItems);
        // Set paging data
        $res = $this->setPaging($res, $limit, $conditions);
        // Extend data (photo, user, etc)
        $res['data'] = $this->extend($res['data'], $teamId);

        return $res;
    }

    /**
     * Format array structure for api response
     *
     * @param array $data
     *
     * @return array
     */
    function convertResponseForApi(array $data): array
    {
        $res = [];
        foreach ($data as $v) {
            $item = $v['SavedPost'];
            $item['post_user_id'] = Hash::get($v, 'Post.user_id');
            $item['type'] = Hash::get($v, 'Post.type');
            $item['post_created'] = Hash::get($v, 'Post.created');
            if (Hash::get($v, 'Post.type') == Post::TYPE_ACTION) {
                $item['body'] = Hash::get($v, 'ActionResult.name');
                $item['action_result_id'] = Hash::get($v, 'ActionResult.id');
                $item['action_photo_file_name'] = Hash::get($v, 'ActionResult.photo1_file_name');
            } else {
                $item['body'] = Hash::get($v, 'Post.body');
                $item['site_info'] = Hash::get($v, 'Post.site_info');
                $item['site_photo_file_name'] = Hash::get($v, 'Post.site_photo_file_name');
            }
            $res[] = $item;
        }
        return $res;
    }

    /**
     * Extend saved posts
     * Image priority with saved item which displaying list page
     * 1. Attached image with post
     * 2. OGP image with post
     * 3. Post creator's profile image
     *
     * @param array $items
     * @param int   $teamId
     *
     * @return array
     */
    function extend(array $items, int $teamId): array
    {
        /** @var User $User */
        $User = ClassRegistry::init('User');
        /** @var AttachedFile $AttachedFile */
        $AttachedFile = ClassRegistry::init('AttachedFile');
        App::uses('UploadHelper', 'View/Helper');
        $Upload = new UploadHelper(new View());
        App::uses('TimeExHelper', 'View/Helper');
        $TimeEx = new TimeExHelper(new View());

        /* Bulk set posted user info each post */
        // [Efficient processing]
        // This is why it is inefficient to throw SQL for each post and get user info
        $postUserIds = array_unique(
            Hash::extract($items, '{n}.post_user_id')
        );
        $postUsers = $User->findAllById($postUserIds);
        $postUsers = Hash::combine($postUsers, '{n}.User.id', '{n}.User');
        // For filtering key value as white list
        $userFiledKeys = array_flip(['id', 'photo_file_name', 'display_username']);
        foreach ($items as $i => $item) {
            $postUserId = Hash::get($item, 'post_user_id');
            $user = Hash::get($postUsers, $postUserId) ?? [];
            $items[$i]['post_user'] = array_intersect_key($user, $userFiledKeys);
        }

        /* Bulk set saved item image each post */
        $postIds = [];
        $actionIds = [];
        foreach ($items as $item) {
            if ($item['type'] == Post::TYPE_NORMAL) {
                $postIds[] = $item['post_id'];
            } else {
                $actionIds[] = $item['action_result_id'];
            }
        }

        $attachedImgEachPost = $AttachedFile->findAttachedImgEachPost($teamId, $postIds);
        $attachedImgEachPost = Hash::combine($attachedImgEachPost, '{n}.post_id', '{n}');

        /* Bulk set saved item image each post */
        $attachedImgEachAction = $AttachedFile->findAttachedImgEachAction($teamId, $actionIds);
        $attachedImgEachAction = Hash::combine($attachedImgEachAction, '{n}.action_result_id', '{n}');

        // Fetch post resource
        /** @var PostResource $PostResource */
        $PostResource = ClassRegistry::init('PostResource');
        $postResources = $PostResource->getResourcesByPostId($postIds, false);

        foreach ($items as $i => $item) {
            $imgUrl = "";
            $items[$i]['display_created'] = $TimeEx->elapsedTime($item['created'], 'normal', false);
            // Separate logic each type(Action or Post)
            if ($item['type'] == Post::TYPE_ACTION) {
                $actionId = Hash::get($item, 'action_result_id');
                $attachedImg = Hash::get($attachedImgEachAction, $actionId);
                $imgUrl = $Upload->uploadUrl($attachedImg,
                    "AttachedFile.attached",
                    ['style' => 'x_small']);
            } else {
                $postId = Hash::get($item, 'post_id');
                // Attached image with post
                $attachedImg = Hash::get($attachedImgEachPost, $postId);

                $resources = Hash::get($postResources, $postId, []);

                // check if post_resource have a video or not
                // TODO: https://jira.goalous.com/browse/GL-6601
                $hasVideo = (0 < count($resources));

                if ($hasVideo) {
                    $imgUrl = '/img/no-image-video.jpg';
                } else {
                    if (!empty($attachedImg)) {
                        $imgUrl = $Upload->uploadUrl($attachedImg,
                            "AttachedFile.attached",
                            ['style' => 'x_small']);
                        // OGP image with post
                    } elseif (!empty(Hash::get($item, 'site_photo_file_name'))) {
                        $postForGetImg = [
                            'id'                   => $item['post_id'],
                            'site_photo_file_name' => $item['site_photo_file_name']
                        ];
                        $imgUrl = $Upload->uploadUrl($postForGetImg,
                            "Post.site_photo",
                            ['style' => 'small']);
                        // Post creator's profile image
                    }
                }
            }
            if (empty($imgUrl)) {
                $user = Hash::get($item, 'post_user');
                $imgUrl = $Upload->uploadUrl($user,
                    "User.photo",
                    ['style' => 'medium']);

            }
            $items[$i]['image_url'] = $imgUrl;
        }
        return $items;
    }

    /**
     * Setting paging Information
     * - $data includes extra record that will be removed.
     *
     * @param array $data
     * @param int   $limit
     * @param array $conditions
     *
     * @return array
     */
    private function setPaging(array $data, int $limit, array $conditions): array
    {
        // If next page is not exists, return
        if (count($data['data']) < $limit + 1) {
            return $data;
        }
        // exclude that extra record for paging
        array_pop($data['data']);
        $cursor = end($data['data'])['id'];
        $queryParams = am(
            $conditions,
            [
                'cursor' => $cursor,
                'limit'  => $limit
            ]
        );

        $data['paging']['next'] = "/api/v1/saved_items?" . http_build_query($queryParams);
        return $data;
    }

}
