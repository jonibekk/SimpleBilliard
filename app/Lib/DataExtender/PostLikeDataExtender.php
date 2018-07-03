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
                'post_id'
            ],

        ];

        $result = $PostLike->find('all', $likeOptions);

        return Hash::extract($result, "{n}.{s}.post_id");
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