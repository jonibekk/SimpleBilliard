<?php
App::import('Service', 'AppService');
App::uses('Evaluation', 'Model');
App::uses('EvaluationSetting', 'Model');
App::uses('Term', 'Model');
App::uses('TeamMember', 'Model');
App::uses('User', 'Model');
App::import('Service', 'ExperimentService');

use Goalous\Enum as Enum;

class EvaluationService extends AppService
{
    /* Evaluation stage */
    const STAGE_NONE = 0;
    const STAGE_SELF_EVAL = 1;
    const STAGE_EVALUATOR_EVAL = 2;
    const STAGE_FINAL_EVALUATOR_EVAL = 3;
    const STAGE_COMPLETE = 4;

    private $cachedEvalStages = [];

    /**
     * 認定リストのステータスをフォーマット
     *
     * @param int $termId
     * @param int $userId
     *
     * @return array
     */
    function getEvalStatus(int $termId, int $userId): array
    {
        /** @var  Evaluation $Evaluation */
        $Evaluation = ClassRegistry::init('Evaluation');
        /** @var  EvaluationSetting $EvaluationSetting */
        $EvaluationSetting = ClassRegistry::init('EvaluationSetting');

        $isFixedEvaluationOrder = $EvaluationSetting->isFixedEvaluationOrder();
        $evaluations = $Evaluation->getEvaluationListForIndex($termId, $userId);
        $evaluations = Hash::combine($evaluations, '{n}.id', '{n}');
        if (empty($evaluations)) {
            return [];
        }

        $flow = [];
        $evaluator_index = 1;
        $status_text = ['your_turn' => false, 'body' => null];
        $myEval = [];
        //update flow
        foreach ($evaluations as $val) {
            $name = Evaluation::$TYPE[$val['evaluate_type']]['index'];
            $otherEvaluator = false;
            if ($val['evaluate_type'] == Evaluation::TYPE_EVALUATOR) {
                $user_name = $val['evaluator_user']['display_username'];
                if ($val['evaluator_user_id'] == $Evaluation->my_uid) {
                    $name = __("You");
                } else {
                    $name = "${evaluator_index}(${user_name})";
                    $otherEvaluator = true;
                }
                $evaluator_index++;
            } //自己評価で被評価者が自分以外の場合は「メンバー」
            elseif ($val['evaluate_type'] == Evaluation::TYPE_ONESELF && $val['evaluatee_user_id'] != $Evaluation->my_uid) {
                $name = __('Members');
            }
            $flow[] = [
                'name'            => $name,
                'status'          => $val['status'],
                'this_turn'       => $val['my_turn_flg'],
                'other_evaluator' => $otherEvaluator,
                'evaluate_type'   => $val['evaluate_type']
            ];

            if ($val['evaluator_user_id'] == $Evaluation->my_uid) {
                $myEval = $val;
            }
            //update status_text
            if ($val['my_turn_flg'] === false) {
                continue;
            }
            if ($isFixedEvaluationOrder) {
                if ($val['evaluator_user_id'] != $Evaluation->my_uid) {
                    $status_text['body'] = __("Waiting for the evaluation by %s.", $name);
                    continue;
                }
                //your turn
                $status_text['your_turn'] = true;
                switch ($val['evaluate_type']) {
                    case Evaluation::TYPE_ONESELF:
                        $status_text['body'] = __("Please evaluate yourself.");
                        break;
                    case Evaluation::TYPE_EVALUATOR:
                        $status_text['body'] = __("Please evaluate.");
                        break;
                }
            }
        }

        $evalStage = $this->getEvalStageIfNotFixedEvalOrder($termId, $userId);
        if (!$isFixedEvaluationOrder) {
            switch ($evalStage) {
                case self::STAGE_SELF_EVAL:
                    if ($userId == $Evaluation->my_uid) {
                        $status_text['body'] = __("Please evaluate yourself.");
                    } else {
                        $status_text['body'] = __("Waiting for the evaluation by %s.", __("Members"));
                    }
                    break;
                case self::STAGE_EVALUATOR_EVAL:
                    if ($myEval['evaluate_type'] == Evaluation::TYPE_EVALUATOR
                        && $myEval['status'] != Enum\Model\Evaluation\Status::DONE) {
                        $status_text['body'] = __("Please evaluate.");
                    } else {
                        $status_text['body'] = __("Waiting for the evaluation by %s.", __("Evaluator"));
                    }
                    break;
                case self::STAGE_FINAL_EVALUATOR_EVAL:
                    $status_text['body'] = __("Waiting for the evaluation by %s.", __("Final Evaluator"));
                    break;
            }
        }

        /** @var  User $User */
        $User = ClassRegistry::init('User');
        $user = $User->getProfileAndEmail($userId);
        $res = array_merge(['flow' => $flow, 'status_text' => $status_text, 'eval_stage' => $evalStage], $user);
        return $res;
    }

