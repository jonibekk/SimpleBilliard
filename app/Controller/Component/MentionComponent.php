<?php
App::uses('Component', 'Controller');
App::uses('TeamMember', 'Model');

/**
 * Class MentionComponent
 */
class MentionComponent extends Component
{

    private static $PREFIX = '%%%';
    private static $SUFFIX = '%%%';
    private static $USER_ID_PREFIX = 'user';
    private static $CIRCLE_ID_PREFIX = 'circle';
    private static $ID_DELIMITER = '_';

    private static function getMentionReg(string $pattern, string $option)
    {
        return '/' . self::$PREFIX . $pattern . self::$SUFFIX . '/' . $option;
    }

    /**
     * extract any kinds of ID from content.
     *
     * @param $text string content of Post/Action/Comment
     *
     * @return array
     */
    static function extractAllIdFromMention(string $text = null): array
    {
        $result = array();
        if (preg_match_all(self::getMentionReg('(.*?)', 'm'), $text, $matches) && count($matches[1]) > 0) {
            foreach ($matches[1] as $match) {
                $isUser = strpos($match, self::$USER_ID_PREFIX) === 0;
                $isCircle = strpos($match, self::$CIRCLE_ID_PREFIX) === 0;
                $replacement = '';
                if ($isUser) {
                    $replacement = self::$USER_ID_PREFIX . self::$ID_DELIMITER;
                } else {
                    if ($isCircle) {
                        $replacement = self::$CIRCLE_ID_PREFIX . self::$ID_DELIMITER;
                    }
                }
                $result[$match] = array(
                    // $match will be like "user_1:user_name".
                    // Explode it at first and replace it with $replacement
                    // so that we can return just an ID itself.
                    'id'       => explode(':', str_replace($replacement, '', $match))[0],
                    'isUser'   => $isUser,
                    'isCircle' => $isCircle
                );
            }
        }
        return $result;
    }

    /**
     * replace a mention expression with params below.
     *
     * @param $pattern     string a regular expression to replace
     * @param $replacement string a replacement to replace $pattern with
     * @param $subject     string a subject to replace
     *
     * @return string
     */
    static function replaceAndAddNameToMention(string $pattern, $replacement, string $subject = null): string
    {
        $result = preg_replace(self::getMentionReg($pattern, 'm'),
            self::$PREFIX . $pattern . ':' . $replacement . self::$SUFFIX, $subject);
        return $result;
    }

    /**
     * Replace all mentions in content with HTML expression.
     *
     * @param $text     string the content should be replaced
     * @param $mentions array[string] mentions should be replaced
     *
     * @return string
     */
    public function replaceMention(string $text = null, array $mentions = [], $plain = false): string
    {
        $result = $text;
        foreach ($mentions as $mention) {
            $result = preg_replace(self::getMentionReg($mention . ':(.*?)', 'm'),
                !$plain ? '<b><i class="mention to-me">@${1}</i></b>' : '@${1}', $result);
        }
        $result = preg_replace(self::getMentionReg('.*?:(.*?)', 'm'),
            !$plain ? '<b><i class="mention">@${1}</i></b>' : '@${1}', $result);
        return $result;
    }

    /**
     * replace all mentions in content with HTML expression.
     *
     * @param $text     string the content should be replaced
     *
     * @return string
     */
    public static function replaceMentionForTranslation(string $text): string
    {
        $result = $text;
        $result = preg_replace(self::getMentionReg('(.*?):(.*?)', 'm'),
            '<span class="mention" translate="no" mention="${1}">${2}</span>', $result);
        return $result;
    }

    /**
     * Replace all mention HTML tags in content with mention string for API v1
     *
     * @param $text     string the content should be replaced
     *
     * @return string
     */
    public static function replaceMentionTagForTranslationV1(string $text): string
    {
        $result = $text;
        $result = preg_replace('/<span class="mention" translate="no" mention=".*?">(.*?)<\/span>/m',
            '<span class="mention">@${1}</span>', $result);
        return $result;
    }

    /**
     * Replace all mention HTML tags in content with mention string for API v2
     *
     * @param $text     string the content should be replaced
     *
     * @return string
     */
    public static function replaceMentionTagForTranslationV2(string $text): string
    {
        $result = $text;
        $result = preg_replace('/<span class="mention" translate="no" mention="(.*?)">(.*?)<\/span>/m',
            self::$PREFIX . '${1}:${2}' . self::$SUFFIX, $result);
        return $result;
    }

    /**
     * replace all mentions to plain text that human can read.
     * e.g.
     * before: `%%%circle_104:テストサークル%%% %%%user_1:山田 太郎%%%　いいね！`
     * after: `いいね！`
     *
     * @param $text     string the content should be replaced
     *
     * @return string
     */
    static public function replaceMentionToSimpleReadable(string $text): string
    {
        return preg_replace("/" . self::$PREFIX . "(user|circle)_(\d+):(.*?)" . self::$SUFFIX . "/", '', $text);
    }

