<?php
App::uses('Component', 'Controller');
/**
 * Class MentionComponent
 */
class MentionComponent extends Component {
    private static $PREFIX = '%%%';
    private static $SUFFIX = '%%%';
    private static $USER_ID_PREFIX = 'user';
    private static $CIRCLE_ID_PREFIX = 'circle';
    private static $ID_DELIMITER = '_';
    private static function getMentionReg(string $pattern, string $option) {
        return '/' . self::$PREFIX . $pattern . self::$SUFFIX . '/' . $option;
    }
    /**
     * extract any kinds of ID from content.
     *
     * @param $text string content of Post/Action/Comment
     * @return array 
     */
    static function extractAllIdFromMention(string $text = null): array {
        $result = array();
        if (preg_match_all(self::getMentionReg('(.*?)', 'm'), $text, $matches) && count($matches[1]) > 0) {
            foreach ($matches[1] as $match) {
                $isUser = strpos($match, self::$USER_ID_PREFIX) === 0;
                $isCircle = strpos($match, self::$CIRCLE_ID_PREFIX) === 0;
                $replacement = '';
                if ($isUser) {
                    $replacement = self::$USER_ID_PREFIX.self::$ID_DELIMITER;
                }else if ($isCircle) {
                    $replacement = self::$CIRCLE_ID_PREFIX.self::$ID_DELIMITER;
                }
                $result[$match] = array(
                    // $match will be like "user_1:user_name".
                    // Explode it at first and replace it with $replacement
                    // so that we can return just an ID itself.
                    'id' => explode(':', str_replace($replacement, '', $match))[0],
                    'isUser' => $isUser, 
                    'isCircle' => $isCircle
                );
            }
        }
        return $result;
    }
    /**
     * replace a mention expression with params below. 
     *
     * @param $pattern string a regular expression to replace
     * @param $replacement string a replacement to replace $pattern with
     * @param $subject string a subject to replace 
     * @return string 
     */
    static function replaceAndAddNameToMention(string $pattern, $replacement, string $subject = null): string {
        $result = preg_replace(self::getMentionReg($pattern, 'm'), self::$PREFIX.$pattern.':'.$replacement.self::$SUFFIX, $subject);
        return $result;
    }
    /**
     * replace all mentions in content with HTML expression.
     *
     * @param $text string the content should be replaced
     * @param $mentions array[string] mentions should be replaced
     * @return string 
     */
    public function replaceMention(string $text = null, array $mentions): string {
        $result = $text;
        foreach ($mentions as $mention) {
            $result = preg_replace(self::getMentionReg($mention.':(.*?)', 'm'), '<b><i class="mention to-me">@${1}</i></b>', $result);
        }
        $result = preg_replace(self::getMentionReg('.*?:(.*?)', 'm'), '<b><i class="mention">@${1}</i></b>', $result);
        return $result;
    }
    /**
     * a shortcut method to get belongings
     * 
     * @param $body string the content which can contain mentions
     * @param $userId int a userId which should be recognized as myself
     * @param $teamId int the team ID to identify the circle uniquely
     * @return array
     */
    public function getMyMentions(string $body = null, int $userId, int $teamId): array {
        return $this->getUserList($body, $teamId, $userId, true, true);
    }
    /**
     * append the name to the ID in each mention in the content
     * 
     * @param $body string the content which can contain mentions
     * @return string
     */
    static public function appendName(string $body = null): string {
        $matches = self::extractAllIdFromMention($body);
        if (count($matches) > 0) {
            $cache = array();
            foreach ($matches as $key => $match) {
                $replacementName = 'name';
                $model = null;
                if ($match['isUser'] === true) {
                    $model = ClassRegistry::init('PlainUser');
                    $replacementName = 'display_username';
                    $model->alias = 'User';
                }else if ($match['isCircle'] === true) {
                    $model = ClassRegistry::init('Circle');
                }
                if (!is_null($model)) {
                    // ExtContainableBehavior set del_flg false by default
                    // However we want to get this even if it is deleted
                    $data = $model->find('first', array(
                        'conditions' => array('id' => $match['id'], 'del_flg' => [true, false])
                    ));
                    $obj = $data[$model->alias];
                    $replacement = $obj[$replacementName];
                    $body = self::replaceAndAddNameToMention($key, $replacement, $body);
                }
            }
        }
        return $body;
    }
    /**
     * get user id list or id list of user/circle which contains $userId.
     *
     * @param $body string content of Post/Action/Comment
     * @param $teamId int the team ID to identify the circle uniquely
     * @param $me int the user ID to decide to exlude or include the user itself
     * @param $includeMe boolean whether the result should include $me or not
     * @param $returnAsBelonging boolean whether the result should be user/circle which contains $me
     */
    public function getUserList(string $body = null, int $teamId, int $me, $includeMe = false, $returnAsBelonging = false): array {
        $mentions = self::extractAllIdFromMention($body);
        $result = array();
        
        foreach ($mentions as $key => $mention) {
            if ($mention['isUser']) {
                $userId = $mention['id'];
                if ($returnAsBelonging && $userId != $me) continue;
                $result[] = $returnAsBelonging ? self::$USER_ID_PREFIX.self::$ID_DELIMITER.$userId : $userId;
            }else if($mention['isCircle']) {
                $notifyCircles[] = $mention['id'];
            }
        }
        if (!empty($notifyCircles)) {
            foreach ($notifyCircles as $circleId) {
                $CircleMember = ClassRegistry::init('CircleMember');
                $circleMembers = $CircleMember->getMembers($circleId, true);
                foreach ($circleMembers as $member) {
                    $userId = $member['CircleMember']['user_id'];
                    if ($returnAsBelonging && $userId != $me) continue;
                    if ($includeMe || $userId != $me) {
                        $result[] = $returnAsBelonging ? self::$CIRCLE_ID_PREFIX.self::$ID_DELIMITER.$circleId : $userId;
                    }
                }
            }
        }
        return $result;
    }
    private function filterAsMentionable(string $type, int $postId, array $list = []) {
        $model = ClassRegistry::init('PostShare'.$type);
        $model->displayField = strtolower($type).'_id';
        $data = $model->find('list', [
            'conditions' => array('post_id', $postId)
        ]);
        $data = array_values($data);
        $result = array_filter($list, function($l) {
            return array_search($l['id'], $data) !== false;
        });
        return $result;
    }
    public function filterAsMentionableUser(int $postId, array $list = []) {
        $filtered = $this->filterAsMentionable('User', $postId, $list);
    }
    public function filterAsMentionableCircle(int $postId, array $list = []) {
        return $this->filterAsMentionable('Circle', $postId, $list);
    }
}
