<?php
App::uses("PostRead", "Model");
App::import('Lib/DataExtender/Extension', 'DataExtension');

class PostReadExtension extends DataExtension
{
    /** @var int */
    private $userId;

    /**
     * Set user ID for the extender function
     *
     * @param int $userId
     */
    public function setUserId(int $userId)
    {
        $this->userId = $userId;
    }

    protected function fetchData(array $keys): array
    {
        if (empty($this->userId)) {
            throw new RuntimeException("Missing user ID");
        }

        $postIds = $this->filterKeys($keys);

        /** @var Post $Post */
        $Post = ClassRegistry::init('Post');

        /* Deal as post is read if post creator is logged in user */
        $options = [
            'conditions' => [
                'id'      => $postIds,
                'user_id' => $this->userId,
            ],
            'fields'     => [
                'id'
            ],
        ];
        $posts = $Post->find('all', $options);
        $createdPostIds = Hash::extract($posts, '{n}.Post.id');
        $notCreatedPostIds = array_diff($postIds, $createdPostIds);
        // All posts are created by logged in user, finish processing
        if (empty($notCreatedPostIds)) {
            return $createdPostIds;
        }

        /** @var PostRead $PostRead */
        $PostRead = ClassRegistry::init('PostRead');

        /* Get whether read posts created by other user. */
        $options = [
            'conditions' => [
                'post_id' => $notCreatedPostIds,
                'user_id' => $this->userId,
            ],
            'fields'     => [
                'post_id'
            ],
        ];
        $postReads = $PostRead->find('all', $options);
        $readPostIds = Hash::extract($postReads, "{n}.{s}.post_id");
        return array_merge($createdPostIds, $readPostIds);
    }

    protected function connectData(
        array $parentData,
        string $parentKeyName,
        array $extData,
        string $extDataKey,
        string $extEntryKey = ""
    ): array {
        foreach ($parentData as $key => &$parentElement) {
            if (!is_int($key)) {
                $parentData['is_read'] = in_array(Hash::get($parentData, $parentKeyName), $extData);
                return $parentData;
            }
            $parentElement['is_read'] = in_array(Hash::get($parentElement, $parentKeyName), $extData);
        }
        return $parentData;
    }

}
