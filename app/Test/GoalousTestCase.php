<?php
/**
 * CakeTestCase file
 * CakePHP(tm) Tests <http://book.cakephp.org/2.0/en/development/testing.html>
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://book.cakephp.org/2.0/en/development/testing.html CakePHP(tm) Tests
 * @package       Cake.TestSuite
 * @since         CakePHP(tm) v 1.2.0.4667
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('CakeFixtureManager', 'TestSuite/Fixture');
App::uses('CakeTestFixture', 'TestSuite/Fixture');
App::uses('EvaluateTerm', 'Model');
App::uses('GoalMember', 'Model');
App::import('Service', 'GoalService');

/**
 * CakeTestCase class
 *
 * @package       Cake.TestSuite
 * @property EvaluateTerm $EvaluateTerm
 * @property GoalMember   $GoalMember
 * @property Team         $Team
 * @property GoalService  $GoalService
 */
class GoalousTestCase extends CakeTestCase
{
    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        Cache::config('team_info', ['prefix' => 'test_cache_team_info:']);
        Cache::config('user_data', ['prefix' => 'test_cache_user_data:']);
        $this->EvaluateTerm = ClassRegistry::init('EvaluateTerm');
        $this->Team = ClassRegistry::init('Team');
        $this->GoalMember = ClassRegistry::init('GoalMember');
        $this->GoalService = ClassRegistry::init('GoalService');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        $this->_clearCache();
        parent::tearDown();
    }

    function _clearCache()
    {
        Cache::clear(false, 'team_info');
        Cache::clear(false, 'user_data');
    }

    function createGoal(int $userId, array $data = [], int $termType = EvaluateTerm::TYPE_CURRENT)
    {
        $teamId = 1;
        /** @var KeyResult $KeyResult */
        $KeyResult = ClassRegistry::init('KeyResult');
        /** @var Goal $Goal */
        $Goal = ClassRegistry::init('Goal');
        $KeyResult->my_uid = $userId;
        $KeyResult->current_team_id = $teamId;
        $Goal->my_uid = $userId;
        $Goal->current_team_id = $teamId;
        $this->GoalMember->my_uid = $userId;
        $this->GoalMember->current_team_id = $teamId;
        $this->setDefaultTeamIdAndUid($userId);

        $data = $this->buildGoalData($data, $termType);
        return $this->GoalService->create($userId, $data);
    }

    function createGoalMember($data)
    {
        $default = [
            "goal_id"              => 13,
            "role"                 => "役割",
            "description"          => "詳細",
            "priority"             => 5,
            "approval_status"      => 0,
            "is_target_evaluation" => false,
            "user_id"              => $this->EvaluateTerm->my_uid,
            "team_id"              => $this->EvaluateTerm->current_team_id,
            "type"                 => 0,
        ];
        $data = am($default, $data);
        $this->GoalMember->clear();
        return $this->GoalMember->save($data);
    }

    function buildGoalData(array $data, int $termType)
    {
        //実行月の期間1ヶ月で生成される。開始日:当月の月初、終了日:当月の月末
        $default = [
            "name"             => "ゴール",
            "goal_category_id" => 1,
            "labels"           => [
                "0" => "Goalous"
            ],
            "term_type"        => "current",
            "priority"         => 5,
            "description"      => "ゴールの詳細\nです",
            "is_wish_approval" => true,
            "key_result"       => [
                "value_unit"   => 0,
                "start_value"  => 0,
                "target_value" => 100,
                "name"         => "TKR1",
                "description"  => "TKR詳細\nです",
            ],
        ];
        $data = am($default, $data);
        $termEndTime = $this->EvaluateTerm->getTermData($termType)['end_date'];
        $data['end_date'] = date('Y-m-d', $termEndTime);
        return $data;
    }

    function setupTerm($teamId = 1)
    {
        //実行月の期間1ヶ月で生成される。開始日:当月の月初、終了日:当月の月末
        $this->Team->id = $teamId;
        $this->Team->saveField('start_term_month', 1);
        $this->Team->saveField('border_months', 1);

        $this->Team->current_team_id = $teamId;
        $this->EvaluateTerm->current_team_id = $teamId;

        $this->EvaluateTerm->addTermData(EvaluateTerm::TYPE_CURRENT);
        $this->EvaluateTerm->addTermData(EvaluateTerm::TYPE_NEXT);
        $this->EvaluateTerm->addTermData(EvaluateTerm::TYPE_PREVIOUS);
    }

    /**
     * 今期の日付を拡張して登録する
     * 当日が期のどこにいるかでテスト結果に影響する場合のため
     *
     * @param int $teamId
     * @param int $beforeDays
     * @param int $afterDays
     */
    function setupCurrentTermExtendDays($teamId = 1, $beforeDays = 30, $afterDays = 30)
    {
        //実行月の期間1ヶ月で生成される。開始日:当月の月初、終了日:当月の月末
        $this->Team->id = $teamId;
        $this->Team->saveField('start_term_month', 1);
        $this->Team->saveField('border_months', 1);

        $this->Team->current_team_id = $teamId;
        $this->EvaluateTerm->current_team_id = $teamId;
        $this->EvaluateTerm->addTermData(EvaluateTerm::TYPE_CURRENT);
        $evaluateTermId = $this->EvaluateTerm->getLastInsertID();
        $term = $this->EvaluateTerm->findById($evaluateTermId);
        $term['EvaluateTerm']['start_date'] -= $beforeDays * DAY;
        $term['EvaluateTerm']['end_date'] += $afterDays * DAY;
        $this->EvaluateTerm->save($term);
        $this->EvaluateTerm->addTermData(EvaluateTerm::TYPE_NEXT);
        $this->EvaluateTerm->addTermData(EvaluateTerm::TYPE_PREVIOUS);

    }

    /**
     * 今日を今期の開始日にする
     *
     * @param int $teamId
     * @param int $termDays
     */
    function setupCurrentTermStartToday($teamId = 1, $termDays = 30)
    {
        //実行月の期間1ヶ月で生成される。開始日:当月の月初、終了日:当月の月末
        $this->Team->id = $teamId;
        $this->Team->saveField('start_term_month', 1);
        $this->Team->saveField('border_months', 1);

        $this->Team->current_team_id = $teamId;
        $this->EvaluateTerm->current_team_id = $teamId;
        $this->EvaluateTerm->addTermData(EvaluateTerm::TYPE_CURRENT);
        $evaluateTermId = $this->EvaluateTerm->getLastInsertID();
        $term = $this->EvaluateTerm->findById($evaluateTermId);
        $today = strtotime(date("Y/m/d 00:00:00")) - $term['EvaluateTerm']['timezone'] * HOUR;
        $term['EvaluateTerm']['start_date'] = $today;
        $term['EvaluateTerm']['end_date'] = $today + $termDays * DAY;
        $this->EvaluateTerm->save($term);
        $this->EvaluateTerm->addTermData(EvaluateTerm::TYPE_NEXT);
        $this->EvaluateTerm->addTermData(EvaluateTerm::TYPE_PREVIOUS);
    }

    /**
     * 今日を今期の終了日にする
     *
     * @param int $teamId
     * @param int $termDays
     */
    function setupCurrentTermEndToday($teamId = 1, $termDays = 30)
    {
        //実行月の期間1ヶ月で生成される。開始日:当月の月初、終了日:当月の月末
        $this->Team->id = $teamId;
        $this->Team->saveField('start_term_month', 1);
        $this->Team->saveField('border_months', 1);

        $this->Team->current_team_id = $teamId;
        $this->EvaluateTerm->current_team_id = $teamId;
        $this->EvaluateTerm->addTermData(EvaluateTerm::TYPE_CURRENT);
        $evaluateTermId = $this->EvaluateTerm->getLastInsertID();
        $term = $this->EvaluateTerm->findById($evaluateTermId);
        $today = strtotime(date("Y/m/d 23:59:59")) - $term['EvaluateTerm']['timezone'] * HOUR;
        $term['EvaluateTerm']['end_date'] = $today;
        $term['EvaluateTerm']['start_date'] = $today - $termDays * DAY;
        $this->EvaluateTerm->save($term);
    }

    function setDefaultTeamIdAndUid($uid = 1, $teamId = 1)
    {
        foreach (ClassRegistry::keys() as $k) {
            $obj = ClassRegistry::getObject($k);
            if ($obj instanceof AppModel) {
                $obj->current_team_id = $teamId;
                $obj->my_uid = $uid;
            }
        }
    }

    function prepareUploadImages($file_size = 1000)
    {
        $destDir = TMP . 'attached_file';
        if (!file_exists($destDir)) {
            @mkdir($destDir, 0777, true);
            @chmod($destDir, 0777);
        }
        $file_1_path = TMP . 'attached_file' . DS . 'attached_file_1.jpg';
        $file_2_path = TMP . 'attached_file' . DS . 'attached_file_2.php';
        copy(IMAGES . 'no-image.jpg', $file_1_path);
        copy(APP . WEBROOT_DIR . DS . 'test.php', $file_2_path);

        $data = [
            'file' => [
                'name'     => 'test.jpg',
                'type'     => 'image/jpeg',
                'tmp_name' => $file_1_path,
                'size'     => $file_size,
                'remote'   => true
            ]
        ];
        App::import('Service', 'AttachedFileService');
        /** @var AttachedFileService $AttachedFileService */
        $AttachedFileService = ClassRegistry::init('AttachedFileService');
        $hash_1 = $AttachedFileService->preUploadFile($data);
        $data = [
            'file' => [
                'name'     => 'test.php',
                'type'     => 'test/php',
                'tmp_name' => $file_2_path,
                'size'     => 1000,
                'remote'   => true
            ]
        ];
        $hash_2 = $AttachedFileService->preUploadFile($data);

        return [$hash_1['id'], $hash_2['id']];
    }

    /**
     * KRのプログレスを指定してゴール作成
     * プログレスの計算が必要なテストで利用
     *
     * @param     $termType
     * @param     $krProgresses
     * @param int $teamId
     * @param int $userId
     * @param int $goalMemberType
     *
     * @return mixed
     */
    function createGoalKrs($termType, $krProgresses, $teamId = 1, $userId = 1, $goalMemberType = GoalMember::TYPE_OWNER)
    {
        /** @var Goal $Goal */
        $Goal = ClassRegistry::init('Goal');
        /** @var KeyResult $KeyResult */
        $KeyResult = ClassRegistry::init('KeyResult');
        /** @var GoalMember $GoalMember */
        $GoalMember = ClassRegistry::init('GoalMember');

        $startDate = $this->EvaluateTerm->getTermData($termType)['start_date'];
        $endDate = $this->EvaluateTerm->getTermData($termType)['end_date'];
        $goalData = [
            'user_id'          => $userId,
            'team_id'          => $teamId,
            'name'             => 'ゴール1',
            'goal_category_id' => 1,
            'start_date'       => $startDate,
            'end_date'         => $endDate
        ];
        $Goal->create();
        $Goal->save($goalData);
        $goalId = $Goal->getLastInsertID();
        $GoalMember->create();
        $GoalMember->save([
            'goal_id' => $goalId,
            'user_id' => $userId,
            'team_id' => $teamId,
            'type'    => $goalMemberType,
        ]);
        $krDatas = [];
        foreach ($krProgresses as $v) {
            $krDatas[] = [
                'goal_id'       => $goalId,
                'team_id'       => $teamId,
                'user_id'       => $userId,
                'name'          => 'テストKR',
                'start_value'   => 0,
                'target_value'  => 100,
                'value_unit'    => 0,
                'current_value' => $v,
                'start_date'    => $startDate,
                'end_date'      => $endDate
            ];
        }

        $KeyResult->create();
        $KeyResult->saveAll($krDatas);
        return $goalId;
    }

    function createKr(
        $goalId,
        $teamId,
        $userId,
        $currentValue,
        $startValue = 0,
        $targetValue = 100,
        $priority = 3,
        $termType = EvaluateTerm::TYPE_CURRENT
    ) {
        /** @var KeyResult $KeyResult */
        $KeyResult = ClassRegistry::init('KeyResult');
        $startDate = $this->EvaluateTerm->getTermData($termType)['start_date'];
        $endDate = $this->EvaluateTerm->getTermData($termType)['end_date'];

        $kr = [
            'goal_id'       => $goalId,
            'team_id'       => $teamId,
            'user_id'       => $userId,
            'name'          => 'テストKR',
            'start_value'   => $startValue,
            'target_value'  => $targetValue,
            'value_unit'    => 0,
            'current_value' => $currentValue,
            'start_date'    => $startDate,
            'end_date'      => $endDate,
            'priority'      => $priority,
        ];
        $KeyResult->create();
        $KeyResult->save($kr);
        return $KeyResult->getLastInsertID();
    }

    function delKr($krId)
    {
        /** @var KeyResult $KeyResult */
        $KeyResult = ClassRegistry::init('KeyResult');
        $KeyResult->delete($krId);
    }

    function createTeam($startTermMonth = 4, $borderMonths = 6)
    {
        $team = [
            'start_term_month' => $startTermMonth,
            'border_months'    => $borderMonths,
            'type'             => 3,
            'name'             => 'Test Team.'
        ];
        $this->Team->create();
        $this->Team->save($team);
        return $this->Team->getLastInsertID();
    }

    function deleteAllTeam()
    {
        $this->Team->deleteAll(['id > ' => 0]);
    }
}
