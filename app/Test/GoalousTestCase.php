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
        $this->KeyResult->my_uid = $userId;
        $this->KeyResult->current_team_id = $teamId;
        $this->Goal->my_uid = $userId;
        $this->Goal->current_team_id = $teamId;
        $this->GoalMember->my_uid = $userId;
        $this->GoalMember->current_team_id = $teamId;
        $this->setDefaultTeamIdAndUid($userId);

        $data = $this->buildGoalData($data, $termType);
        return $this->GoalService->create($userId, $data);
    }

    function createGoalMember($data)
    {
        $default = [
            "goal_id" => 13,
            "role" => "役割",
            "description" => "詳細",
            "priority" => 5,
            "approval_status" => 0,
            "is_target_evaluation" => false,
            "user_id" => $this->EvaluateTerm->my_uid,
            "team_id" => $this->EvaluateTerm->current_team_id,
            "type" => 0,
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

    function setupTerm()
    {
        //実行月の期間1ヶ月で生成される。開始日:当月の月初、終了日:当月の月末
        $this->EvaluateTerm->addTermData(EvaluateTerm::TYPE_PREVIOUS);
        $this->EvaluateTerm->addTermData(EvaluateTerm::TYPE_CURRENT);
        $this->EvaluateTerm->addTermData(EvaluateTerm::TYPE_NEXT);
    }

    function setDefaultTeamIdAndUid($uid = 1, $teamId = 1)
    {
        $this->Team->current_team_id = $teamId;
        $this->Team->my_uid = $uid;
        $this->EvaluateTerm->current_team_id = $teamId;
        $this->EvaluateTerm->my_uid = $uid;
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

}
