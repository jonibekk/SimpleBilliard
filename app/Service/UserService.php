<?php
App::import('Service', 'AppService');
App::import('Service', 'PaymentService');
App::uses('User', 'Model');

use Goalous\Model\Enum as Enum;

/**
 * Class UserService
 */
class UserService extends AppService
{
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
     * @param  string  $keyword
     * @param  integer $limit
     * @param  int     $topicId
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
     * get curency as team member
     * - If team's status is paid plan, use team currency
     * - If not, decide currency by user language
     *
     * @param int|null $teamId
     *
     * @return int
     */
    function getCurrencyAsMember($teamId = null): int
    {
        if (AppUtil::isInt($teamId)) {
            /** @var PaymentService $PaymentService */
            $PaymentService = ClassRegistry::init("PaymentService");
            $paymentSetting = $PaymentService->get($teamId);
            // If paid plan, use currency of payment setting
            if (!empty($paymentSetting)) {
                return $paymentSetting['currency'];
            }
        }

        App::uses('LangHelper', 'View/Helper');
        $Lang = new LangHelper(new View());
        $userCountryCode = $Lang->getUserCountryCode();
        return $userCountryCode == 'JP' ?
            Enum\PaymentSetting\Currency::JPY :
            Enum\PaymentSetting\Currency::USD;
    }

}
