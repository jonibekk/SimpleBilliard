<?php
App::uses('TextUtil', 'Lib/Util');
/**
 * Class MessageService
 */
class MentionComponent extends Component {
    public function getUserList($body, $me) {
        $mentions = TextUtil::extractAllIdFromMention($body);
        $notifyUsers = array();
        foreach ($mentions as $key => $mention) {
            if ($mention['isUser']) {
                $notifyUsers[] = $mention['id'];
            }else if($mention['isCircle']) {
                $notifyCircles[] = $mention['id'];
            }
        }
        if (!empty($notifyCircles)) {
            foreach ($notifyCircles as $circleId) {
                $CircleMember = ClassRegistry::init('CircleMember');
                $circle_members = CircleMember()->getMembers($circleId, true);
                foreach ($circle_members as $member) {
                    $userId = $member['CircleMember']['user_id'];
                    if ($userId != $me) {
                        $notifyUsers[] = $userId;
                    }
                }
            }
        }
        return $notifyUsers;
    }
}
