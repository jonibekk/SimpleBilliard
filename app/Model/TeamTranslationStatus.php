<?php
App::uses('AppModel', 'Model');
App::import('Model/Entity', 'TeamTranslationStatusEntity');

use Goalous\Exception as GlException;
use Goalous\Enum\DataType\DataType as DataType;

class TeamTranslationStatus extends AppModel
{
    protected $modelConversionTable = [
        'team_id'                   => DataType::INT,
        'circle_post_total'         => DataType::INT,
        'circle_post_comment_total' => DataType::INT,
        'action_post_total'         => DataType::INT,
        'action_post_comment_total' => DataType::INT,
        'total_limit'               => DataType::INT
    ];

    /**
     * Get usage status of a team
     *
     * @param int $teamId
     *
     * @return TeamTranslationStatusEntity
     */
    public function getUsageStatus(int $teamId): TeamTranslationStatusEntity
    {
        $option = [
            'conditions' => [
                'team_id' => $teamId
            ]
        ];

        /** @var TeamTranslationStatusEntity $queryResult */
        $queryResult = $this->useType()->UseEntity()->find('first', $option);

        if (empty($queryResult)) {
            throw new GlException\GoalousNotFoundException();
        }

        return $queryResult;
    }

    /**
     * Get total of translation count in a team
     *
     * @param int $teamId
     *
     * @return int
     */
    public function getTotalUsageCount(int $teamId): int
    {
        return $this->getUsageStatus($teamId)->getTotalUsageCount();
    }

    /**
     * Increment translation count in circle post in a team
     *
     * @param int $teamId Team Id
     * @param int $count  Amount to increment
     */
    public function incrementCirclePostCount(int $teamId, int $count)
    {

        $this->incrementTranslationCount('circle_post_total', $teamId, $count);
    }

    /**
     * Increment translation count in circle post's comment in a team
     *
     * @param int $teamId Team Id
     * @param int $count  Amount to increment
     */
    public function incrementCircleCommentCount(int $teamId, int $count)
    {

        $this->incrementTranslationCount('circle_post_comment_total', $teamId, $count);
    }

    /**
     * Increment translation count in action post in a team
     *
     * @param int $teamId Team Id
     * @param int $count  Amount to increment
     */
    public function incrementActionPostCount(int $teamId, int $count)
    {

        $this->incrementTranslationCount('action_post_total', $teamId, $count);
    }

    /**
     * Increment translation count in action post's comment in a team
     *
     * @param int $teamId Team Id
     * @param int $count  Amount to increment
     */
    public function incrementActionCommentCount(int $teamId, int $count)
    {
        $this->incrementTranslationCount('action_post_comment_total', $teamId, $count);
    }

    /**
     * Increment a given column by given count in a team
     *
     * @param string $columnName Data to increment
     * @param int    $teamId     Team Id
     * @param int    $count      Amount to increment
     */
    private function incrementTranslationCount(string $columnName, int $teamId, int $count)
    {
        $newData = [
            $columnName => "$columnName + $count",
            'modified'  => GoalousDateTime::now()->getTimestamp()
        ];

        $condition = [
            'team_id' => $teamId
        ];

        $this->updateAll($newData, $condition);
    }

    /**
     * Reset all translation count in a team
     *
     * @param int $teamId
     */
    public function resetAllTranslationCount(int $teamId)
    {
        $newData = [
            'circle_post_total'         => 0,
            'circle_post_comment_total' => 0,
            'action_post_total'         => 0,
            'action_post_comment_total' => 0,
            'modified'                  => GoalousDateTime::now()->getTimestamp()
        ];

        $condition = [
            'team_id' => $teamId
        ];

        $this->updateAll($newData, $condition);
    }

    /**
     * Get translation limit of the team
     *
     * @param int $teamId
     *
     * @return int
     */
    public function getLimit(int $teamId): int
    {
        return $this->getUsageStatus($teamId)['total_limit'];
    }

    /**
     * Set translation limit of the team
     *
     * @param int $teamId
     * @param int $newLimit
     */
    public function setLimit(int $teamId, int $newLimit)
    {
        $newData = [
            'total_limit' => $newLimit,
            'modified'    => GoalousDateTime::now()->getTimestamp()
        ];

        $condition = [
            'team_id' => $teamId
        ];

        $this->updateAll($newData, $condition);
    }

    /**
     * Create new entry for a team
     *
     * @param int $teamId Team ID
     * @param int $limit  Translation limit per payment cycle. Default to 10000 for trial team
     *
     * @throws Exception
     */
    public function createEntry(int $teamId, int $limit = 10000)
    {
        $newData = [
            'team_id'     => $teamId,
            'total_limit' => $limit
        ];

        $this->create();
        $this->save($newData, false);
    }


    /**
     * Check if entry exists for a team
     *
     * @param int $teamId Team ID
     *
     * @return bool
     */
    public function hasEntry(int $teamId): bool
    {
        $option = [
            'conditions' => [
                'team_id' => $teamId,
            ]
        ];

        return $this->find('count', $option) > 0;
    }

    /**
     * Return usage data as json string
     *
     * @param int $teamId
     *
     * @return array
     */
    public function exportUsageAsJson(int $teamId): string
    {
        $data = $this->getUsageStatus($teamId);

        return json_encode([
            'circle_post_total'         => $data->getCirclePostUsageCount(),
            'circle_post_comment_total' => $data->getCirclePostCommentUsageCount(),
            'action_post_total'         => $data->getActionPostUsageCount(),
            'action_post_comment_total' => $data->getActionPostCommentUsageCount(),
        ]);
    }

    /**
     * Check whether translation limit is reached in a team
     *
     * @param int $teamId
     *
     * @return bool
     */
    public function isLimitReached(int $teamId): bool
    {
        /** @var TeamTranslationStatusEntity $usageStatus */
        $usageStatus = $this->getUsageStatus($teamId);

        return $usageStatus->getTotalUsageCount() >= $usageStatus['total_limit'];
    }
}