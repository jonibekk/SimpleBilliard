<?php
App::uses('AppUtil', 'Util');
/**
 * TermMigrationShell
 *
 * @property Term $Term
 * @property Team $Team
 */
class TermMigrationShell extends AppShell
{
    const EXE_TYPE_ALL = 'all';
    const EXE_TYPE_TRANSFER = 'transfer';
    const EXE_TYPE_REMAKE = 'remake';

    public $uses = array(
        'Team',
        'Term'
    );

    public function startup()
    {
        parent::startup();
//        $this->_setModelVariable();
    }

    public function main()
    {
        // 実行処理を限定できるようにする
        $exeType = $this->getExeType();

        try {
            // 期が1つ以上存在するチームのデータ移行
            if (in_array($exeType, [self::EXE_TYPE_TRANSFER, self::EXE_TYPE_ALL])) {
                // チームを期とまとめて取得
                $teamsWithTerms = $this->findTeamsWithTerms();
                foreach ($teamsWithTerms as $team) {
                    $this->transferTerm($team);
                }
            }

            // 期が全く存在しないチームのデータ移行
            if (in_array($exeType, [self::EXE_TYPE_REMAKE, self::EXE_TYPE_ALL])) {
                // チーム取得
                $teams = $this->findTeamsNotExistTerms();
                foreach ($teams as $team) {
                    $this->remakeAllTerms($team);
                }
            }

        } catch (Exception $e) {
            // transaction rollback
            CakeLog::error($e->getMessage());
            CakeLog::error($e->getTraceAsString());
            // if return false, it will be paused to wait input.. So, exit
            exit(1);
        }

    }

    /**
     * Get execution type
     * 'transfer','remake','all'
     */
    private function getExeType(): string
    {
        if (empty($this->args[0])) {
            return self::EXE_TYPE_ALL;
        }
        $exeType = $this->args[0];
        if (in_array($exeType, [self::EXE_TYPE_TRANSFER, self::EXE_TYPE_REMAKE, self::EXE_TYPE_ALL])) {
            return $exeType;
        }
        echo 'Invalid argument. Specify「transfer」or「remake」or「all」.' . PHP_EOL;
        exit();
    }

    /**
     * Find teams with terms
     */
    private function findTeamsWithTerms(): array
    {
        $teams = $this->Team->find('all', [
            'fields'     => [
                'Team.id',
                'Team.start_term_month',
                'Team.border_months',
                'Team.timezone',
                'Team.created',
                'EvaluateTerm.id',
                'EvaluateTerm.team_id',
                'EvaluateTerm.start_date',
                'EvaluateTerm.end_date',
                'EvaluateTerm.timezone',
                'EvaluateTerm.evaluate_status',
            ],
            'conditions' => [
                'Team.del_flg' => false,
            ],
            'joins'      => [
                [
                    'type'       => 'INNER',
                    'table'      => 'evaluate_terms',
                    'alias'      => 'EvaluateTerm',
                    'conditions' => [
                        'EvaluateTerm.team_id = Team.id',
                    ]
                ],
            ],
            'order'      => ['Team.id', 'EvaluateTerm.start_date'],
        ]);

        $teamsWithTerms = [];
        foreach ($teams as $team) {
            $teamId = $team['Team']['id'];
            if (empty($teamsWithTerms[$teamId])) {
                $teamsWithTerms[$teamId] = $team['Team'];
            }
            $teamsWithTerms[$teamId]['terms'][] = $team['EvaluateTerm'];
        }
        return $teamsWithTerms;
    }

    /**
     * Find teams not exist terms
     */
    private function findTeamsNotExistTerms(): array
    {
        $teams = $this->Team->find('all', [
            'fields'     => [
                'Team.id',
                'Team.start_term_month',
                'Team.border_months',
                'Team.timezone',
                'Team.created',
            ],
            'conditions' => [
                'Team.del_flg'    => false,
                'EvaluateTerm.id' => null,
            ],
            'joins'      => [
                [
                    'type'       => 'LEFT',
                    'table'      => 'evaluate_terms',
                    'alias'      => 'EvaluateTerm',
                    'conditions' => [
                        'EvaluateTerm.team_id = Team.id',
                    ]
                ],
            ],
            'order'      => ['Team.id', 'EvaluateTerm.start_date'],
        ]);

        return Hash::extract($teams, '{n}.Team');
    }

