<?php
App::uses("CommentLike", "Model");
App::import('Lib/DataExtender/Extension', 'DataExtension');

class CommentLikeExtension extends DataExtension
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

        $filteredKeys = $this->filterKeys($keys);

        /** @var CommentLike $CommentLike */
        $CommentLike = ClassRegistry::init('CommentLike');

        $likeOptions = [
            'conditions' => [
                'comment_id' => $filteredKeys,
                'user_id' => $this->userId
            ],
            'fields'     => [
                'comment_id'
            ],

        ];

        $result = $CommentLike->find('all', $likeOptions);

        return Hash::extract($result, "{n}.{s}.comment_id");
    }

    protected function connectData(
        array $parentData,
        string $parentKeyName,
        array $extData,
        string $extDataKey
    ): array {
        foreach ($parentData as $key => &$parentElement) {
            if (!is_int($key)){
                $parentData['is_liked'] = in_array(Hash::get($parentData, $parentKeyName), $extData);
                return $parentData;
            }
            $parentElement['is_liked'] = in_array(Hash::get($parentElement, $parentKeyName), $extData);
        }
        return $parentData;
    }

}