    /**
     * Get stage who can input evaluation(Evaluatee, Evaluator, Final Evaluator)
     *
     * @param int $termId
     * @param int $evaluateeId
     *
     * @return int
     */
    function getEvalStageIfNotFixedEvalOrder(int $termId, int $evaluateeId): int
    {
        /** @var  Evaluation $Evaluation */
        $Evaluation = ClassRegistry::init('Evaluation');
        /** @var  EvaluationSetting $EvaluationSetting */
        $EvaluationSetting = ClassRegistry::init('EvaluationSetting');

        $key = $termId . '-' . $evaluateeId;
        if (isset($this->cachedEvalStages[$key])) {
            return $this->cachedEvalStages[$key];
        }

        $isFixedEvaluationOrder = $EvaluationSetting->isFixedEvaluationOrder();
        if ($isFixedEvaluationOrder) {
            $this->cachedEvalStages[$key] = self::STAGE_NONE;
            return self::STAGE_NONE;
        }

        $selfEval = $Evaluation->getUnique($evaluateeId, $evaluateeId, $termId, Evaluation::TYPE_ONESELF);
        if ((int)$selfEval['status'] !== Enum\Model\Evaluation\Status::DONE) {
            $this->cachedEvalStages[$key] = self::STAGE_SELF_EVAL;
            return self::STAGE_SELF_EVAL;
        }

        if (!$Evaluation->isCompleteEvalByEvaluator($termId, $evaluateeId)) {
            $this->cachedEvalStages[$key] = self::STAGE_EVALUATOR_EVAL;
            return self::STAGE_EVALUATOR_EVAL;
        }

        if (!$Evaluation->isCompleteEvalByFinalEvaluator($termId, $evaluateeId)) {
            $this->cachedEvalStages[$key] = self::STAGE_FINAL_EVALUATOR_EVAL;
            return self::STAGE_FINAL_EVALUATOR_EVAL;
        }
        $this->cachedEvalStages[$key] = self::STAGE_COMPLETE;
        return self::STAGE_COMPLETE;
    }

    /**
     * @param int $termId
     *
     * @return array
     */
    function getEvaluateeEvalStatusAsEvaluator(int $termId): array
    {
        /** @var  Evaluation $Evaluation */
        $Evaluation = ClassRegistry::init('Evaluation');
        $evaluateeList = $Evaluation->getEvaluateeListEvaluableAsEvaluator($termId);

        return $this->getEvaluateesFromUserIds($termId, $evaluateeList);
    }

