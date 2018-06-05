<?php
App::import('Service', 'AppService');
App::uses('Circle', 'Model');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/06/04
 * Time: 15:36
 */
class CircleMemberService extends AppService
{

    /**
     * Fetch list of circles that the user belongs to in a given team
     *
     * @param int  $userId
     * @param int  $teamId
     * @param bool $isPublic Whether the circle is public or not
     *
     * @return array Array of circle models
     */
    public function getUserCircles(int $userId, int $teamId, bool $isPublic = true): array
    {
        /** @var Circle $Circle */
        $Circle = ClassRegistry::init('Circle');

        $conditions = [
            'conditions' => [
                'Circle.team_id'    => $teamId,
                'Circle.public_flg' => $isPublic,
                'Circle.del_flg'    => false
            ],
            'joins'      => [
                [
                    'type'       => 'LEFT',
                    'table'      => 'circle_members',
                    'alias'      => 'CircleMember',
                    'conditions' => [
                        'Circle.id'            => 'CircleMember.circle_id',
                        'CircleMember.user_id' => $userId,
                        'CircleMember.del_flg' => false
                    ]
                ]
            ]
        ];

        return $Circle->find('all', $conditions);
    }

}