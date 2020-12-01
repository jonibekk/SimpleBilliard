<?php

App::uses('AppModel', 'Model');

class TeamLoginMethod extends AppModel
{
    /**
     * Add new login method to a team
     *
     * @param int    $teamId
     * @param string $loginMethod
     *
     * @throws Exception
     */
    public function addLoginMethod(int $teamId, string $loginMethod): void
    {
        $newData = [
            'team_id' => $teamId,
            'method'  => $loginMethod,
            'created' => GoalousDateTime::now()->getTimestamp()
        ];

        $this->create();
        $this->save($newData, false);
    }

    /**
     * Get all login methods of a team
     *
     * @param int $teamId
     *
     * @return mixed
     */
    public function getLoginMethods(int $teamId): array
    {
        $option = [
            'conditions' => [
                'team_id' => $teamId
            ]
        ];

        return $this->find('all', $option);
    }

    /**
     * Check whether the team has the following login method in DB
     *
     * @param int    $teamId
     * @param string $loginMethod
     *
     * @return bool
     */
    public function hasLoginMethod(int $teamId, string $loginMethod): bool
    {
        $option = [
            'conditions' => [
                'team_id' => $teamId,
                'method'  => $loginMethod
            ]
        ];

        return $this->find('count', $option) > 0;
    }

    /**
     * Delete login method from a team
     *
     * @param int    $teamId
     * @param string $loginMethod
     */
    public function deleteLoginMethod(int $teamId, string $loginMethod): void
    {
        $condition = [
            'team_id' => $teamId,
            'method'  => $loginMethod
        ];

        $this->deleteAll($condition);
    }

    /**
     * WARNING!! Delete all login methods from a team. Use cautiously.
     *
     * @param int $teamId
     */
    public function purgeLoginMethods(int $teamId): void
    {
        $condition = [
            'team_id' => $teamId
        ];

        $this->deleteAll($condition);
    }
}
