<?php
App::uses('AppModel', 'Model');
App::uses('AppUtil', 'Util');

/**
 * Term Model
 *
 * @property Team       $Team
 * @property Evaluation $Evaluation
 * @property Evaluator  $Evaluator
 */
class Term extends AppModel
{
    const STATUS_EVAL_NOT_STARTED = 0;
    const STATUS_EVAL_IN_PROGRESS = 1;
    const STATUS_EVAL_FROZEN = 2;
    const STATUS_EVAL_FINISHED = 3;

    const TYPE_CURRENT = 0;
    const TYPE_PREVIOUS = 1;
    const TYPE_NEXT = 2;
    static private $TYPE = [
        self::TYPE_CURRENT,
        self::TYPE_PREVIOUS,
        self::TYPE_NEXT,
    ];

    const TERM_TYPE_CURRENT = 'current';
    const TERM_TYPE_NEXT = 'next';

    private $previousTerm = [];
    private $currentTerm = [];
    private $nextTerm = [];

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'del_flg' => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
    ];

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [
        'Team',
    ];

    /**
     * hasMany associations
     *
     * @var array
     */
    public $hasMany = [
        'Evaluation',
        'Evaluator',
    ];

    public $update_validate = [
        'start_ym'  => [
            'notBlank'            => [
                'required' => 'update',
                'rule'     => 'notBlank',
            ],
            'dateYm'             => [
                'rule' => ['date', 'ym'],
            ],
        ],
        'term_range' => [
            'notBlank'            => [
                'required' => 'update',
                'rule'     => 'notBlank',
            ],
            'numeric' => [
                'rule' => ['numeric'],
            ],
            'range'   => [
                'rule' => ['range', 1, 12]
            ]
        ]
    ];

    /**
     * 来期開始月のバリデーション
     * - 来月 - 12ヶ月後 の間に収まっているか
     *
     * @param  array $val
     *
     * @return bool
     */
    function customValidNextStartDate(array $val)
    {
        $nextStartYm = array_shift($val);
        // lower limit
        $lowerLimitYm = date('Y-m', strtotime("+1 month"));
        if ($nextStartYm < $lowerLimitYm) {
            // TODO: set valid error message
            return '';
        }

        // upper limit
        $upperLimitYm = date('Y-m', strtotime("$nextStartYm +12 month"));
        if ($nextStartYm > $upperLimitYm) {
            // TODO: set valid error message
            return '';
        }
        return true;
    }

    /**
     * TODO:findAllメソッドに統合
     *
     * @deprecated
     *
     * @param bool $order_desc
     *
     * @return array|null
     */
    function getAllTerm($order_desc = true)
    {
        $options = [
            'conditions' => [
                'team_id' => $this->current_team_id
            ],
            'order'      => [
                'start_date' => 'asc'
            ]
        ];
        if ($order_desc) {
            $options['order']['start_date'] = 'desc';
        }
        $res = $this->find('all', $options);
        $res = Hash::combine($res, '{n}.Term.id', '{n}.Term');
        return $res;
    }

    /**
     * チームの全評価期間取得
     *
     * @return array|null
     */
    function findByTeam()
    {
        $options = [
            'conditions' => [
                'team_id' => $this->current_team_id
            ],
            'order'      => [
                'start_date' => 'desc'
            ]
        ];
        $res = $this->find('all', $options);
        return Hash::extract($res, '{n}.Term');
    }

    function changeToInProgress($id)
    {
        $this->id = $id;
        return $this->saveField('evaluate_status', self::STATUS_EVAL_IN_PROGRESS);
    }

    /**
     * @param $id
     *
     * @return bool
     */
    function isAbleToStartEvaluation($id)
    {
        $options = [
            'conditions' => [
                'id'              => $id,
                'team_id'         => $this->current_team_id,
                'evaluate_status' => self::STATUS_EVAL_NOT_STARTED,
            ],
        ];
        $res = $this->find('first', $options);
        return (bool)$res;
    }

    function isStartedEvaluation($id)
    {
        $options = [
            'conditions' => [
                'id'      => $id,
                'team_id' => $this->current_team_id,
                'NOT'     => [
                    'evaluate_status' => self::STATUS_EVAL_NOT_STARTED,
                ]
            ]
        ];
        $res = $this->find('first', $options);
        return (bool)$res;
    }

    function changeFreezeStatus($id)
    {
        // Check freezable
        $options = [
            'conditions' => [
                'id'      => $id,
                'team_id' => $this->current_team_id,
            ]
        ];
        $res = $this->find('first', $options);
        if (empty($res)) {
            throw new RuntimeException(__("This term can not be frozen."));
        }

        $isFrozen = $this->checkFrozenEvaluateTerm($id);
        if ($isFrozen) {
            $expect_status = self::STATUS_EVAL_IN_PROGRESS;
        } else {
            $expect_status = self::STATUS_EVAL_FROZEN;
        }

        $this->id = $id;
        $saveData = ['evaluate_status' => $expect_status];
        $res = $this->save($saveData);
        return $res;
    }

    function checkFrozenEvaluateTerm($id)
    {
        $options = [
            'conditions' => [
                'id'              => $id,
                'team_id'         => $this->current_team_id,
                'evaluate_status' => self::STATUS_EVAL_FROZEN
            ]
        ];
        $res = $this->find('first', $options);
        return (empty($res)) ? false : true;
    }

    /**
     * is available type? true or RuntimeException
     *
     * @param $type
     *
     * @return bool
     */
    private function _checkType($type)
    {
        if (!in_array($type, self::$TYPE)) {
            throw new RuntimeException("invalid type!");
        }
        return true;
    }

    /**
     * return term data
     *
     * @param      $type
     * @param bool $withCache
     *
     * @return array
     */
    public function getTermData(int $type, bool $withCache = true): array
    {
        $this->_checkType($type);
        $timezone = $this->Team->getTimezone();

        //先ずはcurrentを取得。previous, nextの基準になるので
        if (!$this->currentTerm) {
            if ($withCache) {
                $currentTermFromCache = Cache::read($this->getCacheKey(CACHE_KEY_TERM_CURRENT), 'team_info');
                if ($currentTermFromCache !== false) {
                    $this->currentTerm = $currentTermFromCache;
                }
            }
            if (!$this->currentTerm) {
                $this->currentTerm = $this->getTermDataByDate(AppUtil::todayDateYmdLocal($timezone));
                if ($this->currentTerm && $withCache) {
                    Cache::write($this->getCacheKey(CACHE_KEY_TERM_CURRENT), $this->currentTerm, 'team_info');
                }
            }
        }

        if ($type === self::TYPE_PREVIOUS) {
            if ($this->previousTerm) {
                return $this->previousTerm;
            }
            if ($withCache) {
                $previousTermFromCache = Cache::read($this->getCacheKey(CACHE_KEY_TERM_PREVIOUS), 'team_info');
                if ($previousTermFromCache !== false) {
                    $this->previousTerm = $previousTermFromCache;
                    return $this->previousTerm;
                }
            }
            if (isset($this->currentTerm['start_date']) && !empty($this->currentTerm['start_date'])) {
                $this->previousTerm = $this->getTermDataByDate(AppUtil::dateYmd(strtotime($this->currentTerm['start_date']) - DAY));
                if ($this->previousTerm && $withCache) {
                    Cache::write($this->getCacheKey(CACHE_KEY_TERM_PREVIOUS), $this->previousTerm, 'team_info');
                }
            }
            return $this->previousTerm;
        }

        if ($type === self::TYPE_NEXT) {
            if ($this->nextTerm) {
                return $this->nextTerm;
            }
            if ($withCache) {
                $nextTermFromCache = Cache::read($this->getCacheKey(CACHE_KEY_TERM_NEXT), 'team_info');
                if ($nextTermFromCache !== false) {
                    $this->nextTerm = $nextTermFromCache;
                    return $this->nextTerm;
                }
            }
            if (isset($this->currentTerm['end_date']) && !empty($this->currentTerm['end_date'])) {
                $this->nextTerm = $this->getTermDataByDate(AppUtil::dateYmd(strtotime($this->currentTerm['end_date']) + DAY));
                if ($this->nextTerm && $withCache) {
                    Cache::write($this->getCacheKey(CACHE_KEY_TERM_NEXT), $this->nextTerm, 'team_info');
                }
            }
            return $this->nextTerm;
        }

        return $this->currentTerm;
    }

    /**
     * @param $type
     *
     * @return null| int
     */
    public function getTermId($type)
    {
        $this->_checkType($type);
        $term = $this->getTermData($type);
        return Hash::get($term, 'id');
    }

    /**
     * @return array
     */
    public function getCurrentTermData(): array
    {
        $term = $this->getTermData(self::TYPE_CURRENT);
        return $term;
    }

    /**
     * @return array
     */
    public function getNextTermData(): array
    {
        $term = $this->getTermData(self::TYPE_NEXT);
        return $term;
    }

    /**
     * @return array
     */
    public function getPreviousTermData(): array
    {
        $term = $this->getTermData(self::TYPE_PREVIOUS);
        return $term;
    }

    public function getCurrentTermId()
    {
        return $this->getTermId(self::TYPE_CURRENT);
    }

    public function getNextTermId()
    {
        return $this->getTermId(self::TYPE_NEXT);
    }

    public function getPreviousTermId()
    {
        return $this->getTermId(self::TYPE_PREVIOUS);
    }

    /**
     * @param $type
     *
     * @return bool|mixed
     * @throws Exception
     */
    public function addTermData($type)
    {
        //キャッシュを削除
        Cache::delete($this->getCacheKey(CACHE_KEY_TERM_CURRENT), 'team_info');
        Cache::delete($this->getCacheKey(CACHE_KEY_TERM_NEXT), 'team_info');
        Cache::delete($this->getCacheKey(CACHE_KEY_TERM_PREVIOUS), 'team_info');
        $this->_checkType($type);
        $newStart = null;
        $newEnd = null;

        if ($type === self::TYPE_PREVIOUS) {
            if ($this->getTermData(self::TYPE_PREVIOUS, false)) {
                return false;
            }
            if (!$current = $this->getTermData(self::TYPE_CURRENT, false)) {
                return false;
            }
            $newStart = $this->_getStartEndWithoutExistsData(AppUtil::dateYesterday($current['start_date']))['start'];

            $newEnd = AppUtil::dateYesterday($current['start_date']);
        }

        if ($type === self::TYPE_CURRENT) {
            if ($this->getTermData(self::TYPE_CURRENT, false)) {
                return false;
            }
            $timezone = $this->Team->getTimezone();
            $new = $this->_getStartEndWithoutExistsData(AppUtil::todayDateYmdLocal($timezone));
            $newStart = $new['start'];
            $newEnd = $new['end'];
        }

        if ($type === self::TYPE_NEXT) {
            if ($this->getTermData(self::TYPE_NEXT, false)) {
                return false;
            }
            if (!$current = $this->getTermData(self::TYPE_CURRENT, false)) {
                return false;
            }
            $newStart = AppUtil::dateTomorrow($current['end_date']);
            $newEnd = $this->_getStartEndWithoutExistsData(AppUtil::dateTomorrow($current['end_date']))['end'];
        }

        $team = $this->Team->getCurrentTeam();
        $data = [
            'start_date' => $newStart,
            'end_date'   => $newEnd,
            'team_id'    => $team['Team']['id'],
        ];
        $this->create();
        $res = $this->save($data);
        return $res;
    }

    /**
     * reset term only property, not delete data
     *
     * @param $type
     */
    public function resetTermProperty($type)
    {
        $this->_checkType($type);
        if ($type === self::TYPE_CURRENT) {
            $this->currentTerm = [];
        }
        if ($type === self::TYPE_NEXT) {
            $this->nextTerm = [];
        }
        if ($type === self::TYPE_PREVIOUS) {
            $this->previousTerm = [];
        }
    }

    /**
     * 全ての期間のプロパティをリセット
     */
    function resetAllTermProperty()
    {
        $this->resetTermProperty(self::TYPE_CURRENT);
        $this->resetTermProperty(self::TYPE_PREVIOUS);
        $this->resetTermProperty(self::TYPE_NEXT);
    }

    /**
     * @param $option
     * @param $startTermMonth
     * @param $borderMonths
     *
     * @return bool|mixed
     */
    public function updateTermData($option, $startTermMonth, $borderMonths)
    {
        $saveData = $this->getSaveDataBeforeUpdate($option, $startTermMonth, $borderMonths);
        $res = $this->saveAll($saveData);
        return $res;
    }

    public function getSaveDataBeforeUpdate($option, $startTermMonth, $borderMonths)
    {
        $currentTerm = $this->getCurrentTermData();
        if ($option == Team::OPTION_CHANGE_TERM_FROM_CURRENT) {
            $timezone = $this->Team->getTimezone();
            $todayDate = AppUtil::todayDateYmdLocal($timezone);
            $newCurrent = $this->_getStartEndWithoutExistsData($todayDate, $startTermMonth, $borderMonths);
            $newNext = $this->_getStartEndWithoutExistsData(
                AppUtil::dateTomorrow($newCurrent['end']),
                $startTermMonth,
                $borderMonths
            );
            $data = [
                $this->getCurrentTermId() =>
                    [
                        'id'         => $this->getCurrentTermId(),
                        'start_date' => $currentTerm['start_date'],
                        'end_date'   => $newCurrent['end'],
                    ],
                $this->getNextTermId()    =>
                    [
                        'id'         => $this->getNextTermId(),
                        'start_date' => $newNext['start'],
                        'end_date'   => $newNext['end'],
                    ]
            ];
        } else {
            $newNext = $this->_getStartEndWithoutExistsData(
                AppUtil::dateTomorrow($currentTerm['end_date']),
                $startTermMonth,
                $borderMonths
            );
            $data = [
                $this->getNextTermId() =>
                    [
                        'id'         => $this->getNextTermId(),
                        'start_date' => $currentTerm['end_date'] + 1,
                        'end_date'   => $newNext['end'],
                    ],
            ];
        }

        return $data;
    }

    public function getNewStartEndBeforeAdd($startTermMonth, $borderMonths, $timezone)
    {
        $todayDate = AppUtil::todayDateYmdLocal($timezone);
        return $this->_getStartEndWithoutExistsData($todayDate, $startTermMonth, $borderMonths);
    }

    /**
     * return new start date and end date calculated
     *
     * @param string $targetDate
     * @param null   $startTermMonth
     * @param null   $borderMonths
     *
     * @return null|array
     */
    private function _getStartEndWithoutExistsData(
        string $targetDate,
        $startTermMonth = null,
        $borderMonths = null
    ) {
        $team = $this->Team->getCurrentTeam();
        if (empty($team) && (!$startTermMonth || !$borderMonths)) {
            return null;
        }
        if (!$startTermMonth) {
            $startTermMonth = $team['Team']['start_term_month'];
        }
        if (!$borderMonths) {
            $borderMonths = $team['Team']['border_months'];
        }
        $startDate = date("Y-m-01", strtotime(date('Y') . "-{$startTermMonth}-01"));
        $endDate = AppUtil::dateYmd(strtotime($startDate . " +{$borderMonths} month") - DAY);
        //date型をリフォーマット
        $targetDate = AppUtil::dateYmd(strtotime($targetDate));

        $term = [];
        //指定日時が期間内の場合 in the case of target date include the term
        if ($startDate <= $targetDate && $endDate >= $targetDate) {
            $term['start'] = $startDate;
            $term['end'] = $endDate;
        } //指定日時が開始日より前の場合 in the case of target date is earlier than start date
        elseif ($targetDate < $startDate) {
            while ($targetDate < $startDate) {
                $startDate = AppUtil::dateYmd(strtotime($startDate . " -{$borderMonths} month"));
            }
            $term['start'] = $startDate;
            $term['end'] = AppUtil::dateYmd(strtotime($startDate . " +{$borderMonths} month") - DAY);
        } //終了日が指定日時より前の場合 in the case of target date is later than end date
        elseif ($targetDate > $endDate) {
            while ($targetDate > $endDate) {
                $endDate = date('Y-m-t', strtotime(date('Y-m-01', strtotime($endDate)) . " +{$borderMonths} month"));
            }
            $term['end'] = $endDate;
            $term['start'] = AppUtil::dateYmd(
                strtotime(date('Y-m-01', strtotime($endDate)) . " -{$borderMonths} month +1 month")
            );
        }
        return $term;
    }

    /**
     * return term data from date string
     *
     * @param string $date
     *
     * @return array|null
     */
    public function getTermDataByDate(string $date)
    {
        $timezone = $this->Team->getTimezone();
        $options = [
            'conditions' => [
                'team_id'       => $this->current_team_id,
                'start_date <=' => $date,
                'end_date >='   => $date,
            ]
        ];
        $res = $this->find('first', $options);
        if (!empty($res)) {
            $res = Hash::extract($res, 'Term');
            $res['timezone'] = $timezone;
            // TODO: error logging for unexpected creating term data. when running test cases, ignore it for travis.
        } elseif ($this->useDbConfig != "test") {
            $this->log(sprintf('[%s] Term data is not found. find options: %s, session data: %s, backtrace: %s',
                __METHOD__,
                var_export($options, true),
                var_export(CakeSession::read(), true),
                Debugger::trace()
            ));
        }
        return $res;
    }

    /**
     * @param string $start_date
     * @param string $end_date
     *
     * @return string
     */
    public function getTermText(string $start_date, string $end_date): string
    {
        $current = $this->getCurrentTermData();
        $previous = $this->getPreviousTermData();
        $next = $this->getNextTermData();
        if ($start_date >= $current['start_date'] && $end_date <= $current['end_date']) {
            return __('Current Term');
        } elseif ($start_date >= $previous['start_date'] && $end_date <= $previous['end_date']) {
            return __('Previous Term');
        } elseif ($start_date >= $next['start_date'] && $end_date <= $next['end_date']) {
            return __('Next Term');
        }
        return AppUtil::dateYmdReformat($start_date, "/") . ' - ' . AppUtil::dateYmdReformat($end_date, "/");
    }

    /**
     * どの評価期間かを判定
     *
     * @param $start_date
     * @param $end_date
     *
     * @return null|string
     */
    public function getTermType($start_date, $end_date)
    {
        $current = $this->getCurrentTermData();
        $next = $this->getNextTermData();

        if ($start_date >= $current['start_date'] && $end_date <= $current['end_date']) {
            return "current";
        } elseif ($start_date >= $next['start_date'] && $end_date <= $next['end_date']) {
            return "next";
        }
        return null;
    }

    /**
     * 指定したタイムゾーン設定になっているチームのIDのリストを返す
     *
     * @param float  $timezone
     * @param string $targetDate
     *
     * @return array
     */
    public function findTeamIdByTimezone(float $timezone, string $targetDate): array
    {
        $options = [
            'conditions' => [
                'start_date <=' => $targetDate,
                'end_date >='   => $targetDate,
            ],
            'fields'     => [
                'team_id'
            ],
            'joins'      => [
                [
                    'table'      => 'teams',
                    'alias'      => 'Team',
                    'type'       => 'INNER',
                    'conditions' => [
                        'Team.id = Term.team_id',
                        'Team.timezone' => $timezone,
                        'Team.del_flg'  => false,
                    ]
                ],
            ],
        ];
        $ret = $this->findWithoutTeamId('list', $options);
        // キーに特別な意味を持たせないように、歯抜けのキーを再採番
        $ret = array_merge($ret);
        return $ret;
    }

    /**
     * update current term end date
     *
     * @param  string $endDate
     *
     * @return bool
     */
    public function updateCurrentRange(string $endDate): bool
    {
        $currentTermId = $this->getCurrentTermId();
        $this->id = $currentTermId;
        return (bool)$this->saveField('end_date', $endDate);
    }

    /**
     * Update next term start_date and end_date
     *
     * @param  string $startDate
     * @param  string $endDate
     *
     * @return bool
     */
    public function updateNextRange(string $startDate, string $endDate): bool
    {
        $saveData = [
            'id'         => $this->getNextTermId(),
            'start_date' => $startDate,
            'end_date'   => $endDate
        ];

        return (bool)$this->save($saveData);
    }

    public function initTerm(string $nextStartDate, int $termRange, int $teamId): bool
    {
        $currentStartDate = date('Y-m-01');
        $currentEndDate = date('Y-m-d', strtotime($nextStartDate) - DAY);
        $nextEndDate = date('Y-m-t', strtotime("$nextStartDate +$termRange month"));
        $saveData = [
            [
                'team_id'    => $teamId,
                'start_date' => $currentStartDate,
                'end_date'   => $currentEndDate
            ],
            [
                'team_id'    => $teamId,
                'start_date' => $nextStartDate,
                'end_date'   => $nextEndDate
            ]
        ];
        return $this->saveAll($saveData);
    }
}
