<?php
App::import('Service', 'AppService');
App::import('Service', 'ExperimentService');
App::uses('Circle', 'Model');
App::uses('CircleMember', 'Model');
App::uses('User', 'Model');
App::uses('Post', 'Model');

/**
 * Class CircleService
 */
class CircleService extends AppService
{
    // Cache Experiment mode or not
    private $isExperimentMode;

    /**
     * Create circle
     *
     * @param  array $data
     * @param  array $members
     *
     * @return bool
     */
    function create(array $data, int $myUserId,  array $memberUserIds = []): bool
    {
        /** @var Circle $Circle */
        $Circle = ClassRegistry::init('Circle');
        /** @var CircleMember $CircleMember */
        $CircleMember = ClassRegistry::init('CircleMember');
        /** @var Post $Post */
        $Post = ClassRegistry::init('Post');

        try {
            $Circle->begin();

            // create circle
            if (!$Circle->add($data, $myUserId)) {
                throw new Exception(sprintf("Failed to create a circle. data:%s userId:%d", var_export($data, true), $myUserId));
            }
            $newCircleId = $Circle->getLastInsertID();

            // validate members
            if ($memberUserIds) {
                $validateMembers = $this->validateAddMembers($newCircleId, $myUserId, $memberUserIds, true);
                if ($validateMembers !== true) {
                    throw new Exception(sprintf("Failed to add members. userIds:%s", var_export($memberUserIds, true)));
                }
            }

            // build save data
            $saveData = [];
            array_unshift($memberUserIds, $myUserId);
            foreach($memberUserIds as $userId) {
                $isAdmin = ($userId == $myUserId);
                $saveData[] = $this->buildJoinData($newCircleId, $userId, $isAdmin);
            }
            // save
            if (!$CircleMember->bulkInsert($saveData)) {
                throw new Exception(sprintf("Failed to add members to circle. circleId:%d data:%s", $newCircleId, var_export($saveData, true)));
            }

            $CircleMember->updateCounterCache(['circle_id' => $newCircleId]);

            // post public circle create
            if (Hash::get($data, 'Circle.public_flg')) {
                $Post->createCirclePost($newCircleId, $myUserId);
            }

        } catch (Exception $e) {
            $this->log(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            $this->log($e->getTraceAsString());
            $Circle->rollback();
            return false;
        }

        // Delete joined members circles cache
        foreach($memberUserIds as $userId) {
            $this->deleteUserCirclesCache($userId);
        }

        $Circle->commit();
        return true;
    }

    /**
     * Validate create circle
     *
     * @param  array  $circle
     * @param  array  $members
     *
     * @return true|string
     */
    function validateCreate(array $circle, int $userId)
    {
        /** @var Circle $Circle */
        $Circle = ClassRegistry::init("Circle");
        /** @var CircleMember $CircleMember */
        $CircleMember = ClassRegistry::init("CircleMember");

        // Validate circle
        $circle['Circle']['user_id'] = $userId;
        $Circle->set(Hash::get($circle, 'Circle'));
        if (!$Circle->validates()) {
            return false;
        }

        return true;
    }

    /**
     * Join circle
     * - If AB Test mode, feed and notify are disabled
     * - Join circle
     * - Delete my circles cache
     *
     * @param  array $circles
     * @param  int   $userId
     *
     * @return bool
     */
    function join(int $circleId, int $userId, bool $isAdmin = false): bool
    {
        /** @var CircleMember $CircleMember */
        $CircleMember = ClassRegistry::init('CircleMember');
        /** @var ExperimentService $ExperimentService */
        $ExperimentService = ClassRegistry::init('ExperimentService');

        // check already joined or not
        if ($CircleMember->isJoined($circleId, $userId)) {
            return false;
        }

        // join circle
        $saveData = $this->buildJoinData($circleId, $userId);
        if (!$CircleMember->save($saveData)) {
            return false;
        }

        $CircleMember->updateCounterCache(['circle_id' => $circleId]);

        // Delete circles cache
        $this->deleteUserCirclesCache($userId);

        return true;
    }

    /**
     * Join multiple circles
     *
     * @param  array $circleIds
     * @param  int   $userId
     *
     * @return bool
     */
    function joinMultiple(array $circleIds, int $userId): bool
    {
        /** @var CircleMember $CircleMember */
        $CircleMember = ClassRegistry::init('CircleMember');

        try {
            $CircleMember->begin();

            // build save data
            foreach($circleIds as $circleId) {
                $saveData[] = $this->buildJoinData($circleId, $userId);
            }
            // join
            if (!$CircleMember->bulkInsert($saveData)) {
                throw new Exception(sprintf("Failed to add members to circle. circleIds:%s data:%s", var_export($circleIds, true), var_export($saveData, true)));
            }

            foreach($circleIds as $circleId) {
                $CircleMember->updateCounterCache(['circle_id' => $circleId]);
            }

        } catch (Exception $e) {
            $this->log(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            $this->log($e->getTraceAsString());
            $CircleMember->rollback();
            return false;
        }

        $CircleMember->commit();

        // delete cache
        $this->deleteUserCirclesCache($userId);

        return true;
    }

    /**
     * Leave circles
     * - Delete circle member record
     * - Delete my circles cache
     *
     * @param array $circles
     * @param int $userId
     *
     * @return bool
     */
    function leave(int $circleId, int $userId): bool
    {
        /** @var CircleMember $CircleMember */
        $CircleMember = ClassRegistry::init('CircleMember');

        // Leave circles
        if (!$CircleMember->leave($circleId, $userId)) {
            return false;
        }

        // Delete circles cache
        $this->deleteUserCirclesCache($userId);

        return true;
    }

    /**
     * Add members to circle
     * - bulk insert
     *
     * @param  int   $circleId
     * @param  array $memberUserIds
     *
     * @return bool
     */
    function addMembers(int $circleId, array $memberUserIds): bool
    {
        /** @var CircleMember $CircleMember */
        $CircleMember = ClassRegistry::init('CircleMember');

        try {
            $CircleMember->begin();

            $saveData = [];
            // build save data
            foreach($memberUserIds as $userId) {
                $saveData[] = $this->buildJoinData($circleId, $userId);
            }
            // save
            if (!$CircleMember->bulkInsert($saveData)) {
                throw new Exception(sprintf("Failed to add members to circle. circleId:%d data:%s", $circleId, var_export($saveData, true)));
            }

            $CircleMember->updateCounterCache(['circle_id' => $circleId]);

        } catch (Exception $e) {
            $this->log(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            $this->log($e->getTraceAsString());
            $CircleMember->rollback();
            return false;
        }

        $CircleMember->commit();

        // Delete joined members circles cache
        foreach($memberUserIds as $userId) {
            $this->deleteUserCirclesCache($userId);
        }

        return true;
    }

    /**
     * Validate add member to circle
     *
     * @param  int $circleId
     * @param  int $userId
     *
     * @return true|string
     */
    function validateAddMembers(int $circleId, int $myUserId, array $memberUserIds, bool $isCreate = false)
    {
        /** @var CircleMember $CircleMember */
        $CircleMember = ClassRegistry::init('CircleMember');
        /** @var Circle $Circle */
        $Circle = ClassRegistry::init('Circle');
        /** @var User $User */
        $User = ClassRegistry::init('User');

        // check exec user is admin
        // if create mode, not exist circle_member record, then pass this check
        if (!$isCreate && !$CircleMember->isAdmin($myUserId, $circleId)) {
            return __("It's only a circle administrator that can change circle settings.");
        }

        // can't add to team all circle
        $beforeCircle = $Circle->getById($circleId);
        if ($beforeCircle['team_all_flg']) {
            return __("You can't change members of the all team circle.");
        }

        // check numeric
        if (!Hash::numeric($memberUserIds)) {
            return __("Failed to add circle member(s.)");
        }

        // check duplicate
        if (count($memberUserIds) !== count(array_unique($memberUserIds))) {
            return __("Failed to add circle member(s.)");
        }

        // check users active
        if (!$User->isActiveUsers($memberUserIds)) {
            return __("Failed to add circle member(s.)");
        }

        // Check can join member
        $usersExist = $CircleMember->find('count', ['conditions' => ['user_id' => $memberUserIds, 'circle_id' => $circleId]]);
        if ($usersExist > 0) {
            return __("Failed to add circle member(s.)");
        }

        return true;
    }

    /**
     * build join circle member data
     * - define experiment mode or not
     * - if exist this cache, use cache
     *
     * @param  int     $circleId
     * @param  int     $userId
     * @param  boolean $isAdmin
     *
     * @return array
     */
    function buildJoinData(int $circleId, int $userId, bool $isAdmin = false): array
    {
        /** @var ExperimentService $ExperimentService */
        $ExperimentService = ClassRegistry::init('ExperimentService');
        /** @var CircleMember $CircleMember */
        $CircleMember = ClassRegistry::init('CircleMember');

        if ($this->isExperimentMode === null) {
            $isExperimentMode = $ExperimentService->isDefined(Experiment::NAME_CIRCLE_DEFAULT_SETTING_OFF);
            $this->isExperimentMode = $isExperimentMode;
        }

        $showForAllFeedFlg = $this->isExperimentMode ? false : true;
        $getNotificationFlg = $this->isExperimentMode ? false : true;

        $saveData = [
            'circle_id'             => $circleId,
            'team_id'               => $CircleMember->current_team_id,
            'user_id'               => $userId,
            'admin_flg'             => $isAdmin,
            'show_for_all_feed_flg' => $showForAllFeedFlg,
            'get_notification_flg'  => $getNotificationFlg,
        ];
        return $saveData;
    }

    /**
     * extract user ids from select2 string
     *
     * @param  array ['user_{user_id}']
     * @return array [{user_id}]
     */
    function extractUserIds(string $userListStr): array
    {
        $members = explode(",", $userListStr);
        return Hash::map($members, '', function ($member) {
            $memberUserId = str_replace('user_', '', $member);
            return $memberUserId;
        });
    }

    /**
     * Delete user's circles cache
     * @param [type] $userId [description]
     */
    function deleteUserCirclesCache(int $userId)
    {
        /** @var CircleMember $CircleMember */
        $CircleMember = ClassRegistry::init('CircleMember');

        Cache::delete($CircleMember->getCacheKey(CACHE_KEY_CHANNEL_CIRCLES_ALL, true, $userId), 'user_data');
        Cache::delete($CircleMember->getCacheKey(CACHE_KEY_CHANNEL_CIRCLES_NOT_HIDE, true, $userId), 'user_data');
        Cache::delete($CircleMember->getCacheKey(CACHE_KEY_MY_CIRCLE_LIST, true, $userId), 'user_data');
    }

}
