<?php
App::uses("CircleMember", "Model");
App::import('Lib/DataExtender/Extension', 'DataExtension');

class CircleMemberInfoExtension extends DataExtension
{
    /**
     * @var int
     */
    private $userId;

    protected function fetchData(array $keys): array
    {
        /** @var CircleMember $CircleMember */
        $CircleMember = ClassRegistry::init('CircleMember');

        if (empty($this->userId)) {
            GoalousLog::error("Missing user ID for circle member info data extension");
            throw new InvalidArgumentException("Missing user ID for circle member info data extension");
        }

        $uniqueKeys = $this->filterKeys($keys);

        $options = [
            'conditions' => [
                'circle_id' => $uniqueKeys,
                'user_id'   => $this->userId
            ],
            'fields'     => [
                'circle_id',
                'unread_count',
                'admin_flg',
                'get_notification_flg'
            ],
        ];
        $result = $CircleMember->useType()->find('all', $options);

        if (count($result) != count($uniqueKeys)) {
            GoalousLog::info("Missing data for circle member extension. For user $this->userId, Circle ID: " . implode(',',
                    array_diff($uniqueKeys, Hash::extract($result, '{n}.{s}.circle_id'))));
        }

        return $result;
    }

    protected function connectData(
        array $parentData,
        string $parentKeyName,
        array $extData,
        string $extDataKey,
        string $extEntryKey = ""
    ): array {

        foreach ($parentData as $key => &$parentElement) {
            foreach ($extData as $extElement) {
                if (!is_int($key)) {
                    if (Hash::get($parentData, $parentKeyName) ==
                        Hash::extract($extElement, "{s}." . $extDataKey)[0]) {
                        $parentData = array_merge($parentData, Hash::extract($extElement, "{s}")[0]);
                        return $parentData;
                    }
                }
                if (Hash::get($parentElement, $parentKeyName) ==
                    Hash::extract($extElement, "{s}." . $extDataKey)[0]) {
                    $parentElement = array_merge($parentElement, Hash::extract($extElement, "{s}")[0]);
                }
            }
        }
        return $parentData;
    }

    /**
     * Set the user ID required
     *
     * @param int $userId
     */
    public function setUserId(int $userId)
    {
        $this->userId = $userId;
    }

}