    /**
     * Return the evaluatees of who have a coach as $coachUserId
     *
     * @param int $termId
     * @param int $coachUserId
     * @param     $activeOnlyFlag
     *
     * @return array
     */
    function getEvaluateesFromCoachUserId(int $termId, int $coachUserId, bool $activeOnlyFlag = false): array
    {

        /** @var User $User */
        $User = ClassRegistry::init('User');
        /** @var Term $Term */
        $Term = ClassRegistry::init('Term');
        /** @var  TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init('TeamMember');
        $teamMembers = $TeamMember->findAllByCoachUserId($coachUserId);
        $coacheeUserIds = Hash::extract($teamMembers, '{n}.TeamMember.user_id');

        $team_id = Hash::get($Term->getById($termId), ['team_id']);
        $userIds = $activeOnlyFlag ? Hash::extract($User->filterUsersOnTeamActivity($team_id, $coacheeUserIds,
            true), '{n}.User.id') : $coacheeUserIds;

        return $this->getEvaluateesFromUserIds($termId, $userIds);
    }

    /**
     * Fetch the array of Users data with Evaluation status
     * from teams.id and users.ids
     *
     * @param int   $termId
     * @param int[] $userIds
     *
     * @return array
     */
    private function getEvaluateesFromUserIds(int $termId, array $userIds): array
    {
        /** @var  User $User */
        $User = ClassRegistry::init('User');
        $evaluatees = [];
        foreach ($userIds as $userId) {
            $user = $User->getProfileAndEmail($userId);
            $evaluation = $this->getEvalStatus($termId, $userId);
            $evaluatees[] = array_merge($user, $evaluation);
        }
        return $evaluatees;
    }

    /**
     * 評価期間中かどうか判定
     * - 頻繁に確認されるフラグなので結果をキャッシュする
     * - キャッシュの保持期限は期の終わり
     *
     * @return boolean
     */
    function isStarted(): bool
    {
        /** @var  Term $Term */
        $Term = ClassRegistry::init('Term');
        /** @var  Team $Team */
        $Team = ClassRegistry::init('Team');

        $cachedData = Cache::read($Term->getCacheKey(CACHE_KEY_IS_STARTED_EVALUATION, true), 'team_info');
        if ($cachedData !== false) {
            // $isStartedEvaluation will be created by extracting $cachedData
            extract($cachedData);
        } else {
            $currentTermId = $Term->getCurrentTermId();
            $isStartedEvaluation = $Term->isStartedEvaluation($currentTermId);

            // 結果をキャッシュに保存
            $currentTerm = $Term->getCurrentTermData();
            $timezone = $Team->getTimezone();
            $duration = $Term->makeDurationOfCache($currentTerm['end_date'], $timezone);
            Cache::set('duration', $duration, 'team_info');
            Cache::write($Term->getCacheKey(CACHE_KEY_IS_STARTED_EVALUATION, true),
                compact('isStartedEvaluation'), 'team_info');
        }

        /** @noinspection PhpUndefinedVariableInspection */
        return $isStartedEvaluation;
    }

    /**
     * 評価期間中かどうか判定
     * - 頻繁に確認されるフラグなので結果をキャッシュする
     * - キャッシュの保持期限は期の終わり
     *
     * @param int $userId
     * @param int $evaluateeId
     * @param int $termId
     *
     * @return array
     */
    function findEvaluations(int $userId, int $evaluateeId, int $termId): array
    {
        /** @var  Evaluation $Evaluation */
        $Evaluation = ClassRegistry::init('Evaluation');
        /** @var  EvaluationSetting $EvaluationSetting */
        $EvaluationSetting = ClassRegistry::init('EvaluationSetting');
        /** @var  Term $Term */
        $Term = ClassRegistry::init('Term');
        $term = $Term->getById($termId);
        if (empty($term)) {
            return [];
        }

        // Get all evaluations
        if ($EvaluationSetting->isShowAllEvaluationBeforeFreeze()
            || (int)$term['evaluate_status'] !== Enum\Model\Term\EvaluateStatus::IN_PROGRESS) {
            $evaluations = $Evaluation->getEvaluations($termId, $evaluateeId);
            return $evaluations;
        }

        // Case: login user is evaluatee (only my evaluation)
        if ($userId == $evaluateeId) {
            return $Evaluation->getEvaluationsForEvaluatee($termId, $evaluateeId);
        }

        // Case: login user is evaluator (evaluatee + my evaluation)
        $isFixedEvaluationOrder = $EvaluationSetting->isFixedEvaluationOrder();
        $evaluations = $Evaluation->getEvaluationsForEvaluatorAndEvaluatee($termId, $evaluateeId, $userId, $isFixedEvaluationOrder);
        return $evaluations;

    }

    /**
     * Check whether login user edit evaluation
     *
     * @param $evaluateTermId
     * @param $evaluateeId
     * @param $userId
     *
     * @return bool
     */
    function isEditable(int $evaluateTermId, int $evaluateeId, int $userId): bool
    {
        /** @var  Evaluation $Evaluation */
        $Evaluation = ClassRegistry::init('Evaluation');
        /** @var  EvaluationSetting $EvaluationSetting */
        $EvaluationSetting = ClassRegistry::init('EvaluationSetting');
        /** @var  Term $Term */
        $Term = ClassRegistry::init('Term');

        $term = $Term->getById($evaluateTermId);
        if (empty($term)) {
            return false;
        }
        // check frozen
        if ((int)$term['evaluate_status'] !== Enum\Model\Term\EvaluateStatus::IN_PROGRESS) {
            return false;
        }
        $evaluation = $Evaluation->getUnique($evaluateeId, $userId, $evaluateTermId);
        // Check if exist evaluation
        if (empty($evaluation)) {
            return false;
        }

        // If not fixed evaluation order
        if (!$EvaluationSetting->isFixedEvaluationOrder()) {
            // login user = evaluatee
            if ($evaluateeId == $userId) {
                // evaluatee can't edit if even one of the evaluators evaluated.
                return $Evaluation->countCompletedByEvaluators($evaluateTermId, $evaluateeId) == 0;
            } else {
                $selfEvaluation = $Evaluation->getUnique($evaluateeId, $evaluateeId, $evaluateTermId);
                if ((int)Hash::get($selfEvaluation, 'status') === Enum\Model\Evaluation\Status::DONE) {
                    return true;
                }
                return false;
            }
        }

        // Check my turn if all evaluation show before freeze
        $evaluations = $Evaluation->getEvaluations($evaluateTermId, $evaluateeId);
        $nextEvaluatorId = $Evaluation->getNextEvaluatorId($evaluateTermId, $evaluateeId);
        $isMyTurn = !empty(Hash::extract($evaluations,
            "{n}.{n}.Evaluation[my_turn_flg=true][evaluator_user_id={$userId}]"));
        $isNextTurn = !empty(Hash::extract($evaluations,
            "{n}.{n}.Evaluation[my_turn_flg=true][evaluator_user_id={$nextEvaluatorId}]"));
        if ($isMyTurn || $isNextTurn) {
            return true;
        }
        return false;
    }

    /**
     * Start the evaluation that specified terms.id evaluation
     *
     * @param int $teamId
     * @param int $termId
     *
     * @throws Exception, RuntimeException
     * @return bool
     */
    function startEvaluation(int $teamId, int $termId): bool
    {
        /** @var  Term $Term */
        $Term = ClassRegistry::init('Term');
        /** @var  Team $Team */
        $Team = ClassRegistry::init('Team');
        /** @var  TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init('TeamMember');
        /** @var  EvaluationSetting $EvaluationSetting */
        $EvaluationSetting = ClassRegistry::init('EvaluationSetting');
        /** @var  Evaluator $Evaluator */
        $Evaluator = ClassRegistry::init('Evaluator');
        /** @var  Evaluation $Evaluation */
        $Evaluation = ClassRegistry::init('Evaluation');
        /** @var ExperimentService $ExperimentService */
        $ExperimentService = ClassRegistry::init("ExperimentService");

        if (!$ExperimentService->isDefined("EnableEvaluationFeature")) {
            throw new RuntimeException("Evaluation setting of the team is not enabled. Please contact the team administrator.");
        }

        if (!$EvaluationSetting->isEnabled()) {
            throw new RuntimeException("evaluation is not enabled");
        }

        $term = $Term->getById($termId);

        if (empty($term)) {
            throw new RuntimeException(sprintf('term(%d) is empty', $termId));
        }
        if (intval($term['team_id']) !== $teamId) {
            throw new RuntimeException(sprintf('term(%d) is not belongs to team(%d)', $termId, $teamId));
        }
        if (intval($term['evaluate_status']) !== Enum\Model\Term\EvaluateStatus::NOT_STARTED) {
            throw new RuntimeException(sprintf('term(%d) evaluation is already started', $termId));
        }

        $team_members_list = $TeamMember->getAllMemberUserIdList(true, true, true, $teamId);
        $evaluators = [];
        if ($EvaluationSetting->isEnabledEvaluator()) {
            $evaluators = $Evaluator->getEvaluatorsCombined($teamId);
            $evaluators = $Evaluation->appendEvaluatorAccessibleGoals($evaluators);
        }
        $all_evaluations = [];
        try {
            $this->TransactionManager->begin();

            // Creating a record data of evaluations table by each user
            foreach ($team_members_list as $uid) {
                $all_evaluations = array_merge(
                    $all_evaluations,
                    $Evaluation->getAddRecordsOfEvaluatee($uid, $termId, $evaluators)
                );
            }
            if (empty($all_evaluations)) {
                throw new RuntimeException('evaluations are empty');
            }
            $res = $Evaluation->saveAll($all_evaluations);
            if (false === $res) {
                throw new RuntimeException("failed to save evaluations");
            }
            // TODO: fix not to use turn flg. This is not simple.
            // Currently, we use turn flg if only fixed evaluation order
            if ($Team->EvaluationSetting->isFixedEvaluationOrder()) {
                //set my_turn
                $Evaluation->updateAll(['Evaluation.my_turn_flg' => true],
                    [
                        'Evaluation.team_id'   => $teamId,
                        'Evaluation.term_id'   => $termId,
                        'Evaluation.index_num' => 0,
                    ]
                );
            }
            $Term->changeToInProgress($termId);

            $this->TransactionManager->commit();
        } catch (Exception $e) {
            $this->TransactionManager->rollback();
            throw $e;
        }

        return true;
    }
}
