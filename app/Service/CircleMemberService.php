<?php
App::import('Service', 'AppService');
App::uses('Circle', 'Model');
App::uses('CircleMember', 'Model');
App::import('Model/Entity', 'CircleMemberEntity');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/06/04
 * Time: 15:36
 */

use Goalous\Exception as GlException;

class CircleMemberService extends AppService
{

    /**
     * Fetch list of circles that the user belongs to in a given team
     *
     * @param int  $userId
     * @param int  $teamId
     * @param bool $publicOnlyFlag Whether the circle is public or not
     *
     * @return array Array of circle models
     */
    public function getUserCircles(int $userId, int $teamId, bool $publicOnlyFlag = true): array
    {
        /** @var Circle $Circle */
        $Circle = ClassRegistry::init('Circle');

        $conditions = [
            'conditions' => [
                'Circle.team_id' => $teamId,
                'Circle.del_flg' => false
            ],
            'joins'      => [
                [
                    'type'       => 'INNER',
                    'table'      => 'circle_members',
                    'alias'      => 'CircleMember',
                    'conditions' => [
                        'Circle.id = CircleMember.circle_id',
                        'CircleMember.user_id' => $userId,
                        'CircleMember.del_flg' => false
                    ]
                ]
            ]
        ];

        if ($publicOnlyFlag) {
            $conditions['conditions']['Circle.public_flg'] = $publicOnlyFlag;
        }

        return Hash::extract($Circle->find('all', $conditions), "{n}.Circle");
    }

    /**
     * Add new user to CircleMember
     *
     * @param int $userId
     * @param int $circleId
     * @param int $teamId
     *
     * @return CircleMemberEntity
     * @throws Exception
     */
    public function add(int $userId, int $circleId, int $teamId): CircleMemberEntity
    {
        /** @var Circle $Circle */
        $Circle = ClassRegistry::init("Circle");

        $condition = [
            'Circle.id'  => $circleId
        ];

        $circle = $Circle->find('first', $condition);

        if (empty($circle)) {
            throw new GlException\GoalousNotFoundException(__("This circle does not exist."));
        }

        /** @var CircleMember $CircleMember */
        $CircleMember = ClassRegistry::init('CircleMember');

        $condition = [
            'conditions' => [
                'circle_id' => $circleId,
                'user_id'   => $userId,
                'del_flg'   => false
            ]
        ];

        if (!empty($CircleMember->find('first', $condition))) {
            throw new GlException\GoalousConflictException(__("You already joined to this circle."));
        }

        $newData = [
            'circle_id' => $circleId,
            'user_id'   => $userId,
            'team_id'   => $teamId,
            'created'   => REQUEST_TIMESTAMP,
            'modified'  => REQUEST_TIMESTAMP
        ];

        $CircleMember->create();
        /** @var CircleMemberEntity $return */
        $return = $CircleMember->useType()->useEntity()->save($newData, false);

        if (empty($return)) {
            throw new RuntimeException("Failed to add new circle member");
        }

        return $return;
    }

}