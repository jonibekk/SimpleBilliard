<?php
App::uses('Circle', 'Model');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/06/04
 * Time: 15:36
 */
class CircleMemberService
{

    public function getUserCircles(int $userId, int $teamId, bool $isPublic = true): array
    {
        $circle = new Circle();

        $conditions = [
            'conditions' => [
                'Circle.team_id'    => $teamId,
                'Circle.public_flg' => $isPublic
            ],
            'joins'      => [
                [
                    'type'       => 'LEFT',
                    'table'      => 'circle_members',
                    'alias'      => 'CircleMember',
                    'conditions' => [
                        'Circle.id'            => 'CircleMember.circle_id',
                        'CircleMember.user_id' => $userId,
                    ]
                ]
            ]
        ];

        return $circle->find('all', $conditions);
    }

}