<?php
App::uses("PostLike", "Model");
App::import('Lib/DataExtender', 'DataExtender');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/06/29
 * Time: 10:31
 */
class PostLikeDataExtender extends DataExtender
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

        /** @var PostLike $PostLike */
        $PostLike = ClassRegistry::init('PostLike');

        $likeOptions = [
            'conditions' => [
                'post_id' => $filteredKeys,
                'user_id' => $this->userId
            ],
            'fields'     => [
                'id',
                'post_id'
            ],

        ];

        $result = $PostLike->find('all', $likeOptions);

        return $result;
    }

    protected function connectData(
        array $parentData,
        string $parentKeyName,
        array $extData,
        string $extDataKey
    ): array {
        foreach ($parentData as &$parentElement) {
            /** @var bool $found */
            $found = false;
            foreach ($extData as $extension) {
                //Since extension data will have its own Model name as key, we use extract
                //E.g. ['User'][...]
                if (Hash::get($parentElement, $parentKeyName) ==
                    Hash::extract($extension, "{s}." . $extDataKey)[0]) {
                    $parentElement['is_liked'] = true;
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $parentElement['is_liked'] = false;
            }
        }

        return $parentData;
    }

}