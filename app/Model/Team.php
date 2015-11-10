<?php
App::uses('AppModel', 'Model');

/**
 * Team Model
 *
 * @property Badge             $Badge
 * @property Circle            $Circle
 * @property CommentLike       $CommentLike
 * @property CommentMention    $CommentMention
 * @property CommentRead       $CommentRead
 * @property Comment           $Comment
 * @property GivenBadge        $GivenBadge
 * @property Group             $Group
 * @property Invite            $Invite
 * @property JobCategory       $JobCategory
 * @property PostLike          $PostLike
 * @property PostMention       $PostMention
 * @property PostRead          $PostRead
 * @property Post              $Post
 * @property TeamMember        $TeamMember
 * @property Thread            $Thread
 * @property Evaluator         $Evaluator
 * @property EvaluationSetting $EvaluationSetting
 * @property Evaluation        $Evaluation
 * @property EvaluateTerm      $EvaluateTerm
 * @property TeamVision        $TeamVision
 * @property GroupVision       $GroupVision
 * @property TeamInsight       $TeamInsight
 * @property GroupInsight      $GroupInsight
 * @property CircleInsight     $CircleInsight
 * @property AccessUser        $AccessUser
 */
class Team extends AppModel
{
    /**
     * タイプ
     */
    const TYPE_FREE = 1;
    const TYPE_PRO = 2;
    static public $TYPE = [self::TYPE_FREE => "", self::TYPE_PRO => ""];
    const OPTION_CHANGE_TERM_FROM_CURRENT = 1;
    const OPTION_CHANGE_TERM_FROM_NEXT = 2;
    static public $OPTION_CHANGE_TERM = [
        self::OPTION_CHANGE_TERM_FROM_CURRENT => "",
        self::OPTION_CHANGE_TERM_FROM_NEXT    => ""
    ];

    /**
     * タイプの名前をセット
     */
    private function _setTypeName()
    {
        self::$TYPE[self::TYPE_FREE] = __d('gl', "フリー");
        self::$TYPE[self::TYPE_PRO] = __d('gl', "プロ");
    }

    private function _setTermOptionName()
    {
        self::$OPTION_CHANGE_TERM[self::OPTION_CHANGE_TERM_FROM_CURRENT] = __d('gl', "今期から");
        self::$OPTION_CHANGE_TERM[self::OPTION_CHANGE_TERM_FROM_NEXT] = __d('gl', "来期から");
    }

    /**
     * Display field
     *
     * @var string
     */
    public $displayField = 'name';

