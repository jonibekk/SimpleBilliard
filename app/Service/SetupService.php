<?php
App::uses('User', 'Model');
App::uses('GlRedis', 'Model');
App::uses('TransactionManager', 'Model');

/**
 * Class SetupService
 */
class SetupService extends CakeObject
{
    /**
     * Retrieve the each setup item status
     * If there is no cached setup item's status, resolve from DB and store to Redis.
     *
     * @param string $userId
     * @return array|mixed
     */
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
        // remove setup's item has removed
        unset($status[User::SETUP_GOAL_CREATED]);
        unset($status[User::SETUP_ACTION_POSTED]);
        unset($status[User::SETUP_CIRCLE_JOINED_OR_CREATED]);
        unset($status[User::SETUP_CIRCLE_POSTED]);

        return $status;
    }

    /**
     * Resolve the user's setup status, doing update if there is inconsistent.
     *
     * @param string $userId
     * @param bool $setupCompleteFlg
     * @return array
     */
    public function resolveSetupCompleteAndRest(string $userId, bool $setupCompleteFlg): array
    {
        if ($setupCompleteFlg) {
            return [
                'complete' => true,
                'rest_count' => 0,
            ];
        }

        $setupStatuses = $this->getSetupStatuses($userId);
        $completed = array_filter($setupStatuses);
        $restCount = max(0, count(User::$TYPE_SETUP_GUIDE) - count($completed));

        if (!$setupCompleteFlg && 0 === $restCount) {
            // If setup item rest count is 0, but setup flag is not completed.
            // Update DB user to be setup completed.
            /** @var User $User */
            $User = ClassRegistry::init("User");
            $User->completeSetupGuide($userId);
            return [
                'complete' => true,
                'rest_count' => 0,
            ];
        }
        return [
            'complete' => false,
            'rest_count' => $restCount,
        ];
    }
}
