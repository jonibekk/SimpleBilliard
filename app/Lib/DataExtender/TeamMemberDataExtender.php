<?php
App::uses("TeamMember", "Model");
App::import('Lib/DataExtender', 'DataExtender');
/**
 * Created by PhpStorm.
 * User: Stephen Raharja
 * Date: 12/4/2018
 * Time: 4:51 PM
 */

class TeamMemberDataExtender extends DataExtender
{
    /** @var int */
    private $teamId;

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
        if (empty($this->teamId)) {
            throw new RuntimeException("Missing team ID");
        }

        $filteredKeys = $this->filterKeys($keys);

        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init('TeamMember');

        $options = [
            'conditions' => [
                'user_id' => $filteredKeys,
                'team_id' => $this->teamId
            ],
            'fields'     => [
                'user_id',
                'status'
            ]
        ];

        $result = $TeamMember->find('all', $options);

        return $result;
    }
}