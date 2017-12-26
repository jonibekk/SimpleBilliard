<?php
App::import('Service/Api', 'ApiService');
App::import('Service', 'SavedPostService');
App::uses('SavedPost', 'Model');

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
     * @param int|null $cursor
     * @param int|null $limit
     *
     * @return array
     */
    function find(
        int $teamId,
        int $loginUserId,
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
        $savedItems = $SavedPostService->findByUserId($teamId, $loginUserId, $cursor, $limit + 1);

        // Format array structure for api response
        $res['data'] = $this->convertResponseForApi($savedItems);
        // Set paging data
        $res = $this->setPaging($res, $limit);
        // Extend data (photo, user, etc)
        $res['data'] = $this->extend($res['data'], $teamId);

        return $res;
    }

    /**
     * Format array structure for api response
     * @param array $data
     *
     * @return array
     */
    function convertResponseForApi(array $data): array
    {
        $res = [];
        foreach ($data as $v) {
            $item = $v['SavedPost'];
            $item['post'] = $v['Post'];
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

        /* Bulk set posted user info each post */
        // [Efficient processing]
        // This is why it is inefficient to throw SQL for each post and get user info
        $postUserIds = array_unique(
            Hash::extract($items, '{n}.post.user_id')
        );
        $postUsers = $User->findAllById($postUserIds);
        $postUsers = Hash::combine($postUsers, '{n}.User.id', '{n}.User');
        // For filtering key value as white list
        $userFiledKeys = array_flip(['id', 'photo_file_name', 'display_username']);
        foreach ($items as $i => $item) {
            $postUserId = Hash::get($item, 'post.user_id');
            $user = Hash::get($postUsers, $postUserId) ?? [];
            $items[$i]['post']['user'] = array_intersect_key($user, $userFiledKeys);
        }

        /* Bulk set saved item image each post */
        $postIds = Hash::extract($items, '{n}.post.id');
        $attachedImgEachPost = $AttachedFile->findAttachedImgEachPost($teamId, $postIds);
        $attachedImgEachPost = Hash::combine($attachedImgEachPost, '{n}.post_id', '{n}');
        foreach ($items as $i => $item) {
            $postId = Hash::get($item, 'post.id');
            $attachedImg = Hash::get($attachedImgEachPost, $postId);
            // Attached image with post
            if (!empty($attachedImg)) {
                $postImgUrl = $Upload->uploadUrl($attachedImg,
                    "AttachedFile.attached",
                    ['style' => 'x_small']);
            // OGP image with post
            } elseif (!empty(Hash::get($item,'post.site_photo_file_name'))) {
                $postImgUrl = $Upload->uploadUrl($item['post'],
                    "Post.site_photo",
                    ['style' => 'small']);
            // Post creator's profile image
            } else {
                $user = Hash::get($item, 'post.user');
                $postImgUrl = $Upload->uploadUrl($user,
                    "User.photo",
                    ['style' => 'small']);

            }
            $items[$i]['post']['image_url'] = $postImgUrl;
        }
        return $items;
    }

    /**
     * Setting paging Information
     * - $data includes extra record that will be removed.
     *
     * @param array $data
     * @param int   $limit
     *
     * @return array
     */
    private function setPaging(array $data, int $limit): array
    {
        // If next page is not exists, return
        if (count($data['data']) < $limit + 1) {
            return $data;
        }
        // exclude that extra record for paging
        array_pop($data['data']);
        $cursor = end($data['data'])['id'];
        $queryParams = [
            'cursor' => $cursor,
            'limit'  => $limit
        ];

        $data['paging']['next'] = "/api/v1/saved_items?" . http_build_query($queryParams);
        return $data;
    }

}
