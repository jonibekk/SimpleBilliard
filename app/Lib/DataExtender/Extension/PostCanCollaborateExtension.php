<?php
App::uses("PostRead", "Model");
App::import('Policy', 'PostPolicy');
App::import('Lib/DataExtender/Extension', 'DataExtension');

class PostCanCollaborateExtension extends DataExtension
{
    /** @var int */
    private $userId;

    /** @var int */
    private $teamId;

    /**
     * Set user ID for the extender function
     *
     * @param int $userId
     */
    public function setUserId(int $userId)
    {
        $this->userId = $userId;
    }

    public function setTeamId(int $teamId)
    {
        $this->teamId = $teamId;
    }

    protected function fetchData(array $keys): array
    {
        if (empty($this->userId)) {
            throw new RuntimeException("Missing user ID");
        }

        if (empty($this->teamId)) {
            throw new RuntimeException("Missing team ID");
        }

        $postIds = $this->filterKeys($keys);
        $options = [
            "conditions" => [
                "Post.id" => $postIds
            ]
        ];

        $policy = new PostPolicy($this->userId, $this->teamId);
        $scope = $policy->scope("collaborate");

        /** @var Post $Post */
        $Post = ClassRegistry::init('Post');
        $results = $Post->find('all', array_merge_recursive($options, $scope));

        return Hash::extract($results, "{n}.Post.id");
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
                $parentData['can_collaborate'] = in_array(Hash::get($parentData, $parentKeyName), $extData);
                return $parentData;
            }
            $parentElement['can_collaborate'] = in_array(Hash::get($parentElement, $parentKeyName), $extData);
        }
        return $parentData;
    }

}
