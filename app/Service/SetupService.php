<?php
App::uses('User', 'Model');
App::uses('GlRedis', 'Model');
App::uses('TransactionManager', 'Model');

/**
 * Class SetupService
 */
class SetupService extends CakeObject
{
    public function getSetupStatuses(string $userId)
    {
        /** @var GlRedis $GlRedis */
        $GlRedis = ClassRegistry::init("GlRedis");
        /** @var User $User */
        $User = ClassRegistry::init("User");

        $status = $GlRedis->getSetupGuideStatus($userId);
        if (!$status) {
            $status = $User->generateSetupGuideStatusDict($userId);
            //set update time
            $status[GlRedis::FIELD_SETUP_LAST_UPDATE_TIME] = GoalousDateTime::now()->getTimestamp();
            $GlRedis->saveSetupGuideStatus($userId, $status);

            $status = $GlRedis->getSetupGuideStatus($userId);
        }
        // remove last update time
        unset($status[GlRedis::FIELD_SETUP_LAST_UPDATE_TIME]);
        // remove setup's removed
        unset($status[User::SETUP_GOAL_CREATED]);
        unset($status[User::SETUP_ACTION_POSTED]);
        unset($status[User::SETUP_CIRCLE_JOINED_OR_CREATED]);
        unset($status[User::SETUP_CIRCLE_POSTED]);

        return $status;
    }
}
