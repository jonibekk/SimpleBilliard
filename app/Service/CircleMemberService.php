<?php
App::import('Service', 'AppService');
App::uses('Circle', 'Model');
App::uses('CircleMember', 'Model');
App::import('Model/Entity', 'CircleMemberEntity');
App::uses('NotifyBiz', 'Controller/Component');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/06/04
 * Time: 15:36
 */

use Goalous\Exception as GlException;
use Goalous\Enum as Enum;

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
     * @param int $teamId
     * @param int $circleId
     *
     * @return CircleMemberEntity
     * @throws Exception
     */
    public function add(int $userId, int $teamId, int $circleId): CircleMemberEntity
    {
        /** @var Circle $Circle */
        $Circle = ClassRegistry::init("Circle");

        $condition = [
            'conditions' => [
                'Circle.id' => $circleId
            ]
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
            //Define message in caller
            throw new GlException\GoalousConflictException();
        }

        $newData = [
            'circle_id' => $circleId,
            'user_id'   => $userId,
            'team_id'   => $teamId,
            'admin_flg' => Enum\Model\CircleMember\CircleMember::NOT_ADMIN(),
            'created'   => GoalousDateTime::now()->getTimestamp(),
            'modified'  => GoalousDateTime::now()->getTimestamp()
        ];

        $CircleMember->create();

        try {
            $this->TransactionManager->begin();
            /** @var CircleMemberEntity $return */
            $return = $CircleMember->useType()->useEntity()->save($newData, false);
            if (empty($return)) {
                throw new RuntimeException("Failed to add new member $userId to circle $circleId");
            }
            $Circle->updateMemberCount($circleId);
            $this->TransactionManager->commit();
        } catch (Exception $exception) {
            $this->TransactionManager->rollback();
            GoalousLog::error("Failed to add new member $userId to circle $circleId", $exception->getTrace());
            throw $exception;
        }

        return $return;
    }

    /**
     * Send notification to all members in a circle
     *
     * @param int $notificationType
     * @param int $circleId
     * @param int $userId
     * @param int $teamId
     */
    public function notifyMembers(int $notificationType, int $circleId, int $userId, int $teamId)
    {
        /** @var CircleMember $CircleMember */
        $CircleMember = ClassRegistry::init('CircleMember');

        $memberList = $CircleMember->getMemberList($circleId, true, false, $userId);

        /** @var NotifyBizComponent $notifyBiz */
        $notifyBiz = ClassRegistry::init('NotifyBizComponent');
        // Notify to circle member
        $notifyBiz->execSendNotify($notificationType, $circleId, null, $memberList, $teamId, $userId);
    }

}