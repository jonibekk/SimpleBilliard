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

    private $previous_term = [];
    private $current_term = [];
    private $next_term = [];
    private $latest_term = [];

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

    function getTermIdByDate($start, $end)
    {
        $res = $this->getTermByDate($start, $end);
        return viaIsSet($res['id']);
    }

    function getTermByDate($start, $end)
    {
        $options = [
            'conditions' => [
                'start_date <=' => $start,
                'end_date >='   => $end,
                'team_id'       => $this->current_team_id
            ]
        ];
        $res = $this->find('first', $options);
        $res = Hash::extract($res, 'EvaluateTerm');
        return $res;
    }

    function getCurrentTerm()
    {
        if ($this->current_term) {
            return $this->current_term;
        }
        $res = $this->getTermByDate(REQUEST_TIMESTAMP, REQUEST_TIMESTAMP);
        $this->current_term = $res;
        return $this->current_term;
    }

    function getCurrentTermId()
    {
        $current_term = $this->getCurrentTerm();
        return viaIsSet($current_term['id']);
    }

    function getNextTerm()
    {
        if ($this->next_term) {
            return $this->next_term;
        }
        $current_term = $this->Team->EvaluateTerm->getCurrentTerm();
        $next_term = $this->Team->EvaluateTerm->getTermByDate($current_term['end_date'] + 1,
                                                              $current_term['end_date'] + 1);
        $this->next_term = $next_term;
        return $this->next_term;
    }

    function getNextTermId()
    {
        $next_term = $this->getNextTerm();
        return viaIsSet($next_term['id']);
    }

    function getLatestTermId()
    {
        $res = $this->getLatestTerm();
        return viaIsSet($res['id']);
    }

    function getLatestTerm()
    {
        if ($this->latest_term) {
            return $this->latest_term;
        }
        $options = [
            'conditions' => [
                'team_id' => $this->current_team_id
            ],
            'order'      => ['id' => 'desc']
        ];
        $res = $this->find('first', $options);
        $res = Hash::extract($res, 'EvaluateTerm');
        return $res;
    }

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

    function getPreviousTermId()
    {
        $res = $this->getPreviousTerm();
        return viaIsSet($res['id']);
    }

    function getPreviousTerm()
    {
        if ($this->previous_term) {
            return $this->previous_term;
        }
        $all_term = $this->getAllTerm();
        if (count($all_term) < 2) {
            return null;
        }
        $current_term_id = $this->getCurrentTermId();
        $prev_key = null;
        $res_key = null;
        foreach ($all_term as $k => $v) {
            if ($prev_key == $current_term_id) {
                $res_key = $k;
                break;
            }
            $prev_key = $k;
        }
        $this->previous_term = viaIsSet($all_term[$res_key]);
        return $this->previous_term;
    }

    function saveCurrentTerm()
    {
        $start_date = $this->Team->getCurrentTermStartDate();
        $latest = $this->getLatestTerm();
        if (!empty($latest)) {
            $start_date = $latest['end_date'] + 1;
        }
        $res = $this->saveTerm($start_date, $this->Team->getCurrentTermEndDate() - 1);
        return $res;
    }

    function saveNextTerm()
    {
        $latest = $this->getLatestTerm();
        if (empty($latest)) {
            return;
        }
        $start_date = $latest['end_date'] + 1;
        $team = $this->Team->getCurrentTeam();

        $next_new = $this->Team->getTermStartEndFromParam($team['Team']['start_term_month'],
                                                          $team['Team']['border_months'],
                                                          $start_date,
                                                          $team['Team']['timezone']
        );

        $res = $this->saveTerm($start_date, $next_new['end'] - 1);
        return $res;
    }

    function saveTerm($start, $end, $timezone = null)
    {
        if (!$timezone) {
            $team = $this->Team->getCurrentTeam();
            $timezone = $team['Team']['timezone'];
        }
        $data = [
            'team_id'    => $this->current_team_id,
            'start_date' => $start,
            'end_date'   => $end,
            'timezone'   => $timezone,
        ];
        $this->create();
        $res = $this->save($data);
        return $res;
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
            throw new RuntimeException(__d('gl', "この期間は凍結できません。"));
        }

        $isFrozen = $this->checkFrozenEvaluateTerm($id);
        if ($isFrozen) {
            $expect_status = self::STATUS_EVAL_IN_PROGRESS;
        }
        else {
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

    function saveChangedTerm($option, $start_term_month, $border_months, $timezone = null)
    {
        $current_term_id = $this->getCurrentTermId();
        $next_term_id = $this->getNextTermId();
        $new_term = $this->getChangeCurrentNextTerm($option, $start_term_month, $border_months, $timezone);
        //今期からの場合で評価開始してたら処理しない
        if ($option == Team::OPTION_CHANGE_TERM_FROM_CURRENT &&
            $this->isStartedEvaluation($current_term_id)
        ) {
            return false;
        }
        $saved_current = true;
        $saved_next = true;
        if ($current_term_id && $option == Team::OPTION_CHANGE_TERM_FROM_CURRENT) {
            $this->id = $current_term_id;
            $saved_current = $this->save($new_term['current']);
        }
        if ($next_term_id &&
            ($option == Team::OPTION_CHANGE_TERM_FROM_CURRENT ||
                $option == Team::OPTION_CHANGE_TERM_FROM_NEXT)
        ) {
            $this->id = $next_term_id;
            $saved_next = $this->save($new_term['next']);
        }

        if ((bool)$saved_next && (bool)$saved_current) {
            return true;
        }
        return false;
    }

    function getChangeCurrentNextTerm($option, $start_term_month, $border_months, $timezone = null)
    {

        $res = [
            'current' => [
                'start_date' => null,
                'end_date'   => null,
                'timezone'   => $timezone,
            ],
            'next'    => [
                'start_date' => null,
                'end_date'   => null,
                'timezone'   => $timezone,
            ]
        ];

        switch ($option) {
            case Team::OPTION_CHANGE_TERM_FROM_CURRENT:
                $previous = $this->getPreviousTerm();
                $current_new = $this->Team->getTermStartEndFromParam($start_term_month,
                                                                     $border_months,
                                                                     REQUEST_TIMESTAMP,
                                                                     $timezone
                );
                if ($previous) {
                    $res['current']['start_date'] = $previous['end_date'] + 1;
                }
                else {
                    $res['current']['start_date'] = $current_new['start'];
                }
                $res['current']['end_date'] = $current_new['end'] - 1;
                $next_new = $this->Team->getTermStartEndFromParam($start_term_month,
                                                                  $border_months,
                                                                  $current_new['end'] + 1,
                                                                  $timezone
                );
                $res['next']['start_date'] = $next_new['start'];
                $res['next']['end_date'] = $next_new['end'] - 1;

                break;
            case Team::OPTION_CHANGE_TERM_FROM_NEXT:
                $next = $this->getNextTerm();
                $next_new = $this->Team->getTermStartEndFromParam($start_term_month,
                                                                  $border_months,
                                                                  $next['start_date'],
                                                                  $timezone
                );
                //来期からのみの場合は、来期の開始日は据え置きで終了日のみ変更
                $res['next']['start_date'] = $next['start_date'];
                $res['next']['end_date'] = $next_new['end'] - 1;
                break;
        }
        return $res;
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
     * @param $type
     *
     * @return array|null
     */
    public function getTermData($type)
    {
        $this->_checkType($type);

        if (!$this->current_term) {
            $this->current_term = $this->_getTermByDatetime(REQUEST_TIMESTAMP);
        }

        if ($type === self::TYPE_PREVIOUS) {
            if ($this->previous_term) {
                return $this->previous_term;
            }
            if (viaIsSet($this->current_term['start_date'])) {
                $this->previous_term = $this->_getTermByDatetime(strtotime("-1 day",
                                                                           $this->current_term['start_date']));
            }
            return $this->previous_term;
        }

        if ($type === self::TYPE_NEXT) {
            if ($this->next_term) {
                return $this->next_term;
            }
            if (viaIsSet($this->current_term['end_date'])) {
                $this->next_term = $this->_getTermByDatetime(strtotime("+1 day", $this->current_term['end_date']));
            }
            return $this->next_term;
        }

        return $this->current_term;
    }

    /**
     * @param $type
     *
     * @return bool|mixed
     * @throws Exception
     */
    public function addTermData($type)
    {
        $this->_checkType($type);
        $new_start = null;
        $new_end = null;

        if ($type === self::TYPE_PREVIOUS) {
            return false;
        }

        if ($type === self::TYPE_NEXT) {
            if ($this->getTermData(self::TYPE_PREVIOUS)) {
                return false;
            }
            if (!$current = $this->getTermData(self::TYPE_CURRENT)) {
                return false;
            }
            $new_start = $current['end_date'] + 1;
            $new_end = $this->_getNewStartAndEndDate(strtotime("+1 day", $current['end_date']))['end'];
        }

        if ($type === self::TYPE_CURRENT) {
            if ($this->getTermData(self::TYPE_CURRENT)) {
                return false;
            }
            $new = $this->_getNewStartAndEndDate();
            $new_start = $new['start'];
            $new_end = $new['end'];
        }

        $team = $this->Team->getCurrentTeam();
        $res = $this->save(
            ['start_date' => $new_start,
             'end_date'   => $new_end,
             'timezone'   => $team['Team']['timezone']
            ]
        );
        return $res;
    }

    /**
     * @param $id
     * @param $type
     * @param $start_term_month
     * @param $border_months
     * @param $timezone
     *
     * @return bool|mixed
     * @throws Exception
     */
    public function updateTermData($id, $type, $start_term_month, $border_months, $timezone)
    {
        $this->_checkType($type);
        if ($type === self::TYPE_PREVIOUS) {
            return false;
        }

        $new_start = null;
        $new_end = null;
        $target_date = null;

        if ($type === self::TYPE_CURRENT) {
            if ($previous = $this->getTermData(self::TYPE_PREVIOUS)) {
                $new_start = $previous['end_date'] + 1;
                $target_date = strtotime("+1 day", $new_start);
            }
            else {
                $target_date = REQUEST_TIMESTAMP;
            }
        }

        if ($type === self::TYPE_NEXT) {
            if (!$current = $this->getTermData(self::TYPE_PREVIOUS)) {
                return false;
            }
            $new_start = $current['end_date'] + 1;
            $target_date = strtotime("+1 day", $new_start);
        }

        $new_term = $this->_getNewStartAndEndDate($target_date, $start_term_month, $border_months, $timezone);
        if (!$new_start) {
            $new_start = $new_term['start'];
        }
        $new_end = $new_term['end'];
        $this->id = $id;
        $res = $this->save(
            [
                'start_date' => $new_start,
                'end_date'   => $new_end,
                'time_zone'  => $timezone
            ]
        );
        return $res;
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
    private function _getNewStartAndEndDate($target_date = REQUEST_TIMESTAMP,
                                            $start_term_month = null,
                                            $border_months = null,
                                            $timezone = null)
    {
        $team = $this->Team->getCurrentTeam();
        if (empty($team)) {
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

        $start_date = strtotime(date("Y-{$start_term_month}-1",
                                     $target_date + $timezone * 3600)) - $timezone * 3600;
        $start_date_tmp = date("Y-m-1", $start_date + $timezone * 3600);
        $end_date = strtotime($start_date_tmp . "+ {$border_months} month") - $timezone * 3600;

        //指定日時が期間内の場合 in the case of target date include the term
        if ($start_date <= $target_date && $end_date > $target_date) {
            $term['start'] = $start_date;
            $term['end'] = $end_date - 1;
            return $term;
        }
        //指定日時が開始日より前の場合 in the case of target date is earlier than start date
        elseif ($target_date < $start_date) {
            while ($target_date < $start_date) {
                $start_date_tmp = date("Y-m-1", $start_date + $timezone * 3600);
                $start_date = strtotime($start_date_tmp . "- {$border_months} month") - $timezone * 3600;
            }
            $term['start'] = $start_date;
            $start_date_tmp = date("Y-m-1", $term['start'] + $timezone * 3600);
            $term['end'] = strtotime($start_date_tmp . "+ {$border_months} month") - $timezone * 3600 - 1;
            return $term;
        }
        //終了日が指定日時より前の場合 in the case of target date is later than end date
        elseif ($target_date > $end_date) {
            while ($target_date > $end_date) {
                $end_date_tmp = date("Y-m-1", $end_date + $timezone * 3600);
                $end_date = strtotime($end_date_tmp . "+ {$border_months} month") - $timezone * 3600;
            }
            $term['end'] = $end_date - 1;
            $end_date_tmp = date("Y-m-1", $term['end'] + $timezone * 3600);
            $term['start'] = strtotime($end_date_tmp . "- {$border_months} month") - $timezone * 3600;
            return $term;
        }
    }

    /**
     * return term data from datetime
     *
     * @param int $datetime unixtime
     *
     * @return array|null
     */
    private function _getTermByDatetime($datetime = REQUEST_TIMESTAMP)
    {
        $options = [
            'conditions' => [
                'start_date <=' => $datetime,
                'end_date >='   => $datetime,
                'team_id'       => $this->current_team_id
            ]
        ];
        $res = $this->find('first', $options);
        $res = Hash::extract($res, 'EvaluateTerm');
        return $res;
    }
}
