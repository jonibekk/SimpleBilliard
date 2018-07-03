<?php
App::uses("SavedPost", "Model");
App::import('Lib/DataExtender', 'DataExtender');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/06/29
 * Time: 10:39
 */
class PostSavedDataExtender extends DataExtender
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

        /** @var SavedPost $SavedPost */
        $SavedPost = ClassRegistry::init('SavedPost');

        $options = [
            'conditions' => [
                'post_id' => $filteredKeys,
                'user_id' => $this->userId
            ],
            'fields'     => [
                'post_id'
            ]
        ];

        $result = $SavedPost->find('all', $options);

        return Hash::extract($result, "{n}.{s}.post_id");;
    }

    protected function connectData(
        array $parentData,
        string $parentKeyName,
        array $extData,
        string $extDataKey
    ): array {
        foreach ($parentData as $key => &$parentElement) {
            if (!is_int($key)) {
                $parentData['is_saved'] = in_array(Hash::get($parentData, $parentKeyName), $extData);
                return $parentData;
            }
            $parentElement['is_saved'] = in_array(Hash::get($parentElement, $parentKeyName), $extData);
        }
        return $parentData;
    }

}