    /**
     * Data migration from old evaluate_terms table to new terms table
     *
     * @param array $team
     *
     * @return bool
     * @throws Exception
     * @internal param array $term
     */
    private function transferTerm(array $team)
    {
        try {
            $this->Term->begin();
            // 何回でも実行できるようにデータをリセットする
            $this->Term->deleteAll(['team_id' => $team['id']]);

            $lastIndex = count($team['terms']) - 1;
            $prevTerm = [];
            foreach ($team['terms'] as $i => $term) {
                // 存在する期を新しいテーブル保存用にデータを加工
                $transferTerm = [
                    'team_id'         => $team['id'],
                    'evaluate_status' => $term['evaluate_status']
                ];

                // 今まで開始日・終了日はUTCタイムスタンプ - (タイムゾーン * 時)が入っていたので、 + (タイムゾーン * 時)した後日付文字列に変換
                $transferTerm['start_date'] = $this->getDateByTimestamp($term['start_date'], $term['timezone'], true);
                $monthFirstDate = date('Y-m-d', strtotime('first day of ' . $transferTerm['start_date']));
                $transferTerm['end_date'] = $this->getDateByTimestamp($term['end_date'], $term['timezone'], true);
                $monthLastDate = date('Y-m-d', strtotime('last day of ' . $transferTerm['end_date']));

                // 開始日が月初ではない、または終了日が月末でないかチェック
                if ($transferTerm['start_date'] !== $monthFirstDate || $transferTerm['end_date'] !== $monthLastDate) {
                    throw new Exception(sprintf(
                        'Failed to save term. data:%s',
                        var_export(compact('transferTerm', 'monthFirstDate', 'monthLastDate'), true)
                    ));
                }
                $newTerms[] = $transferTerm;

                // 存在する最初の期の場合
                if ($i == 0) {
                    // 最初の期の開始日がチーム作成日より後の場合、それまでの期が登録されていないということなので、追加
                    $startDate = $transferTerm['start_date'];
                    $teamCreatedDate = AppUtil::dateYmd($team['created']);
                    while ($startDate > $teamCreatedDate) {
                        $newTerm = $this->buildPrevTermByNextStartDate($startDate, $team);
                        $newTerms[] = $newTerm;
                        $startDate = $newTerm['start_date'];
                    }

                    // 存在する最後の期の場合
                } elseif ($i == $lastIndex) {
                    // 最後の期の終了日が現在日より前の場合、今期までの期が登録されていないということなので、追加
                    $endDate = $transferTerm['end_date'];
                    $currentDate = AppUtil::dateYmd(time());
                    $newTermsAfterLast = [];
                    // 今期まで期を追加
                    while ($endDate < $currentDate) {
                        $newTerm = $this->buildNextTermByPrevEndDate($endDate, $team);
                        $newTermsAfterLast[] = $newTerm;
                        $endDate = $newTerm['end_date'];
                    }

                    // 来期を追加
                    if (!empty($newTermsAfterLast)) {
                        // Add next term
                        $newTermsAfterLast[] = $this->buildNextTermByPrevEndDate($endDate, $team);
                        $newTerms = array_merge($newTerms, $newTermsAfterLast);
                    }
                } elseif (!empty($prevTerm)) {
                    // 歯抜けの期が存在するか
                    $diffDays = (strtotime($transferTerm['start_date']) - strtotime($prevTerm['end_date'])) / (60 * 60 * 24);
                    if ($diffDays > 1) {
                        $this->log(compact('transferTerm', 'prevTerm', 'diffDays'));
                        throw new Exception(sprintf("Exist term of missing teeth. data:%s"
                            , var_export(compact('prevTerm', 'transferTerm', 'diffDays'), true)
                        ));
                    }
                }
                $prevTerm = $transferTerm;
            }

            // 順番がばらばらなので開始日昇順で並び替えて一括保存
            $keyStartDate = Hash::extract($newTerms, '{n}.start_date');
            array_multisort($keyStartDate, SORT_ASC, $newTerms);
            if (!$this->Term->bulkInsert($newTerms)) {
                throw new Exception(sprintf(
                    'Failed to transfer terms . data:%s',
                    var_export(compact('newTerms', 'team'), true)
                ));
            }

            $this->Term->commit();
        } catch (Exception $e) {
            $this->Term->rollback();
            throw new Exception(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
        }
    }

    /**
     * Remake all terms for team not exist terms
     *
     * @param array $team
     *
     * @return bool
     * @throws Exception
     * @internal param array $term
     */
    private function remakeAllTerms(array $team)
    {
        try {
            $this->Term->begin();
            // 何回でも実行できるようにデータをリセットする
            $this->Term->deleteAll(['team_id' => $team['id']]);

            // チーム作成時の期を作成
            $newTerms = [];
            $firstTerm = $this->buildFirstTerm($team);
            $newTerms[] = $firstTerm;
            // 来期までの期を作成
            $startDate = $firstTerm['start_date'];
            $currentDate = date('Y-m-d');
            // 来期までの期を作るためdo while文を使用
            do {
                $startDate = date('Y-m-d', strtotime($startDate . " +" . $team['border_months'] . " month"));
                $newTerms[] = [
                    'team_id'    => $team['id'],
                    'start_date' => $startDate,
                    'end_date'   => $this->getEndDateByStartDate($startDate, $team['border_months'])
                ];
            } while ($startDate < $currentDate);

            // 期を一括保存
            if (!$this->Term->bulkInsert($newTerms)) {
                throw new Exception(sprintf(
                    'Failed to remake terms . data:%s',
                    var_export(compact('newTerms', 'team'), true)
                ));
            }

            $this->Term->commit();
        } catch (Exception $e) {
            $this->Term->rollback();
            throw new Exception(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
        }
    }

    /**
     * Build first term
     *
     * @param array $team
     *
     * @return array
     */
    private function buildFirstTerm(array $team): array
    {
        $newTerm = ['team_id' => $team['id']];
        $borderMonths = $team['border_months'];
        $startTermMonth = $team['start_term_month'];

        $teamCreatedDate = date('Y-m-d', $team['created']);
        $startDate = date("Y-" . sprintf('%02d', $startTermMonth) . "-1", strtotime($teamCreatedDate));
        $endDate = $this->getEndDateByStartDate($startDate, $borderMonths);

        //チーム作成日時が期間内の場合 in the case of target date include the term
        if ($startDate <= $teamCreatedDate && $endDate > $teamCreatedDate) {
            $newTerm['start_date'] = $startDate;
            $newTerm['end_date'] = $endDate;

            //チーム作成日時が開始日より前の場合 in the case of target date is earlier than start date
        } elseif ($teamCreatedDate < $startDate) {
            while ($teamCreatedDate < $startDate) {
                $startDate = date('Y-m-d', strtotime($startDate . "- {$borderMonths} month"));
            }
            $newTerm['start_date'] = $startDate;
            $newTerm['end_date'] = $this->getEndDateByStartDate($startDate, $borderMonths);

            //終了日がチーム作成日時より後の場合 in the case of target date is later than end date
        } elseif ($teamCreatedDate > $endDate) {
            while ($teamCreatedDate > $endDate) {
                $endDateTmp = date("Y-m-1", strtotime($endDate));
                $endDate = date('Y-m-t', strtotime($endDateTmp . "+ {$borderMonths} month"));
            }
            $newTerm['start_date'] = $this->getStartDateByEndDate($endDate, $borderMonths);
            $newTerm['end_date'] = $endDate;
        }

        return $newTerm;
    }

    /**
     * Get end date by start date in same term
     *
     * @param string $startDate
     * @param int    $borderMonths
     *
     * @return string
     */
    private function getEndDateByStartDate(string $startDate, int $borderMonths): string
    {
        return date('Y-m-t', strtotime($startDate . " +" . ($borderMonths - 1) . " month"));
    }

    /**
     * Get start date by end date in same term
     *
     * @param string $endDate
     * @param int    $borderMonths
     *
     * @return string
     */
    private function getStartDateByEndDate(string $endDate, int $borderMonths): string
    {
        return date('Y-m-1', strtotime($endDate . " -" . ($borderMonths - 1) . " month"));
    }

    /**
     * Build previous term
     *
     * @param string $startDate
     * @param array  $team
     *
     * @return array
     */
    private function buildPrevTermByNextStartDate(string $startDate, array $team): array
    {
        $newTerm = [
            'team_id'         => $team['id'],
            'evaluate_status' => Term::STATUS_EVAL_NOT_STARTED
        ];
        $newTerm['start_date'] = date("Y-m-d", strtotime($startDate . " -" . $team['border_months'] . " month"));
        $newTerm['end_date'] = date('Y-m-t', strtotime($startDate . " -1 month"));

        return $newTerm;
    }

    /**
     * Build next term
     *
     * @param string $endDate
     * @param array  $team
     *
     * @return array
     */
    private function buildNextTermByPrevEndDate(string $endDate, array $team): array
    {
        $newTerm = [
            'team_id'         => $team['id'],
            'evaluate_status' => Term::STATUS_EVAL_NOT_STARTED
        ];
        $firstDate = date('Y-m-01', strtotime($endDate));
        $newTerm['start_date'] = date('Y-m-d', strtotime($firstDate . " +1 month"));

        $newTerm['end_date'] = date("Y-m-t", strtotime($firstDate . " +" . $team['border_months'] . " month"));

        return $newTerm;
    }

    /**
     * Get date by local timestamp
     *
     * @param int   $timestamp
     * @param float $timezone
     * @param bool  $isAddition
     *
     * @return string
     */
    private function getDateByTimestamp(int $timestamp, float $timezone, $isAddition = false): string
    {
        $localTime = $isAddition ? $timestamp + ($timezone * HOUR) : $timestamp - ($timezone * HOUR);
        return AppUtil::dateYmd($localTime);
    }

}