    /**
     * a shortcut method to get belongings
     *
     * @param $body   string the content which can contain mentions
     * @param $userId int a userId which should be recognized as myself
     * @param $teamId int the team ID to identify the circle uniquely
     *
     * @return array
     */
    public function getMyMentions(string $body = null, int $userId, int $teamId): array
    {
        return $this->getUserList($body, $teamId, $userId, true, true);
    }

    /**
     * append the name to the ID in each mention in the content
     *
     * @param $type               string the type of the content
     * @param $accessControlledId int the ID used for access control to show mentions
     * @param $body               string the content which can contain mentions
     *
     * @return string
     */
    public static function appendName(string $type, int $accessControlledId, string $body = null): string
    {
        $matches = self::extractAllIdFromMention($body);
        if (count($matches) > 0) {
            $cache = array();
            foreach ($matches as $key => $match) {
                $replacementName = 'name';
                $model = null;
                if ($match['isUser'] === true) {
                    $checked = self::filterAsMentionableUser($accessControlledId, [['id' => $match['id']]]);
                    if (!count($checked)) {
                        continue;
                    }
                    $model = ClassRegistry::init('PlainUser');
                    $replacementName = 'display_username';
                    $model->alias = 'User';
                } else {
                    if ($match['isCircle'] === true) {
                        $checked = self::filterAsMentionableCircle($accessControlledId, [['id' => $match['id']]]);
                        if (!count($checked)) {
                            continue;
                        }
                        $model = ClassRegistry::init('PlainCircle');
                    }
                }
                if (!is_null($model)) {
                    // ExtContainableBehavior set del_flg false by default
                    // However we want to get this even if it is deleted
                    $data = $model->find('first', array(
                        'conditions' => array('id' => $match['id'], 'del_flg' => [true, false])
                    ));
                    $obj = $data[$model->alias];
                    $replacement = $obj[$replacementName];
                    if ($replacement) {
                        $body = self::replaceAndAddNameToMention($key, $replacement, $body);
                    }
                }
            }
        }
        return $body;
    }

    /**
     * get user id list or id list of user/circle which contains $userId.
     *
     * @param $body              string content of Post/Action/Comment
     * @param $teamId            int the team ID to identify the circle uniquely
     * @param $me                int the user ID to decide to exlude or include the user itself
     * @param $includeMe         boolean whether the result should include $me or not
     * @param $returnAsBelonging boolean whether the result should be user/circle which contains $me
     */
    public function getUserList(
        string $body = null,
        int $teamId,
        $me,
        $includeMe = false,
        $returnAsBelonging = false
    ): array
    {
        $mentions = self::extractAllIdFromMention($body);
        $result = array();

        $userIds = [];
        foreach ($mentions as $key => $mention) {
            if ($mention['isUser']) {
                $userId = $mention['id'];
                if ($returnAsBelonging && $userId != $me) {
                    continue;
                }
                $userIds[] = $userId;
            } else {
                if ($mention['isCircle']) {
                    $notifyCircles[] = $mention['id'];
                }
            }
        }
        if (!empty($userIds)) {
            /* @var TeamMember $TeamMember */
            $TeamMember = ClassRegistry::init('TeamMember');
            $userIds = $TeamMember->filterActiveMembers($userIds, $teamId);

            foreach ($userIds as $userId) {
                $result[] = $returnAsBelonging ? self::$USER_ID_PREFIX . self::$ID_DELIMITER . $userId : $userId;
            }
        }

        if (!empty($notifyCircles)) {
            foreach ($notifyCircles as $circleId) {
                /* @var PlainCircle $CircleMember */
                $CircleMember = ClassRegistry::init('PlainCircle');
                $circleMemberIds = $CircleMember->getMembers($circleId);
                foreach ($circleMemberIds as $userId) {
                    if ($returnAsBelonging && $userId != $me) {
                        continue;
                    }
                    if ($includeMe || $userId != $me) {
                        $result[] = $returnAsBelonging ? self::$CIRCLE_ID_PREFIX . self::$ID_DELIMITER . $circleId : $userId;
                    }
                }
            }
        }
        $result = array_unique($result);
        return $result;
    }

    /**
     * get mention target ids each type (user/circle)
     *
     * @param $body              string content of Post/Action/Comment
     * @param $teamId            int the team ID to identify the circle uniquely
     *
     * @return array
     */
    public function getTargetIdsEachType(
        string $body = null,
        int $teamId
    ): array
    {
        $mentions = self::extractAllIdFromMention($body);

        $userIds = [];
        $circleIds = [];
        foreach ($mentions as $key => $mention) {
            if ($mention['isUser']) {
                $userIds[] = $mention['id'];
            } else {
                if ($mention['isCircle']) {
                    $circleIds[] = $mention['id'];
                }
            }
        }
        if (!empty($userIds)) {
            /* @var TeamMember $TeamMember */
            $TeamMember = ClassRegistry::init('TeamMember');
            $userIds = $TeamMember->filterActiveMembers($userIds, $teamId);
        }
        if (!empty($circleIds)) {
            /* @var Circle $Circle */
            $Circle = ClassRegistry::init('Circle');
            $circles = $Circle->find('all', [
                'fields'     => 'id',
                'conditions' => [
                    'id' => $circleIds
                ]
            ]);
            $circleIds = Hash::extract($circles, '{n}.Circle.id');
        }
        return [
            'circle' => $circleIds,
            'user'   => $userIds
        ];
    }

