<?php
App::import('Service', 'AppService');
App::import('Service', 'ExperimentService');
App::uses('CircleMember', 'Model');

/**
 * Class CircleService
 */
class CircleService extends AppService
{

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
                $this->log(sprintf("Failed to create a circle. data:%s userId:%d", var_export($data, true), $myUserId));
                throw new Exception();
            }
            $newCircleId = $Circle->getLastInsertID();

            // add members
            array_unshift($memberUserIds, $myUserId);
            foreach($memberUserIds as $userId) {
                if (!$userId) continue;
                $isAdmin = ($userId === $myUserId);
                if (!$this->join($newCircleId, $userId, $isAdmin)) {
                    $this->log(sprintf("Failed to add members to circle. circleId:%d userId:%d", $circleId, $userId));
                    throw new Exception();
                }
            }

            if (Hash::get($data, 'Circle.public_flg')) {
                $Post->createCirclePost($newCircleId, $myUserId);
            }

        } catch (Exception $e) {
            $this->log(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            $this->log($e->getTraceAsString());
            $Circle->rollback();
            return false;
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
    function validateCreate(array $circle, int $userId,  array $members = [])
    {
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

        // Join circle
        $isExperimentMode = $ExperimentService->isDefined(Experiment::NAME_CIRCLE_DEFAULT_SETTING_OFF);
        $showAllFlg = $isExperimentMode ? false : true;
        $notifyFlg = $isExperimentMode ? false : true;
        if (!$CircleMember->join($circleId, $userId, $showAllFlg, $notifyFlg, $isAdmin)) {
            return false;
        }

        // Delete circles cache
        Cache::delete($CircleMember->getCacheKey(CACHE_KEY_CHANNEL_CIRCLES_ALL, true, $userId), 'user_data');
        Cache::delete($CircleMember->getCacheKey(CACHE_KEY_CHANNEL_CIRCLES_NOT_HIDE, true, $userId), 'user_data');
        Cache::delete($CircleMember->getCacheKey(CACHE_KEY_MY_CIRCLE_LIST, true, $userId), 'user_data');

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

            foreach($circleIds as $circleId) {
                if (!$this->join($circleId, $userId)) {
                    throw new Exception(sprintf("Failed to add members to circle. circleId:%d userId:%d", $circleId, $userId));
                }
            }
        } catch (Exception $e) {
            $this->log(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            $this->log($e->getTraceAsString());
            $CircleMember->rollback();
            return false;
        }

        $CircleMember->commit();
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
        Cache::delete($CircleMember->getCacheKey(CACHE_KEY_CHANNEL_CIRCLES_ALL, true, $userId), 'user_data');
        Cache::delete($CircleMember->getCacheKey(CACHE_KEY_CHANNEL_CIRCLES_NOT_HIDE, true, $userId), 'user_data');
        Cache::delete($CircleMember->getCacheKey(CACHE_KEY_MY_CIRCLE_LIST, true, $userId), 'user_data');

        return true;
    }

    /**
     * Add members to circle
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

            foreach($memberUserIds as $userId) {
                if (!$this->join($circleId, $userId)) {
                    throw new Exception(sprintf("Failed to add members to circle. circleId:%d userId:%d", $circleId, $userId));
                }
            }
        } catch (Exception $e) {
            $this->log(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            $this->log($e->getTraceAsString());
            $CircleMember->rollback();
            return false;
        }

        $CircleMember->commit();
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
    function validateAddMember(int $circleId, int $myUserId, array $memberUserIds)
    {
        /** @var CircleMember $CircleMember */
        $CircleMember = ClassRegistry::init('CircleMember');
        /** @var Circle $Circle */
        $Circle = ClassRegistry::init('Circle');

        if (!$CircleMember->isAdmin($myUserId, $circleId)) {
            return __("It's only a circle administrator that can change circle settings.");
        }

        $beforeCircle = $Circle->findById($circleId, ['team_all_flg']);
        if ($beforeCircle['Circle']['team_all_flg']) {
            return __("You can't change members of the all team circle.");
        }

        $beforeMemberList = $CircleMember->getMemberList($circleId, true);
        foreach($memberUserIds as $memberUserId) {
            if (!$memberUserId) {
                return __("Failed to add circle member(s.)");
            }
            if (isset($beforeMemberList[$memberUserId])) {
                return __("Failed to add circle member(s.)");
            }
        }

        return true;
    }

}
