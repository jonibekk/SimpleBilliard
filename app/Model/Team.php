<?php
App::uses('AppModel', 'Model');
App::import('Model/Entity', 'TeamEntity');

use Goalous\Enum as Enum;

/**
 * Team Model
 *
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
 * @property PaymentSetting    $PaymentSetting
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
     * Service use status
     */
    const SERVICE_USE_STATUS_FREE_TRIAL = 0;
    const SERVICE_USE_STATUS_PAID = 1;
    const SERVICE_USE_STATUS_READ_ONLY = 2;
    const SERVICE_USE_STATUS_CANNOT_USE = 3;

    /**
     * Team credit card status
     */
    const STATUS_CREDIT_CARD_CLEAR = 0;
    const STATUS_CREDIT_CARD_EXPIRED = 1;
    const STATUS_CREDIT_CARD_EXPIRE_SOON = 2;

    /**
     * Days of service use status
     */
    const DAYS_SERVICE_USE_STATUS = [
        self::SERVICE_USE_STATUS_FREE_TRIAL => 15,
        self::SERVICE_USE_STATUS_READ_ONLY  => 30,
        self::SERVICE_USE_STATUS_CANNOT_USE => 90,
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
        'border_months'      => ['numeric' => ['rule' => ['numeric'],],],
        'next_start_ym'      => ['dateYm' => ['rule' => ['date', 'ym'],],],
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
        'Circle',
        'CommentLike',
        'CommentMention',
        'CommentRead',
        'Comment',
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
        'PaymentSetting'
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
                    'status'    => TeamMember::USER_STATUS_ACTIVE
                ]
            ]
        ];
        $postData = array_merge($postData, $team_member);

        // set free trial start date and end date
        $postData['Team']['service_use_status'] = self::SERVICE_USE_STATUS_FREE_TRIAL;
        $postData['Team']['service_use_state_start_date'] = AppUtil::todayDateYmdLocal($postData['Team']['timezone']);
        $stateDays = self::DAYS_SERVICE_USE_STATUS[self::SERVICE_USE_STATUS_FREE_TRIAL];
        $stateEndDate = AppUtil::dateAfter($postData['Team']['service_use_state_start_date'],
            $stateDays);
        $postData['Team']['service_use_state_end_date'] = $stateEndDate;

        $this->saveAll($postData);
        // Update default team | デフォルトチームを更新
        $user = $this->TeamMember->User->findById($uid);
        if (isset($user['User']) && empty($user['User']['default_team_id'])) {
            $this->TeamMember->User->id = $uid;
            $this->TeamMember->User->updateDefaultTeam($this->id, true, $uid);
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
                    'maxLength' => ['rule' => ['maxLength', 255]],
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
     * TODO: move to service layter
     *
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
     * TODO: move to service layter
     *
     * @return null
     */
    function resetCurrentTeam()
    {
        $this->current_team = [];
        Cache::delete($this->getCacheKey(CACHE_KEY_CURRENT_TEAM, false), 'team_info');
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
     * update part of term settings
     *
     * @param int $startTermMonth
     * @param int $borderMonth
     *
     * @return bool
     */
    function updateTermSettings(int $startTermMonth, int $borderMonth): bool
    {
        $this->id = $this->current_team_id;
        if (!$this->saveField('start_term_month', $startTermMonth)) {
            return false;
        }
        if (!$this->saveField('border_months', $borderMonth)) {
            return false;
        }
        return true;
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
     * finding team ids that have no term data
     *
     * @param float  $timezone
     * @param string $targetDate
     *
     * @return array
     */
    public function findIdsNotHaveNextTerm(float $timezone, string $targetDate): array
    {
        $options = [
            'conditions' => [
                'Team.timezone' => $timezone,
                'OR'            => [
                    'CurrentTerm.id' => null,
                    'NextTerm.id'    => null,
                ],
            ],
            'fields'     => [
                'Team.id'
            ],
            'joins'      => [
                [
                    'table'      => 'terms',
                    'alias'      => 'CurrentTerm',
                    'type'       => 'LEFT',
                    'conditions' => [
                        'Team.id = CurrentTerm.team_id',
                        'CurrentTerm.start_date <=' => $targetDate,
                        'CurrentTerm.end_date >='   => $targetDate,
                        'CurrentTerm.del_flg'       => false,
                    ]
                ],
                [
                    'table'      => 'terms',
                    'alias'      => 'NextTerm',
                    'type'       => 'LEFT',
                    'conditions' => [
                        'Team.id = NextTerm.team_id',
                        "NextTerm.start_date <= (CurrentTerm.end_date + INTERVAL 1 DAY)",
                        'NextTerm.end_date >= (CurrentTerm.end_date + INTERVAL 1 DAY)',
                        'NextTerm.del_flg' => false,
                    ]
                ],
            ],
        ];
        $ret = $this->findWithoutTeamId('list', $options);
        // renumbering
        $ret = array_merge($ret);
        return $ret;
    }

    /**
     * 今期の期間データを持ち且つ来期データを持たないチームのIDと期の終了日を取得
     * finding id of teams are which have current term setting and which have not next term setting.
     *
     * @param float  $timezone
     * @param string $targetDate
     *
     * @return array [['team_id'=>'','border_months'=>'','end_date'=>'']]
     */
    public function findAllTermEndDatesNextTermNotExists(float $timezone, string $targetDate): array
    {
        $options = [
            'conditions' => [
                'Team.timezone'   => $timezone,
                'NextNextTerm.id' => null,
                'NOT'             => [
                    'CurrentTerm.id' => null,
                    'NextTerm.id'    => null,
                ],
            ],
            'fields'     => [
                'Team.border_months',
                'NextTerm.team_id',
                'NextTerm.end_date'
            ],
            'joins'      => [
                [
                    'table'      => 'terms',
                    'alias'      => 'CurrentTerm',
                    'type'       => 'LEFT',
                    'conditions' => [
                        'Team.id = CurrentTerm.team_id',
                        'CurrentTerm.start_date <=' => $targetDate,
                        'CurrentTerm.end_date >='   => $targetDate,
                        'CurrentTerm.del_flg'       => false,
                    ]
                ],
                [
                    'table'      => 'terms',
                    'alias'      => 'NextTerm',
                    'type'       => 'LEFT',
                    'conditions' => [
                        'Team.id = NextTerm.team_id',
                        "NextTerm.start_date <= (CurrentTerm.end_date + INTERVAL 1 DAY)",
                        'NextTerm.end_date >= (CurrentTerm.end_date + INTERVAL 1 DAY)',
                        'NextTerm.del_flg' => false,
                    ]
                ],
                [
                    'table'      => 'terms',
                    'alias'      => 'NextNextTerm',
                    'type'       => 'LEFT',
                    'conditions' => [
                        'Team.id = NextNextTerm.team_id',
                        "NextNextTerm.start_date <= (NextTerm.end_date + INTERVAL 1 DAY)",
                        'NextNextTerm.end_date >= (NextTerm.end_date + INTERVAL 1 DAY)',
                        'NextNextTerm.del_flg' => false,
                    ]
                ],
            ],
        ];
        $ret = $this->findWithoutTeamId('all', $options);

        // excluding Model name from the arrays and merging them.
        $teams = Hash::extract($ret, '{n}.Team');
        $nextTerms = Hash::extract($ret, '{n}.NextTerm');
        $ret = Hash::merge($teams, $nextTerms);
        return $ret;
    }

    /**
     * @param int   $serviceUseStatus
     * @param array $fields
     *
     * @return array
     */
    function findByServiceUseStatus(
        int $serviceUseStatus,
        array $fields = ['id', 'name', 'service_use_state_start_date', 'service_use_state_end_date', 'timezone']
    ): array
    {
        $options = [
            'conditions' => [
                'service_use_status' => $serviceUseStatus
            ],
            'fields'     => $fields,
        ];
        $res = $this->find('all', $options);
        $res = Hash::extract($res, '{n}.Team');
        return $res;
    }

    /**
     * find team_id list of status expired
     *
     * @param int    $serviceStatus
     * @param string $targetExpireDate
     *
     * @return array
     */
    public function findTeamIdsStatusExpired(int $serviceStatus, string $targetExpireDate): array
    {
        $options = [
            'conditions' => [
                'service_use_status'            => $serviceStatus,
                'service_use_state_end_date <=' => $targetExpireDate
            ],
            'fields'     => [
                'id'
            ]
        ];
        $res = $this->find('all', $options);
        if (empty($res)) {
            return [];
        }
        return Hash::extract($res, '{n}.Team.id');
    }

    /**
     * update service status and start,end date
     *
     * @param array $targetTeamIds
     * @param int   $nextStatus
     *
     * @return bool
     */
    function updateServiceStatusAndDates(array $targetTeamIds, int $nextStatus): bool
    {
        $statusDays = self::DAYS_SERVICE_USE_STATUS[$nextStatus];
        // new service_use_state_end_date will be status days + 1 day from old service_use_state_end_date
        $statusDays++;
        $fields = [
            'Team.service_use_status'           => $nextStatus,
            'Team.service_use_state_start_date' => "DATE_ADD(Team.service_use_state_end_date,INTERVAL 1 DAY)",
            'Team.service_use_state_end_date'   => "DATE_ADD(Team.service_use_state_end_date,INTERVAL {$statusDays} DAY)",
        ];

        // TODO: This is for only testing. cause, SqLite doesn't support DATE_ADD. But, this is bad know how..
        if ($this->useDbConfig == 'test') {
            $fields['Team.service_use_state_start_date'] = "DATE(Team.service_use_state_end_date, '+1 DAY')";
            $fields['Team.service_use_state_end_date'] = "DATE(Team.service_use_state_end_date, '+{$statusDays} DAY')";
        }

        $ret = $this->updateAll(
            $fields,
            [
                'Team.id' => $targetTeamIds
            ]
        );
        return $ret;
    }

    /**
     * update all team service use status start date and end date
     *
     * @param int    $serviceUseStatus
     * @param string $startDate
     *
     * @return bool
     */
    public function updateAllServiceUseStateStartEndDate(int $serviceUseStatus, string $startDate): bool
    {
        if ($serviceUseStatus == self::SERVICE_USE_STATUS_PAID) {
            $endDate = null;
        } else {
            $statusDays = self::DAYS_SERVICE_USE_STATUS[$serviceUseStatus];
            $endDate = AppUtil::dateAfter($startDate, $statusDays);
        }
        $res = $this->updateAll(
            [
                'Team.service_use_state_start_date' => "'$startDate'",
                'Team.service_use_state_end_date'   => $endDate ? "'$endDate'" : null,
            ],
            [
                'Team.service_use_status' => $serviceUseStatus
            ]
        );
        return $res;
    }

    /**
     * Check if paid plan
     *
     * @param int $teamId
     *
     * @return bool
     */
    public function isPaidPlan(int $teamId): bool
    {
        $status = $this->getServiceUseStatus($teamId);
        return $status == self::SERVICE_USE_STATUS_PAID;
    }

    /**
     * Check free trial plan or not
     *
     * @param int $teamId
     *
     * @return bool
     */
    public function isFreeTrial(int $teamId): bool
    {
        $status = $this->getServiceUseStatus($teamId);
        return $status == self::SERVICE_USE_STATUS_FREE_TRIAL;
    }

    /**
     * Update paid plan
     *
     * @param int    $teamId
     * @param string $date
     *
     * @return bool
     */
    public function updatePaidPlan(int $teamId, string $date): bool
    {
        $data = [
            'service_use_status'           => Enum\Model\Team\ServiceUseStatus::PAID,
            'service_use_state_start_date' => $date,
            'service_use_state_end_date'   => null
        ];
        $this->clear();
        $this->id = $teamId;
        $res = $this->save($data, false);
        if (empty($res)) {
            return false;
        }
        return true;
    }

    /**
     * get country by team id
     *
     * @param int $teamId
     *
     * @return string|null
     */
    public function getCountry(int $teamId)
    {
        $res = $this->getByid($teamId);
        if (!empty($res['country'])) {
            return $res['country'];
        }
        return null;
    }

    /**
     * Get service use status by team id
     *
     * @param int $teamId
     *
     * @return int|null
     */
    public function getServiceUseStatus(int $teamId)
    {
        $res = $this->getById($teamId);
        if (!empty($res['service_use_status'])) {
            return $res['service_use_status'];
        }
        return null;
    }

    /**
     * finding team ids that has been failed some times in monthly credit card charge
     *
     * @param int $startTimestamp
     * @param int $endTimestamp
     * @param int $judgeFailureCnt
     *
     * @return array
     */
    public function findTargetsForMovingReadOnly(int $startTimestamp, int $endTimestamp, $judgeFailureCnt = 3): array
    {
        $options = [
            'conditions' => [
                'Team.del_flg' => false,
            ],
            'fields'     => [
                'Team.id'
            ],
            'joins'      => [
                [
                    'table'      => 'payment_settings',
                    'alias'      => 'PaymentSetting',
                    'type'       => 'INNER',
                    'conditions' => [
                        'Team.id = PaymentSetting.team_id',
                        'Team.service_use_status' => Enum\Model\Team\ServiceUseStatus::PAID,
                        'PaymentSetting.type'     => Enum\Model\PaymentSetting\Type::CREDIT_CARD,
                        'PaymentSetting.del_flg'  => false,
                    ]
                ],
                [
                    'table'      => 'charge_histories',
                    'alias'      => 'ChargeHistory',
                    'type'       => 'INNER',
                    'conditions' => [
                        'PaymentSetting.team_id = ChargeHistory.team_id',
                        'ChargeHistory.charge_type'         => Enum\Model\ChargeHistory\ChargeType::MONTHLY_FEE,
                        'ChargeHistory.result_type'         => Enum\Model\ChargeHistory\ResultType::FAIL,
                        'ChargeHistory.charge_datetime >= ' => $startTimestamp,
                        'ChargeHistory.charge_datetime <= ' => $endTimestamp,
                        'ChargeHistory.del_flg'             => false,
                    ]
                ],
            ],
            'group'      => [
                'Team.id HAVING COUNT(Team.id) >= ' . $judgeFailureCnt
            ],
        ];
        $ret = $this->find('all', $options);
        if (empty($ret)) {
            return [];
        }
        return Hash::extract($ret, '{n}.Team.id');
    }

    /**
     * Get amount per user
     *
     * @param int $teamId
     *
     * @return void
     */
    public function getAmountPerUser(int $teamId)
    {
        $res = $this->getById($teamId);
        if (!empty($res['pre_register_amount_per_user'])) {
            return $res['pre_register_amount_per_user'];
        }
        return null;
    }

    /**
     * Get default translation language of a team
     *
     * @param int $teamId
     *
     * @return string ISO-639-1 Language Code
     */
    public function getDefaultTranslationLanguage(int $teamId): string
    {
        $option = [
            'conditions' => [
                'id' => $teamId
            ],
            'fields'     => [
                'default_translation_language'
            ]
        ];

        $queryResult = $this->find('first', $option);

        return $queryResult['Team']['default_translation_language'] ?: '';
    }

    /**
     * Save default translation language of a team
     *
     * @param int    $teamId
     * @param string $language ISO-639-1 Language code
     *
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function setDefaultTranslationLanguage(int $teamId, string $language)
    {
        if (!Enum\Language::isValid($language)) {
            throw new InvalidArgumentException("Unknown language code.");
        }

        $this->id = $teamId;

        $this->save([
            'default_translation_language' => $language
        ], false);
    }

    /**
     * Return ids of paid teams from given array
     *
     * @param int[] $teamIds Array of team ids
     *
     * @return int[] Array of ids of paid teams
     */
    public function filterPaidTeam(array $teamIds): array
    {
        if (empty($teamIds)) {
            return [];
        }

        $option = [
            'conditions' => [
                'id'                 => $teamIds,
                'service_use_status' => Team::SERVICE_USE_STATUS_PAID,
                'del_flg'            => false
            ],
            'fields'     => [
                'id'
            ]
        ];

        $queryResult = $this->useType()->find('all', $option);

        return Hash::extract($queryResult, '{n}.{s}.id') ?: [];
    }

}
