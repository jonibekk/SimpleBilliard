<?php
App::uses('TextUtil', 'Lib/Util');
/**
 * Class MessageService
 */
class MentionComponent extends Component {
    public function replaceMention($text) {
        $result = $this->appendName($text);
        $result = preg_replace('/%%%.*?:(.*?)%%%/m', '<b><i><@${1}></i></b>', $result);
        return $result;
    }
    public function isMentioned($body, $userId, $teamId) {
        $users = $this->getUserList($body, $teamId, $userId, true);
        return in_array($userId, $users);
    }
    public function appendName($body) {
        $matches = TextUtil::extractAllIdFromMention($body);
        if (count($matches) > 0) {
            $cache = array();
            foreach ($matches as $key => $match) {
                $replacementName = 'name';
                $model = null;
                if ($match['isUser'] === true) {
                    $model = ClassRegistry::init('User');
                    $replacementName = 'display_username';
                }else if ($match['isCircle'] === true) {
                    $model = ClassRegistry::init('Circle');
                }else if ($match['isGroup'] === true) {
                    $model = ClassRegistry::init('Group');
                }
                if (!is_null($model)) {
                    $data = $model->findById($match['id']);
                    $obj = $data[$model->alias];
                    $replacement = $obj[$replacementName];
                    $body = TextUtil::replaceAndAddNameToMention($key, $replacement, $body);
                }
            }
        }
        return $body;
    }
    public function getUserList($body, $my, $me, $all = false) {
        $mentions = TextUtil::extractAllIdFromMention($body);
        $notifyUsers = array();
        foreach ($mentions as $key => $mention) {
            if ($mention['isUser']) {
                $notifyUsers[] = explode(':', $mention['id'])[0];
            }else if($mention['isCircle']) {
                $notifyCircles[] = $mention['id'];
            }else if ($mention['isGroup']) {
                $notifyGroups[] = $mention['id'];
            }
        }
        if (!empty($notifyCircles)) {
            foreach ($notifyCircles as $circleId) {
                $CircleMember = ClassRegistry::init('CircleMember');
                $circle_members = $CircleMember->getMembers($circleId, true);
                foreach ($circle_members as $member) {
                    $userId = $member['CircleMember']['user_id'];
                    if ($all || $userId != $me) {
                        $notifyUsers[] = $userId;
                    }
                }
            }
        }
        if (!empty($notifyGroups)) {
            foreach ($notifyGroups as $groupId) {
                $MemberGroup = ClassRegistry::init('MemberGroup');
                $group_members = $MemberGroup->getGroupMemberUserId($my, $groupId);
                foreach ($group_members as $member) {
                    $userId = $member;
                    if ($all || $userId != $me) {
                        $notifyUsers[] = $userId;
                    }
                }
            }
        }
        return $notifyUsers;
    }
}
