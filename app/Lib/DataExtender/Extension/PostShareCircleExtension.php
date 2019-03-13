<?php
App::uses("PostShareCircle", "Model");
App::uses("Circle", "Model");
App::import('Service', 'CircleService');
App::import('Lib/DataExtender/Extension', 'DataExtension');
App::import('Service/Request/Resource', 'CircleResourceRequest');

class PostShareCircleExtension extends DataExtension
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

    /**
     * Set team ID for the extender function
     *
     * @param int $teamId
     */
    public function setTeamId(int $teamId)
    {
        $this->teamId = $teamId;
    }

    protected function fetchData(array $keys): array
    {
        if (empty($this->userId)) {
            throw new RuntimeException("Missing user ID");
        }

        $filteredKeys = $this->filterKeys($keys);

        /** @var PostShareCircle $PostShareCircle */
        $PostShareCircle = ClassRegistry::init('PostShareCircle');
        /** @var CircleService $CircleService */
        $CircleService = ClassRegistry::init('CircleService');

        $options = [
            'conditions' => [
                'post_id' => $filteredKeys,
            ],
            'fields'     => [
                'circle_id'
            ]
        ];

        $resultPostShareCircle = $PostShareCircle->find('all', $options);
        $sharedCircleIds = Hash::extract($resultPostShareCircle, '{n}.PostShareCircle.circle_id');

        $sharedCircles = array_map(function($circleId) use ($CircleService) {
            $circleRequestResource = new CircleResourceRequest($circleId, $this->userId, $this->teamId);
            return $CircleService->get($circleRequestResource);
        }, $sharedCircleIds, []);

        return $sharedCircles;
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
                $parentData['shared_circles'] = $extData;
                return $parentData;
            }
            $parentElement['shared_circles'] = $extData;
        }
        return $parentData;
    }

}
