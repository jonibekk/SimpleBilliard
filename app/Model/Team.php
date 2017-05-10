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
 * @property Evaluator         $Evaluator
 * @property EvaluationSetting $EvaluationSetting
 * @property Evaluation        $Evaluation
 * @property Term              $Term
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
     * Type | タイプ
     */
    // const TYPE_FREE = 1;
    // const TYPE_PRO = 2;
    const TYPE_CAMPAIGN = 3;
    static public $TYPE = [
        // self::TYPE_FREE => "",
        // self::TYPE_PRO => "",
        self::TYPE_CAMPAIGN => ""
    ];
    const OPTION_CHANGE_TERM_FROM_CURRENT = 1;
    const OPTION_CHANGE_TERM_FROM_NEXT = 2;
    static public $OPTION_CHANGE_TERM = [
        self::OPTION_CHANGE_TERM_FROM_CURRENT => "",
        self::OPTION_CHANGE_TERM_FROM_NEXT    => ""
    ];

    /**
     * Set Type name | タイプの名前をセット
     */
    private function _setTypeName()
    {
        self::$TYPE[self::TYPE_CAMPAIGN] = __("Free Campaign");
        // self::$TYPE[self::TYPE_FREE] = __("フリー");
        // self::$TYPE[self::TYPE_PRO] = __("プロ");
    }

    private function _setTermOptionName()
    {
        self::$OPTION_CHANGE_TERM[self::OPTION_CHANGE_TERM_FROM_CURRENT] = __("From this term");
        self::$OPTION_CHANGE_TERM[self::OPTION_CHANGE_TERM_FROM_NEXT] = __("From next term");
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
        'name'               => [
            'isString'  => [
                'rule' => ['isString',],
            ],
            'maxLength' => ['rule' => ['maxLength', 128]],
            'notBlank'  => ['rule' => ['notBlank'],],
        ],
        'type'               => ['numeric' => ['rule' => ['numeric'],],],
        'change_from'        => [
            'numeric' => [
                'rule'       => [
                    'inList',
                    [
                        self::OPTION_CHANGE_TERM_FROM_CURRENT,
                        self::OPTION_CHANGE_TERM_FROM_NEXT
                    ]
                ],
                'allowEmpty' => true,
            ],
        ],
        'timezone'           => [
            'numeric' => [
                'rule'       => ['numeric'],
                'allowEmpty' => true,
            ],
        ],
        'domain_limited_flg' => ['boolean' => ['rule' => ['boolean'],],],
        'start_term_month'   => ['numeric' => ['rule' => ['numeric'],],],
        'border_months'      => ['numeric' => ['rule' => ['numeric'],],],
        'del_flg'            => ['boolean' => ['rule' => ['boolean'],],],
        'photo'              => [
            'image_max_size'  => ['rule' => ['attachmentMaxSize', 10485760],], //10mb
            'image_type'      => ['rule' => ['attachmentImageType',],],
            'canProcessImage' => ['rule' => 'canProcessImage',],
        ],
        'emails'             => [
            'notBlank'    => ['rule' => ['notBlank'],],
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
        'Evaluator',
        'Evaluation',
        'Term',
        'EvaluationSetting',
        'Term',
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
        // Update default team | デフォルトチームを更新
        $user = $this->TeamMember->User->findById($uid);
        if (isset($user['User']) && !$user['User']['default_team_id']) {
            $this->TeamMember->User->id = $uid;
            $this->TeamMember->User->saveField('default_team_id', $this->id);
        }

        // Add All team | 「チーム全体」サークルを追加
        $circleData = [
            'Circle'       => [
                'team_id'      => $this->id,
                'name'         => __('All Team'),
                'description'  => __('All Team'),
                'public_flg'   => true,
                'team_all_flg' => true,
            ],
            'CircleMember' => [
                [
                    'team_id'               => $this->id,
                    'user_id'               => $uid,
                    'admin_flg'             => true,
                    'show_for_all_feed_flg' => true,
                    'get_notification_flg'  => true,
                ]
            ]
        ];
        if ($this->Circle->saveAll($circleData)) {
            // Update circle members number | サークルメンバー数を更新
            // temporarily changed current_team_id | 新しく追加したチームのサークルなので current_team_id を一時的に変更する
            $tmp = $this->Circle->CircleMember->current_team_id;
            $this->Circle->CircleMember->current_team_id = $this->id;
            $this->Circle->CircleMember->updateCounterCache(['circle_id' => $this->Circle->getLastInsertID()]);
            $this->Circle->CircleMember->current_team_id = $tmp;
            // cache clear | cache削除
            Cache::delete($this->getCacheKey(CACHE_KEY_TEAM_LIST, true, null, false), 'team_info');
        }
        return true;
    }

    function getBorderMonthsOptions()
    {
        $term_options = [
            null => __("Please select"),
            3    => __("Quater"),
            6    => __("Half a year"),
            12   => __("Year")
        ];
        return $term_options;
    }

    function getMonths()
    {
        $months = [
            null => __("Please select"),
            1    => __("Jan"),
            2    => __("Feb"),
            3    => __("Mar"),
            4    => __("Apr"),
            5    => __("May"),
            6    => __("Jun"),
            7    => __("Jul"),
            8    => __("Aug"),
            9    => __("Sep"),
            10   => __("Oct"),
            11   => __("Nov"),
            12   => __("Dec"),
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
        if (is_array($data['Team']['emails'])) {
            $data['Team']['emails'] = array_filter($data['Team']['emails']);
            $validate_backup = $this->TeamMember->User->Email->validate;
            $this->TeamMember->User->Email->validate = [
                'email' => [
                    'maxLength' => ['rule' => ['maxLength', 200]],
                    'notBlank'  => ['rule' => 'notBlank',],
                    'email'     => ['rule' => ['email'],],
                ],
            ];
            foreach ($data['Team']['emails'] as $email) {
                $this->TeamMember->User->Email->create(['email' => $email]);
                if (!$this->TeamMember->User->Email->validates(['fieldList' => ['email']])) {
                    return null;
                }
            }
            $this->TeamMember->User->Email->validate = $validate_backup;

            return $data['Team']['emails'];
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
        $current_term_id = $this->Term->getCurrentTermId();
        $next_term_id = $this->Term->getNextTermId();
        if (!$current_term_id || !$next_term_id) {
            return false;
        }
        if (Hash::get($post_data, 'Team.change_from') == Team::OPTION_CHANGE_TERM_FROM_CURRENT &&
            $this->Term->isStartedEvaluation($current_term_id)
        ) {
            return false;
        }

        $res = $this->Term->updateTermData(
            $post_data['Team']['change_from'], $post_data['Team']['start_term_month'],
            $post_data['Team']['border_months']
        );
        //キャッシュを削除
        Cache::clear(false, 'team_info');
        Cache::clear(false, 'user_data');
        return (bool)$res;
    }

    /**
     * @return null
     */
    function getCurrentTeam()
    {
        if (empty($this->current_team)) {
            $model = $this;
            $this->current_team = Cache::remember($this->getCacheKey(CACHE_KEY_CURRENT_TEAM, false),
                function () use ($model) {
                    return $model->findById($model->current_team_id);
                }, 'team_info');
        }
        return $this->current_team;
    }

    /**
     * getting timezone
     *
     * @return mixed
     */
    function getTimezone()
    {
        return Hash::get($this->getCurrentTeam(), 'Team.timezone');
    }

    /**
     * チームのリストを取得する
     */
    function getListWithTeamId()
    {
        $out_list = [];
        $teamList = $this->find('list');
        foreach ($teamList as $id => $name) {
            $out_list[$id] = $id . '_' . $name;
        }
        return $out_list;
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
        try {
            $this->delete($team_id);
        } catch (PDOException $e) {
            return false;
        }

        // delete() の戻り値が soft delete で false になってしまうので、
        // 削除されたか自前で確認する
        $row = $this->findById($team_id);
        return $row ? false : true;
    }

    /**
     * 指定したタイムゾーン設定になっているチームのIDのリストを返す
     *
     * @param float $timezone
     *
     * @return array
     */
    public function findIdsByTimezone(float $timezone): array
    {
        $options = [
            'conditions' => [
                'timezone' => $timezone,
            ],
            'fields'     => [
                'id'
            ],
        ];
        $ret = $this->findWithoutTeamId('list', $options);
        // キーに特別な意味を持たせないように、歯抜けのキーを再採番
        $ret = array_merge($ret);
        return $ret;
    }

    /**
     * 期間データを持たないチームのIDを取得
     *
     * @param float $timezone
     * @param int   $timestamp
     *
     * @return array
     */
    public function findIdsNotHaveTerm(float $timezone, int $timestamp): array
    {
        $targetDate = AppUtil::dateYmdLocal($timestamp, $timezone);
        $options = [
            'conditions' => [
                'Team.timezone' => $timezone,
                'Term.id'       => null,
            ],
            'fields'     => [
                'id'
            ],
            'joins'      => [
                [
                    'table'      => 'terms',
                    'alias'      => 'Term',
                    'type'       => 'LEFT',
                    'conditions' => [
                        'Team.id = Term.team_id',
                        'Term.start_date <=' => $targetDate,
                        'Term.end_date >='   => $targetDate,
                        'Term.del_flg'       => false,
                    ]
                ],
            ],
        ];
        $ret = $this->findWithoutTeamId('list', $options);
        // キーに特別な意味を持たせないように、歯抜けのキーを再採番
        $ret = array_merge($ret);
        return $ret;

    }

}
