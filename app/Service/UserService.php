<?php

App::import('Service', 'AppService');
App::import('Service', 'PaymentService');
App::uses('User', 'Model');
App::uses("Circle", 'Model');
App::uses("Team", 'Model');
App::uses("TeamMember", 'Model');
App::import('Lib/DataExtender', 'MeExtender');
App::import('Service/Request/Resource', 'UserResourceRequest');
App::import('Service', 'ImageStorageService');
App::import('Service', 'UserService');

App::import('Model/Dto/UserSettings', 'UserAccount');
App::import('Model/Dto/UserSettings', 'UserChangeEmail');

use Goalous\Enum as Enum;

/**
 * Class UserService
 */
class UserService extends AppService
{
    /**
     * Get single data based on model.
     * extend data
     *
     * @param UserResourceRequest $req
     * @param array               $extensions
     *
     * @return array
     */
    public function get(UserResourceRequest $req, array $extensions = []): array
    {
        $userId = $req->getId();
        $teamId = $req->getTeamId();

        /** @var User $User */
        $User = ClassRegistry::init('User');

        $fields = $req->isMe() ? $User->loginUserFields : $User->profileFields;
        $data = $this->_getWithCache($userId, 'User', $fields);
        if (empty($data)) {
            return [];
        }

        if ($req->isMe()) {
            /** @var MeExtender $MeExtender */
            $MeExtender = ClassRegistry::init('MeExtender');

            $data = $MeExtender->extend($data, $userId, $teamId, $extensions);
        } else {
            // TODO: create UserExtender
            // Be careful to convert language to 2 characters as same as $MeExtender(LangUtil::convertISOFrom3to2)
        }
        return $data;
    }

    /**
     * Get minimum user information without any information related to team
     * Used in case where user doesn't belong to any team
     *
     * @param int  $userId
     * @param bool $isMe Whether user requesting own information
     *
     * @return array
     */
    public function getMinimum(int $userId, bool $isMe = false): array
    {
        /** @var User $User */
        $User = ClassRegistry::init('User');

        $fields = $isMe ? $User->loginUserFields : $User->profileFields;
        $data = $this->_getWithCache($userId, 'User', $fields);
        if (empty($data)) {
            return [];
        }

        //Can't use extender since it required team id
        /** @var ImageStorageService $ImageStorageService */
        $ImageStorageService = ClassRegistry::init('ImageStorageService');
        $data['profile_img_url'] = $ImageStorageService->getImgUrlEachSize($data, 'User');
        $data['cover_img_url'] = $ImageStorageService->getImgUrlEachSize($data, 'User', 'cover_photo');

        $data['current_team_id'] = null;
        $data['current_team'] = [];
        $data['language'] = LangUtil::convertISOFrom3to2($data['language']);

        return $data;
    }

    /**
     * Getting user names as string from user id list.
     *
     * @param array  $userIds   e.g. [1,2,3]
     * @param string $delimiter
     * @param string $fieldName it should be included in user profile fields.
     *
     * @return string
     */
    function getUserNamesAsString(array $userIds, string $delimiter = ', ', string $fieldName = "display_first_name")
    {
        /** @var User $User */
        $User = ClassRegistry::init('User');
        $users = $User->findProfilesByIds($userIds);
        $userNames = Hash::extract($users, "{n}.$fieldName");
        $ret = implode($delimiter, $userNames);
        return $ret;
    }

    /**
     * find topic new members for select2 on message
     *
     * @param string  $keyword
     * @param integer $limit
     * @param int     $topicId
     * @param boolean $withGroup
     *
     * @return array
     */
    function findUsersForAddingOnTopic(string $keyword, int $limit = 10, int $topicId, bool $withGroup = false): array
    {
        /** @var User $User */
        $User = ClassRegistry::init('User');
        /** @var TopicMember $TopicMember */
        $TopicMember = ClassRegistry::init('TopicMember');

        $topicUsers = $TopicMember->findMemberIdList($topicId);
        // exclude users who joined topic
        $newUsers = $User->getUsersByKeyword($keyword, $limit, true, $topicUsers);
        $newUsers = $User->makeSelect2UserList($newUsers);

        // グループを結果に含める場合
        // 既にメッセージメンバーになっているユーザーを除外してから返却データに追加
        if ($withGroup) {
            // excludeGroupMemberSelect2() の中では配列のキーにuserIdがセットされてること前提で書かれているため、
            // [1, 2, 3] -> [1 => 1, 2 => 2, 3 => 3] の形に変換
            $topicUsersForGroup = array_combine($topicUsers, $topicUsers);
            $group = $User->getGroupsSelect2($keyword, $limit);
            $newUsers = array_merge(
                $newUsers,
                $User->excludeGroupMemberSelect2($group['results'], $topicUsersForGroup)
            );
        }

        return $newUsers;
    }

    /**
     * Update default_team_id of specified users.id
     *
     * @param int      $userId
     * @param int|null $teamId
     *
     * @return bool
     */
    public function updateDefaultTeam(int $userId, ?int $teamId): bool
    {
        /** @var User $User */
        $User = ClassRegistry::init('User');
        $User->id = $userId;
        try {
            $this->TransactionManager->begin();
            $User->updateAll(['User.default_team_id' => $teamId], ['User.id' => $userId]);
            $this->TransactionManager->commit();
            return true;
        } catch (Exception $exception) {
            $this->TransactionManager->rollback();
            throw $exception;
        }
    }


