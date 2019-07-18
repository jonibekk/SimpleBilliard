<?php
App::import('Service', 'AppService');
App::import('Service', 'PaymentService');
App::uses('User', 'Model');
App::uses("Circle", 'Model');
App::import('Lib/DataExtender', 'MeExtender');
App::import('Service/Request/Resource', 'UserResourceRequest');
App::import('Service', 'UserService');

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
        }
        return $data;
    }

    /**
     * Getting user names as string from user id list.
     *
     * @param array $userIds e.g. [1,2,3]
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
     * @param  string $keyword
     * @param  integer $limit
     * @param  int $topicId
     * @param  boolean $withGroup
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
            $newUsers = array_merge($newUsers,
                $User->excludeGroupMemberSelect2($group['results'], $topicUsersForGroup));
        }

        return $newUsers;
    }

    /**
     * Update default_team_id of specified users.id
     *
     * @param int $userId
     * @param int $teamId
     *
     * @return bool
     */
    public function updateDefaultTeam(int $userId, int $teamId): bool
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
     * @param string $keyword
     * @param int $teamId
     * @param int $userId
     * @param int $limit
     * @param int|null $postId: Affection range by post (especially post is in secret circle, search range is only target secret circle members)
     * @return array
     */
    public function findMentionItems(string $keyword, int $teamId, int $userId, $limit = 10, $postId = null) : array
    {
        $keyword = trim($keyword);
        if (strlen($keyword) == 0) {
            return [];
        }

        /** @var Circle $Circle */
        $Circle = ClassRegistry::init('Circle');
        /** @var User $User */
        $User = ClassRegistry::init('User');

        if (!empty($postId)) {
            $circle = $Circle->getSharedSecretCircleByPostId($postId);
            $secretCircleId = !empty($circle) && $circle['public_flg'] === false ? $circle['id'] : null;
        } else {
            $secretCircleId = null;
        }

        $users = $User->findByKeywordRangeCircle($keyword, $teamId, $userId, $limit, true, $secretCircleId);
        return $users;
    }
}