    static private function getPostWithShared(int $postId): array
    {
        $postModel = ClassRegistry::init('PlainPost');
        $shareCircles = $postModel->find('all', [
            'conditions' => ['PlainPost.id' => $postId],
            'joins'      => [
                [
                    'table'      => 'post_share_circles',
                    'type'       => 'LEFT',
                    'alias'      => 'PostShareCircle',
                    'foreignKey' => false,
                    'conditions' => [
                        'PlainPost.id = PostShareCircle.post_id',
                    ]
                ],
            ],
            'fields'     => [
                'PlainPost.type',
                'PostShareCircle.circle_id'
            ]
        ]);
        $shareUsers = $postModel->find('all', [
            'conditions' => ['PlainPost.id' => $postId],
            'joins'      => [
                [
                    'table'      => 'post_share_users',
                    'alias'      => 'PostShareUser',
                    'foreignKey' => false,
                    'conditions' => [
                        'PlainPost.id = PostShareUser.post_id',
                    ]
                ],
            ],
            'fields'     => [
                'PostShareUser.user_id'
            ]
        ]);
        return ['PostShareCircles' => $shareCircles, 'PostShareUsers' => $shareUsers];
    }

    static public function filterAsMentionableUser(int $postId, array $list = [])
    {
        $post = self::getPostWithShared($postId);
        $filterMembers = [];
        foreach ($post['PostShareCircles'] as $circle) {
            $circleModel = ClassRegistry::init('PlainCircle');
            $circleId = $circle['PostShareCircle']['circle_id'];
            $Post = ClassRegistry::init('Post');
            $actionRelated = [$Post::TYPE_CREATE_GOAL, $Post::TYPE_ACTION, $Post::TYPE_KR_COMPLETE, $Post::TYPE_GOAL_COMPLETE];
            if (in_array($circle['PlainPost']['type'], $actionRelated)){
                return $list;
            }
            if (is_null($circleId)){
                continue;
            }
            $circleData = $circleModel->findById($circleId);
            $isPublic = $circleData['PlainCircle']['public_flg'];
            // in public circle comments, we can show everyone.
            // so no need to filter by anything
            if ($isPublic) return $list;
            $circleMembers = $circleModel->getMembers($circleId);
            $members = array();
            foreach ($circleMembers as $userId) {
                $members[] = $userId;
            }
            $filterMembers = array_merge($filterMembers, $members);
        }
        foreach ($post['PostShareUsers'] as $user) {
            $filterMembers[] = $user['PostShareUser']['user_id'];
        }
        $filterMembers = array_unique($filterMembers);
        if (count($filterMembers) == 0) {
            return $list;
        }
        $result = array_filter($list, function ($l) use ($filterMembers) {
            return in_array(str_replace(self::$USER_ID_PREFIX . self::$ID_DELIMITER, '', $l['id']), $filterMembers);
        });
        return array_values($result);
    }

    static public function filterAsMentionableCircle(int $postId, array $list = [])
    {
        $post = self::getPostWithShared($postId);
        $publicCircles = [];
        $secretCircles = [];
        $isActionRelated = false;
        foreach ($post['PostShareCircles'] as $circle) {
            $circleModel = ClassRegistry::init('PlainCircle');
            $circleId = $circle['PostShareCircle']['circle_id'];

            $Post = ClassRegistry::init('Post');
            $actionRelated = [$Post::TYPE_CREATE_GOAL, $Post::TYPE_ACTION, $Post::TYPE_KR_COMPLETE, $Post::TYPE_GOAL_COMPLETE];
            if (in_array($circle['PlainPost']['type'], $actionRelated)){
                $isActionRelated = true;
                break;
            }
            if (is_null($circleId)){
                continue;
            }
            $circleData = $circleModel->findById($circleId);
            $isPublic = $circleData['PlainCircle']['public_flg'];
            if ($isPublic) {
                $publicCircles[] = $circleId;
            } else {
                $secretCircles[] = $circleId;
            }
        }
        if (count($publicCircles) > 0 || $isActionRelated) {
            $circleModel = ClassRegistry::init('PlainCircle');
            $ids = array_map(function ($l) {
                return str_replace(self::$CIRCLE_ID_PREFIX . self::$ID_DELIMITER, '', $l['id']);
            }, $list);
            $filtered = $circleModel->find('list', [
                'conditions' => ['id' => $ids, 'public_flg' => true]
            ]);
            if (count($filtered) == 0) {
                return [];
            }
            $filtered = array_keys($filtered);
            return array_values(array_filter($list, function ($l) use ($filtered) {
                return in_array(str_replace(self::$CIRCLE_ID_PREFIX . self::$ID_DELIMITER, '', $l['id']), $filtered);
            }));
        }
        return array_values(array_filter($list, function ($l) use ($secretCircles) {
            return in_array(str_replace(self::$CIRCLE_ID_PREFIX . self::$ID_DELIMITER, '', $l['id']), $secretCircles);
        }));
    }
}
