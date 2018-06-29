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
                'id',
                'post_id'
            ]
        ];

        $result = $SavedPost->find('all', $options);

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
                    $parentElement['is_saved'] = true;
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $parentElement['is_saved'] = false;
            }
        }

        return $parentData;
    }

}