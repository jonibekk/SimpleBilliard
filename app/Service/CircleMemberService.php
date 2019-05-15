<?php
App::import('Service', 'AppService');
App::import('Service', 'CirclePinService');
App::import('Service', 'SavedPostService');
App::uses('Circle', 'Model');
App::uses('CircleMember', 'Model');
App::import('Model/Entity', 'CircleEntity');
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
            'admin_flg' => Enum\Model\CircleMember\CircleMember::NOT_ADMIN,
            'created'   => GoalousDateTime::now()->getTimestamp(),
            'modified'  => GoalousDateTime::now()->getTimestamp()
        ];

        try {
            $this->TransactionManager->begin();
            $CircleMember->create();
            /** @var CircleMemberEntity $return */
            $return = $CircleMember->useType()->useEntity()->save($newData, false);
            if (empty($return)) {
                throw new RuntimeException("Failed to add new member $userId to circle $circleId");
            }
            $Circle->updateMemberCount($circleId);

            /** @var GlRedis $GlRedis */
            $GlRedis = ClassRegistry::init("GlRedis");
            $GlRedis->deleteMultiCircleMemberCount([$circleId]);

            $this->TransactionManager->commit();
        } catch (Exception $exception) {
            $this->TransactionManager->rollback();
            GoalousLog::error("Failed to add new member $userId to circle $circleId", $exception->getTrace());
            throw $exception;
        }

        return $return;
    }

    /**
     * Add new users to CircleMember
     *
     * @param array $userIds
     * @param int $teamId
     * @param int $circleId
     *
     * @return array
     * @throws Exception
     */
    public function multipleAdd(array $userIds, int $teamId, int $circleId): array
    {
        if (empty($userIds)) {
            throw new GlException\GoalousNotFoundException(__("User list is empty"));
        }

        /** @var Circle $Circle */
        $Circle = ClassRegistry::init("Circle");

        $condition = [
            'conditions' => [
                'Circle.id' => $circleId
            ]
        ];

        $circle = $Circle->getById($circleId);

        if (empty($circle)) {
            throw new GlException\GoalousNotFoundException(__("This circle does not exist."));
        }

        /** @var User $User */
        $User = ClassRegistry::init('User');

        $condition = [
            'conditions' => [
                'User.id' => $userIds,
                'CircleMember.circle_id' => null
            ],
            'fields'     => [
                'User.id'
            ],
            'joins'      => [
                [
                    'type'       => 'LEFT OUTER',
                    'table'      => 'circle_members',
                    'alias'      => 'CircleMember',
                    'conditions' => [
                        'CircleMember.user_id = User.id',
                        'CircleMember.circle_id' => $circleId
                    ]
                ],
                [
                    'type'       => 'INNER',
                    'table'      => 'team_members',
                    'alias'      => 'TeamMember',
                    'conditions' => [
                        'TeamMember.user_id = User.id',
                        'TeamMember.team_id' => $teamId,
                        'TeamMember.del_flg' => 0,
                        'TeamMember.active_flg' = > 1
                    ]
                ]
            ],

        ];

        $newMemberIds = array_values($User->find('list',$condition));
        $notApplicableIds = array_diff($userIds, $newMemberIds);

        if(!empty($newMemberIds)){

            $newData = [];

            foreach($newMemberIds as $newMemberId){
                $newData[] = [
                    'circle_id' => $circleId,
                    'user_id'   => $newMemberId,
                    'team_id'   => $teamId,
                    'admin_flg' => Enum\Model\CircleMember\CircleMember::NOT_ADMIN
                ];
            }

            /** @var CircleMember $CircleMember */
            $CircleMember = ClassRegistry::init('CircleMember');

            try {
                $this->TransactionManager->begin();
                $CircleMember->create();
                /** @var CircleMemberEntity*/
                $CircleMember->useType()->useEntity()->bulkInsert($newData);
                $Circle->updateMemberCount($circleId);

                /** @var GlRedis $GlRedis */
                $GlRedis = ClassRegistry::init("GlRedis");
                $GlRedis->deleteMultiCircleMemberCount([$circleId]);

                $this->TransactionManager->commit();
            } catch (Exception $exception) {
                $this->TransactionManager->rollback();
                GoalousLog::error("Failed to add new member to circle $circleId", [
                    'message' => $exception->getMessage(),
                    'newMemberIds' => $newMemberIds,
                ]);
                GoalousLog::error($exception->getTrace());
                throw $exception;
            }
        }
        return [
                    'newMemberIds' => $newMemberIds,
                    'notApplicableIds' => $notApplicableIds
               ];
    }

    /**
     * Remove a member from a circle
     *
     * @param int $userId
     * @param int $teamId
     * @param int $circleId
     *
     * @return bool TRUE on successful delete
     * @throws Exception
     */
    public function delete(int $userId, int $teamId, int $circleId): bool
    {
        /** @var Circle $Circle */
        $Circle = ClassRegistry::init('Circle');

        /** @var CircleMember $CircleMember */
        $CircleMember = ClassRegistry::init('CircleMember');

        $circleCondition = [
            'conditions' => [
                'id'      => $circleId,
                'del_flg' => false
            ]
        ];

        $circle = $Circle->useType()->useEntity()->find('first', $circleCondition);

        if (empty($circle)) {
            throw new GlException\GoalousNotFoundException(__("This circle does not exist."));
        }

        $condition = [
            'CircleMember.user_id'   => $userId,
            'CircleMember.circle_id' => $circleId,
            'CircleMember.del_flg'   => false
        ];

        $circleMember = $CircleMember->find('first', ['conditions' => $condition]);

        if (empty($circleMember)) {
            throw new GlException\GoalousNotFoundException(__("Not exist"));
        }

        try {
            $this->TransactionManager->begin();
            $res = $CircleMember->deleteAll($condition);
            if (!$res) {
                throw new RuntimeException("Failed to unjoin user $userId from circle $circleId");
            }

            /** @var CirclePinService $CirclePinService */
            $CirclePinService = ClassRegistry::init('CirclePinService');
            $CirclePinService->deleteCircleId($userId, $teamId, $circleId);

            //If circle is secret, perform additional deletion
            if (!$circle['public_flg']) {
                /** @var SavedPostService $SavedPostService */
                $SavedPostService = ClassRegistry::init('SavedPostService');
                $SavedPostService->deleteAllInCircle($userId, $teamId, $circleId);
            }
            $Circle->updateMemberCount($circleId);

            /** @var GlRedis $GlRedis */
            $GlRedis = ClassRegistry::init("GlRedis");
            $GlRedis->deleteMultiCircleMemberCount([$circleId]);

            $this->TransactionManager->commit();
        } catch (Exception $exception) {
            $this->TransactionManager->rollback();
            GoalousLog::error($exception->getMessage(), $exception->getTrace());
            throw $exception;
        }

        return $res;
    }

    /**
     * Set notification setting for an user in a circle
     *
     * @param int  $circleId
     * @param int  $userId
     * @param bool $notificationFlg
     *
     * @throws Exception
     */
    public function setNotificationSetting(int $circleId, int $userId, bool $notificationFlg)
    {
        /** @var CircleMember $CircleMember */
        $CircleMember = ClassRegistry::init('CircleMember');

        $newData = [
            'get_notification_flg' => $notificationFlg,
            'modified'             => GoalousDateTime::now()->getTimestamp()
        ];

        $condition = [
            'CircleMember.user_id'   => $userId,
            'CircleMember.circle_id' => $circleId,
            'CircleMember.del_flg'   => false
        ];

        $circleMember = $CircleMember->find('first', ['conditions' => $condition]);

        if (empty($circleMember)) {
            throw new GlException\GoalousNotFoundException(__("Not exist"));
        }

        try {
            $this->TransactionManager->begin();
            $result = $CircleMember->updateAll($newData, $condition);
            if (!$result) {
                throw new RuntimeException("Failed to set notification setting of user $userId in circle $circleId");
            }
            $this->TransactionManager->commit();
        } catch (Exception $exception) {
            $this->TransactionManager->rollback();
            GoalousLog::error("Failed to set notification",
                [
                    "message" => $exception->getMessage(),
                    "trace"   => $exception->getTrace()
                ]);
            throw $exception;
        }
    }

    /**
     * Decreasing single circle unread count
     * @param int $circleId
     * @param int $userId
     * @param int $teamId
     * @param int $decreasingCount
     * @throws Exception
     */
    public function decreaseCircleUnreadCount(int $circleId, int $userId, int $teamId, int $decreasingCount)
    {
        /** @var CircleMember $CircleMember */
        $CircleMember = ClassRegistry::init('CircleMember');

        try {
            $this->TransactionManager->begin();
            $circleMember = $CircleMember->getCircleMember($circleId, $userId);
            if (empty($circleMember)) {
                return;
            }

            $unreadCountToBe = max(0, $circleMember['unread_count'] - $decreasingCount);
            $circleMember['unread_count'] = $unreadCountToBe;
            $CircleMember->save($circleMember);
            $this->TransactionManager->commit();
        } catch (Exception $exception) {
            $this->TransactionManager->rollback();
            GoalousLog::error('Failed decreasing post unread count',
                [
                    'message'   => $exception->getMessage(),
                    'circle.id' => $circleId,
                    'user.id'   => $userId,
                    'decrease'  => $decreasingCount,
                ]);
            throw $exception;
        }
    }
}