    public $actsAs = [
        'Upload' => [
            'photo' => [
                'styles'      => [
                    'small'        => '32x32',
                    'medium'       => '48x48',
                    'medium_large' => '96x96',
                    'large'        => '128x128',
                    'x_large'      => '256x256',
                ],
                'path'        => ":webroot/upload/:model/:id/:hash_:style.:extension",
                'default_url' => 'no-image-team.jpg',
                'quality'     => 100,
            ]
        ]
    ];
    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'name'               => ['notEmpty' => ['rule' => ['notEmpty'],],],
        'type'               => ['numeric' => ['rule' => ['numeric'],],],
        'domain_limited_flg' => ['boolean' => ['rule' => ['boolean'],],],
        'start_term_month'   => ['numeric' => ['rule' => ['numeric'],],],
        'border_months'      => ['numeric' => ['rule' => ['numeric'],],],
        'del_flg'            => ['boolean' => ['rule' => ['boolean'],],],
        'photo'              => [
            'image_max_size' => ['rule' => ['attachmentMaxSize', 10485760],], //10mb
            'image_type'     => ['rule' => ['attachmentContentType', ['image/jpeg', 'image/gif', 'image/png']],]
        ],
        'emails'             => [
            'notEmpty'    => ['rule' => ['notEmpty'],],
            'emailsCheck' => [
                'rule' => ['emailsCheck']
            ],
        ],
        'comment'            => [
            'isString' => [
                'rule'       => ['isString',],
                'allowEmpty' => true,
            ],
        ]
    ];

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [];

    /**
     * hasMany associations
     *
     * @var array
     */
    public $hasMany = [
        'Badge',
        'Circle',
        'CommentLike',
        'CommentMention',
        'CommentRead',
        'Comment',
        'GivenBadge',
        'Group',
        'Invite',
        'JobCategory',
        'PostLike',
        'PostMention',
        'PostRead',
        'Post',
        'TeamMember',
        'Thread',
        'Evaluator',
        'Evaluation',
        'EvaluateTerm',
        'EvaluationSetting',
        'EvaluateTerm',
        'TeamVision',
        'GroupVision',
        'TeamInsight',
        'GroupInsight',
        'CircleInsight',
        'AccessUser',
    ];

    public $current_team = [];

    function __construct($id = false, $table = null, $ds = null)
    {
        parent::__construct($id, $table, $ds);
        $this->_setTypeName();
        $this->_setTermOptionName();
    }

    /**
     * @param array  $postData
     * @param string $uid
     *
     * @return array|bool
     */
    function add($postData, $uid)
    {
        $this->set($postData);
        if (!$this->validates()) {
            return false;
        }
        $team_member = [
            'TeamMember' => [
                [
                    'user_id'   => $uid,
                    'admin_flg' => true,
                ]
            ]
        ];
        $postData = array_merge($postData, $team_member);
        $this->saveAll($postData);
        //デフォルトチームを更新
        $user = $this->TeamMember->User->findById($uid);
        if (isset($user['User']) && !$user['User']['default_team_id']) {
            $this->TeamMember->User->id = $uid;
            $this->TeamMember->User->saveField('default_team_id', $this->id);
        }

        // 「チーム全体」サークルを追加
        $circleData = [
            'Circle'       => [
                'team_id'      => $this->id,
                'name'         => __d('gl', 'チーム全体'),
                'description'  => __d('gl', 'チーム全体'),
                'public_flg'   => true,
                'team_all_flg' => true,
            ],
            'CircleMember' => [
                [
                    'team_id'   => $this->id,
                    'user_id'   => $uid,
                    'admin_flg' => true,
                ]
            ]
        ];
        if ($this->Circle->saveAll($circleData)) {
            // サークルメンバー数を更新
            // 新しく追加したチームのサークルなので current_team_id を一時的に変更する
            $tmp = $this->Circle->CircleMember->current_team_id;
            $this->Circle->CircleMember->current_team_id = $this->id;
            $this->Circle->CircleMember->updateCounterCache(['circle_id' => $this->Circle->getLastInsertID()]);
            $this->Circle->CircleMember->current_team_id = $tmp;
            //cache削除
            Cache::delete($this->getCacheKey(CACHE_KEY_TEAM_LIST, true, null, false), 'team_info');
        }
        return true;
    }

    function getBorderMonthsOptions()
    {
        $term_options = [
            null => __d('gl', "選択してください"),
            3    => __d('gl', "四半期"),
            6    => __d('gl', "半年"),
            12   => __d('gl', "年")
        ];
        return $term_options;
    }

    function getMonths()
    {
        $months = [
            null => __d('gl', "選択して下さい"),
            1    => __d('gl', "１月"),
            2    => __d('gl', "２月"),
            3    => __d('gl', "３月"),
            4    => __d('gl', "４月"),
            5    => __d('gl', "５月"),
            6    => __d('gl', "６月"),
            7    => __d('gl', "７月"),
            8    => __d('gl', "８月"),
            9    => __d('gl', "９月"),
            10   => __d('gl', "１０月"),
            11   => __d('gl', "１１月"),
            12   => __d('gl', "１２月"),
        ];
        return $months;
    }

    /**
     * @param $data
     *
     * @return null
     */
    function getEmailListFromPost($data)
    {
        if (!isset($data['Team']['emails'])) {
            return null;
        }
        $this->set($data);
        if (!$this->validates()) {
            return null;
        }
        $res = $this->extractEmail($data['Team']['emails']);
        return $res;
    }

    /**
     * @param $emails
     *
     * @return array
     */
    function extractEmail($emails)
    {
        $res = [];
        //一行ずつ処理
        $cr = array("\r\n", "\r"); // 改行コード置換用配列を作成しておく

        $emails = trim($emails); // 文頭文末の空白を削除

        // 改行コードを統一
        //str_replace ("検索文字列", "置換え文字列", "対象文字列");
        $emails = str_replace($cr, "\n", $emails);

        //改行コードで分割（結果は配列に入る）
        $lines_array = explode("\n", $emails);
        //一行ずつ処理
        foreach ($lines_array as $line) {
            //カンマで分割
            $emails = explode(",", $line);
            //メールアドレス毎に処理
            foreach ($emails as $email) {
                //全角スペースを除去
                $email = preg_replace('/　/', ' ', $email);
                //前後スペースを除去
                $email = trim($email);
                //空行はスキップ
                if (empty($email)) {
                    continue;
                }
                if (!in_array($email, $res)) {
                    $res[] = $email;
                }
            }
        }
        return $res;
    }

    /**
     * @param $team_id
     * @param $post_data
     *
     * @return bool
     */
    function saveEditTerm($team_id, $post_data)
    {
        $this->id = $team_id;
        if (!$this->save($post_data)) {
            return false;
        }
        $current_term_id = $this->EvaluateTerm->getCurrentTermId();
        $next_term_id = $this->EvaluateTerm->getNextTermId();
        if (!$current_term_id || !$next_term_id) {
            return false;
        }
        if (viaIsSet($post_data['Team']['change_from']) == Team::OPTION_CHANGE_TERM_FROM_CURRENT &&
            $this->EvaluateTerm->isStartedEvaluation($current_term_id)
        ) {
            return false;
        }

        $res = $this->EvaluateTerm->updateTermData(
            $post_data['Team']['change_from'],
            $post_data['Team']['start_term_month'],
            $post_data['Team']['border_months'],
            $post_data['Team']['timezone']
        );
        //キャッシュを削除
        Cache::delete($this->getCacheKey(CACHE_KEY_TERM_CURRENT), 'team_info');
        Cache::delete($this->getCacheKey(CACHE_KEY_TERM_NEXT), 'team_info');
        Cache::delete($this->getCacheKey(CACHE_KEY_TERM_PREVIOUS), 'team_info');
        return (bool)$res;
    }

    /**
     * @return null
     */
    function getCurrentTeam()
    {
        if (empty($this->current_team)) {
            $this->current_team = $this->findById($this->current_team_id);
        }
        return $this->current_team;
    }

    /**
     * チームのリストを取得する
     */
    function getList()
    {
        return $this->find('list');
    }

    /**
     * チームを削除する
     *
     * @param $team_id
     *
     * @return bool
     */
    function deleteTeam($team_id)
    {
        $this->delete($team_id);

        // delete() の戻り値が soft delete で false になってしまうので、
        // 削除されたか自前で確認する
        $row = $this->findById($team_id);
        return $row ? false : true;
    }
}
