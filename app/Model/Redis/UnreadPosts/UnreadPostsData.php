<?php

class UnreadPostsData
{
    private $data = [];

    public function __construct(array $data = [])
    {
        if (!empty($data)) {
            $this->data = $data;
        }
    }

    /**
     * Add unread post id in circle
     *
     * @param int $circleId
     * @param int $postId
     */
    public function add(int $circleId, int $postId)
    {
        $this->addMany($circleId, [$postId]);
    }

    /**
     * Add multiple unread post ids in a circle
     *
     * @param int   $circleId
     * @param array $postIds
     */
    public function addMany(int $circleId, array $postIds)
    {
        if (empty($this->data[$circleId])) {
            $this->data[$circleId] = $postIds;
            return;
        }
        $this->data[$circleId] = array_merge($this->data[$circleId], $postIds);
    }

    /**
     * Remove entry for an entire circle
     *
     * @param int $circleId
     */
    public function removeByCircleId(int $circleId)
    {
        if (!empty($this->data[$circleId])) {
            unset($this->data[$circleId]);
        }
    }

    /**
     * Remove entry by post id
     *
     * @param array $postIds
     */
    public function removeByPostIds(array $postIds)
    {
        foreach ($this->data as $circleId => $postIdArray) {
            $remainingPostIds = array_diff($postIdArray, $postIds);
            if (empty($remainingPostIds)) {
                $this->removeByCircleId($circleId);
                continue;
            }
            $this->data[$circleId] = $remainingPostIds;
        }
    }

    /**
     * Set data
     *
     * @param array $newData
     */
    public function set(array $newData)
    {
        $this->data = $newData;
    }

    /**
     * Get data
     *
     * @param bool $castContentToStringFlg Cast data content to string
     *
     * @return array
     */
    public function get(bool $castContentToStringFlg = false): array
    {
        if (empty($this->data)) return [];
        if (!$castContentToStringFlg) {
            return $this->data;
        }

        $keys = array_keys($this->data);
        $result = [];
        foreach ($keys as $circleId) {
            foreach ($this->data[$circleId] as $postId) {
                $result[strval($circleId)][] = strval($postId);
            }
        }

        return $result;
    }
}