    /**
     * Search users for mention by keyword
     *
     * @param string   $keyword
     * @param int      $teamId
     * @param int      $userId
     * @param int      $limit
     * @param int|null $postId       : Affection range by post (especially post is in secret circle, search range is
     *                               only target secret circle members)
     * @param int      $resourceType : 1, comment; 2, post;
     *
     * @return array
     */
    public function findMentionItems(
        string $keyword,
        int $teamId,
        int $userId,
        $limit = 10,
        $resourceId = null,
        $resourceType = 1
    ): array {
        $keyword = trim($keyword);
        if (strlen($keyword) == 0) {
            return [];
        }

        /** @var Post $Post */
        $Post = ClassRegistry::init('Post');
        /** @var Circle $Circle */
        $Circle = ClassRegistry::init('Circle');
        /** @var User $User */
        $User = ClassRegistry::init('User');

        $groupIds = [];

        switch ($resourceType) {
            case Enum\MentionSearchType::COMMENT:
                $postId = $resourceId;
                if (!empty($postId)) {
                    $circle = $Circle->getSharedSecretCircleByPostId($postId);
                    $secretCircleId = !empty($circle) && $circle['public_flg'] === false ? $circle['id'] : null;
                    $groupIds = $Post->getPostGroups($postId);
                } else {
                    $secretCircleId = null;
                }
                break;
            case Enum\MentionSearchType::POST:
                $circleId = $resourceId;
                if (!empty($circleId)) {
                    $circle = $Circle->getById($circleId);
                    $secretCircleId = !empty($circle) && $circle['public_flg'] === false ? $circle['id'] : null;
                } else {
                    $secretCircleId = null;
                }
                break;
            default:
                $secretCircleId = null;
                break;
        }
        /*
        if (!empty($postId)) {
            $circle = $Circle->getSharedSecretCircleByPostId($postId);
            $secretCircleId = !empty($circle) && $circle['public_flg'] === false ? $circle['id'] : null;
        } else {
            $secretCircleId = null;
        }
         */

        $users = $User->findByKeywordRangeCircle($keyword, $teamId, $userId, $limit, true, $secretCircleId, $groupIds);

        return $users;
    }

    /**
     * Check whether user's default team is usable & user can access the team
     *
     * @param int $userId
     *
     * @return bool
     */
    public function isDefaultTeamValid(int $userId): bool
    {
        /** @var User $User */
        $User = ClassRegistry::init('User');

        $user = $User->getById($userId);

        if (empty($user)) {
            return false;
        }

        $defaultTeamId = $user['default_team_id'];

        if (empty($defaultTeamId)) {
            return false;
        }

        /** @var Team $Team */
        $Team = ClassRegistry::init('Team');

        if ($Team->isDeleted($defaultTeamId)) {
            return false;
        }

        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init('TeamMember');

        return $TeamMember->isStatusActive($defaultTeamId, $userId);
    }

    /**
     * Update user's default team id if it's invalid
     *
     * @param int $userId
     *
     * @throws Exception
     */
    public function updateDefaultTeamIfInvalid(int $userId)
    {
        if ($this->isDefaultTeamValid($userId)) {
            return;
        }

        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init('TeamMember');

        $nextActiveTeamId = $TeamMember->getLatestLoggedInActiveTeamId($userId);

        if (!empty($nextActiveTeamId)) {
            $this->updateDefaultTeam($userId, $nextActiveTeamId);
        }
    }

    // Get Raw User
    public function getUserData(int $userId): ?array
    {
        /** @var User $User */
        $User = ClassRegistry::init('User');

        $user_options = [
            'conditions' => ['User.id' => $userId,],
        ];

        $user = $User->find('first', $user_options);
        if (empty($user)) {
            return null;
        }

        return $user;
    }

    // Update User data, Primary key required to Save.
    public function updateUserData(int $userId, array $data): bool
    {
        /** @var User $User */
        $User = ClassRegistry::init('User');

        try {
            $User->save($data, false);
        } catch (Exception $e) {
            GoalousLog::error('Failed to update user data.', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'USer data' => $userId
            ]);
            return false;
        }

        return true;
    }

    // Save Field to specified column.
    public function saveField(int $id, $column, $value = null): bool
    {
        /** @var User $User */
        $User = ClassRegistry::init('User');

        $data = array(
            'User' => [
                'id' => $id,
                $column => $value
            ]
        );

        try {
            $User->save($data, false);
        } catch (Exception $e) {
            GoalousLog::error('Column Save Failed!', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'data'    => $data
            ]);
            return false;
        }

        return true;
    }

    // Invalidate TwoFa Auth.
    public function invalidateTwoFa(int $userId): bool
    {
        /** @var RecoveryCode $RecoveryCode */
        $RecoveryCode = ClassRegistry::init('RecoveryCode');

        return $RecoveryCode->invalidateAll($userId);
    }

    public function generateRecoveryCodes(int $userId): bool
    {
        /** @var RecoveryCode $RecoveryCode */
        $RecoveryCode = ClassRegistry::init('RecoveryCode');

        return $RecoveryCode->regenerate($userId);
    }

    public function getRecoveryCodes(int $userId): array
    {
        /** @var RecoveryCode $RecoveryCode */
        $RecoveryCode = ClassRegistry::init('RecoveryCode');

        return $RecoveryCode->getAll($userId);
    }
}
