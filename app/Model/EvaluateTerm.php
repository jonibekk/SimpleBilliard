<?php
App::uses('AppModel', 'Model');

/**
 * EvaluateTerm Model
 *
 * @property Team       $Team
 * @property Evaluation $Evaluation
 * @property Evaluator  $Evaluator
 */
class EvaluateTerm extends AppModel
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
        $res = Hash::combine($res, '{n}.EvaluateTerm.id', '{n}.EvaluateTerm');
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
        return Hash::extract($res, '{n}.EvaluateTerm');
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

        //先ずはcurrentを取得。previous, nextの基準になるので
        if (!$this->currentTerm) {
            if ($withCache) {
                $currentTermFromCache = Cache::read($this->getCacheKey(CACHE_KEY_TERM_CURRENT), 'team_info');
                if ($currentTermFromCache !== false) {
                    $this->currentTerm = $currentTermFromCache;
                }
            }
            if (!$this->currentTerm) {
                $this->currentTerm = $this->getTermDataByTimeStamp(REQUEST_TIMESTAMP);
                if ($this->currentTerm && $withCache) {
                    Cache::set('duration', $this->currentTerm['end_date'] - REQUEST_TIMESTAMP, 'team_info');
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
                $this->previousTerm = $this->getTermDataByTimeStamp(strtotime("-1 day",
                    $this->currentTerm['start_date']));
                if ($this->previousTerm && $withCache) {
                    Cache::set('duration', $this->currentTerm['end_date'] - REQUEST_TIMESTAMP, 'team_info');
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
                $this->nextTerm = $this->getTermDataByTimeStamp(strtotime("+1 day", $this->currentTerm['end_date']));
                if ($this->nextTerm && $withCache) {
                    Cache::set('duration', $this->currentTerm['end_date'] - REQUEST_TIMESTAMP, 'team_info');
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
     * @param bool $utcMidnight
     *
     * @return array
     */
    public function getCurrentTermData(bool $utcMidnight = false): array
    {
        $term = $this->getTermData(self::TYPE_CURRENT);
        if ($utcMidnight) {
            return $this->changeToUtcMidnight($term);
        }
        return $term;
    }

    /**
     * @param bool $utcMidnight
     *
     * @return array
     */
    public function getNextTermData(bool $utcMidnight = false): array
    {
        $term = $this->getTermData(self::TYPE_NEXT);
        if ($utcMidnight) {
            return $this->changeToUtcMidnight($term);
        }
        return $term;
    }

    /**
     * @param bool $utcMidnight
     *
     * @return array
     */
    public function getPreviousTermData(bool $utcMidnight = false): array
    {
        $term = $this->getTermData(self::TYPE_PREVIOUS);
        if ($utcMidnight) {
            return $this->changeToUtcMidnight($term);
        }
        return $term;
    }

    private function changeToUtcMidnight(array $term): array
    {
        $term['start_date'] += $term['timezone'] * HOUR;
        $term['end_date'] += $term['timezone'] * HOUR;
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
        Cache::delete($this->getCacheKey(CACHE_KEY_TERM_CURRENT), 'data');
        Cache::delete($this->getCacheKey(CACHE_KEY_TERM_NEXT), 'data');
        Cache::delete($this->getCacheKey(CACHE_KEY_TERM_PREVIOUS), 'data');
        $this->_checkType($type);
        $new_start = null;
        $new_end = null;

        if ($type === self::TYPE_PREVIOUS) {
            if ($this->getTermData(self::TYPE_PREVIOUS, false)) {
                return false;
            }
            if (!$current = $this->getTermData(self::TYPE_CURRENT, false)) {
                return false;
            }
            $new_start = $this->_getStartEndWithoutExistsData(strtotime("-1 day", $current['start_date']))['start'];
            $new_end = $current['start_date'] - 1;
        }

        if ($type === self::TYPE_CURRENT) {
            if ($this->getTermData(self::TYPE_CURRENT, false)) {
                return false;
            }
            $new = $this->_getStartEndWithoutExistsData();
            $new_start = $new['start'];
            $new_end = $new['end'];
        }

        if ($type === self::TYPE_NEXT) {
            if ($this->getTermData(self::TYPE_NEXT, false)) {
                return false;
            }
            if (!$current = $this->getTermData(self::TYPE_CURRENT, false)) {
                return false;
            }
            $new_start = $current['end_date'] + 1;
            $new_end = $this->_getStartEndWithoutExistsData(strtotime("+1 day", $current['end_date']))['end'];
        }

        $team = $this->Team->getCurrentTeam();
        $data = [
            'start_date' => $new_start,
            'end_date'   => $new_end,
            'timezone'   => $team['Team']['timezone'],
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
     * @param $start_term_month
     * @param $border_months
     * @param $timezone
     *
     * @return bool|mixed
     * @throws Exception
     */
    public function updateTermData($option, $start_term_month, $border_months, $timezone)
    {
        $save_data = $this->getSaveDataBeforeUpdate($option, $start_term_month, $border_months, $timezone);
        $res = $this->saveAll($save_data);
        return $res;
    }

    public function getSaveDataBeforeUpdate($option, $start_term_month, $border_months, $timezone)
    {
        $current_term = $this->getCurrentTermData();
        if ($option == Team::OPTION_CHANGE_TERM_FROM_CURRENT) {
            $new_current = $this->_getStartEndWithoutExistsData(REQUEST_TIMESTAMP, $start_term_month, $border_months,
                $timezone);
            $new_next = $this->_getStartEndWithoutExistsData(strtotime('+1 day', $new_current['end']),
                $start_term_month,
                $border_months, $timezone);
            $data = [
                $this->getCurrentTermId() =>
                    [
                        'id'         => $this->getCurrentTermId(),
                        'start_date' => $current_term['start_date'],
                        'end_date'   => $new_current['end'],
                        'timezone'   => $timezone,
                    ],
                $this->getNextTermId()    =>
                    [
                        'id'         => $this->getNextTermId(),
                        'start_date' => $new_next['start'],
                        'end_date'   => $new_next['end'],
                        'timezone'   => $timezone,
                    ]
            ];
        } else {
            $new_next = $this->_getStartEndWithoutExistsData(strtotime('+1 day', $current_term['end_date']),
                $start_term_month,
                $border_months, $timezone);
            $data = [
                $this->getNextTermId() =>
                    [
                        'id'         => $this->getNextTermId(),
                        'start_date' => $current_term['end_date'] + 1,
                        'end_date'   => $new_next['end'],
                        'timezone'   => $timezone,
                    ],
            ];
        }

        return $data;
    }

    public function getNewStartEndBeforeAdd($start_term_month, $border_months, $timezone)
    {
        return $this->_getStartEndWithoutExistsData(REQUEST_TIMESTAMP, $start_term_month, $border_months, $timezone);
    }

    /**
     * return new start date and end date calculated
     *
     * @param int  $target_date
     * @param null $start_term_month
     * @param null $border_months
     * @param null $timezone
     *
     * @return null|array
     */
    private function _getStartEndWithoutExistsData(
        $target_date = REQUEST_TIMESTAMP,
        $start_term_month = null,
        $border_months = null,
        $timezone = null
    ) {
        $team = $this->Team->getCurrentTeam();
        if (empty($team) && (!$start_term_month || !$border_months || !$timezone)) {
            return null;
        }
        if (!$start_term_month) {
            $start_term_month = $team['Team']['start_term_month'];
        }
        if (!$border_months) {
            $border_months = $team['Team']['border_months'];
        }
        if (!$timezone) {
            $timezone = $team['Team']['timezone'];
        }

        $start_date = strtotime(date("Y-{$start_term_month}-1", $target_date));
        $start_date_tmp = date("Y-m-1", $start_date);
        $end_date = strtotime($start_date_tmp . "+ {$border_months} month");

        $term = [];
        //指定日時が期間内の場合 in the case of target date include the term
        if ($start_date <= $target_date && $end_date > $target_date) {
            $term['start'] = $start_date - $timezone * 3600;
            $term['end'] = $end_date - 1 - $timezone * 3600;
        } //指定日時が開始日より前の場合 in the case of target date is earlier than start date
        elseif ($target_date < $start_date) {
            while ($target_date < $start_date) {
                $start_date_tmp = date("Y-m-1", $start_date);
                $start_date = strtotime($start_date_tmp . "- {$border_months} month");
            }
            $term['start'] = $start_date - $timezone * 3600;
            $start_date_tmp = date("Y-m-1", $start_date);
            $term['end'] = strtotime($start_date_tmp . "+ {$border_months} month") - $timezone * 3600 - 1;
        } //終了日が指定日時より前の場合 in the case of target date is later than end date
        elseif ($target_date > $end_date) {
            while ($target_date > $end_date) {
                $end_date_tmp = date("Y-m-1", $end_date);
                $end_date = strtotime($end_date_tmp . "+ {$border_months} month");
            }
            $term['end'] = $end_date - 1 - $timezone * 3600;
            $end_date_tmp = date("Y-m-1", $end_date);
            $term['start'] = strtotime($end_date_tmp . "- {$border_months} month") - $timezone * 3600;
        }
        return $term;
    }

    /**
     * return term data from datetime
     *
     * @param int $timeStamp unixtime
     *
     * @return array|null
     */
    public function getTermDataByTimeStamp($timeStamp = REQUEST_TIMESTAMP)
    {
        $options = [
            'conditions' => [
                'team_id'       => $this->current_team_id,
                'start_date <=' => $timeStamp,
                'end_date >='   => $timeStamp,
            ]
        ];
        $res = $this->find('first', $options);
        $res = Hash::extract($res, 'EvaluateTerm');
        return $res;
    }

    public function getTermText($start_date, $end_date)
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

        return date('Y/m/d', $start_date + $this->me['timezone'] * 3600) . ' - ' .
            date('Y/m/d', $end_date + $this->me['timezone'] * 3600);
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
     * @param float $timezone
     * @param int   $targetTimestamp
     *
     * @return array
     */
    public function findTeamIdByTimezone(float $timezone, int $targetTimestamp): array
    {
        $options = [
            'conditions' => [
                'start_date <=' => $targetTimestamp,
                'end_date >='   => $targetTimestamp,
                'timezone'      => $timezone,
            ],
            'fields'     => [
                'team_id'
            ]
        ];

        $ret = $this->findWithoutTeamId('list', $options);
        // キーに特別な意味を持たせないように、歯抜けのキーを再採番
        $ret = array_merge($ret);
        return $ret;
    }
}
