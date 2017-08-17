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
App::uses('Term', 'Model');
App::uses('GoalMember', 'Model');
App::uses('Topic', 'Model');
App::uses('Message', 'Model');
App::uses('Invite', 'Model');
App::uses('PaymentSetting', 'Model');
App::uses('CreditCard', 'Model');
App::uses('ChargeHistory', 'Model');
App::uses('GlRedis', 'Model');
App::import('Service', 'GoalService');
App::uses('AppUtil', 'Util');

/**
 * CakeTestCase class
 *
 * @package       Cake.TestSuite
 * @property Term        $Term
 * @property GoalMember  $GoalMember
 * @property Team        $Team
 * @property GoalService $GoalService
 * @property GlRedis     $GlRedis
 * @property CreditCardService  $CreditCardService
 */
class GoalousTestCase extends CakeTestCase
{
    // Card with specific error for Stripe API test
    // https://stripe.com/docs/testing#cards-responses
    // Error Cards
    const CARD_DECLINED = "4000000000000002";
    const CARD_INCORRECT_CVC = "4000000000000127";
    const CARD_EXPIRED = "4000000000000069";
    const CARD_PROCESSING_ERROR = "4000000000000119";
    const CARD_INCORRECT_NUMBER = "4242424242424241";
    const CARD_CHARGE_FAIL = "4000000000000341";
    // Valid Cards
    const CARD_VISA = "4012888888881881";
    const CARD_MASTERCARD = "5555555555554444";

    const ERR_CODE_CARD_DECLINED = 'card_declined';
    const ERR_CODE_CARD_INCORRECT_CVC = "incorrect_cvc";
    const ERR_CODE_CARD_EXPIRED = 'expired_card';
    const ERR_CODE_CARD_PROCESSING_ERROR = 'processing_error';

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        ini_set('memory_limit', '512M');
        parent::setUp();
        Cache::config('user_data', ['prefix' => ENV_NAME . ':test:cache_user_data:']);
        Cache::config('team_info', ['prefix' => ENV_NAME . ':test:cache_team_info:']);
        $this->Term = ClassRegistry::init('Term');
        $this->Team = ClassRegistry::init('Team');
        $this->GoalMember = ClassRegistry::init('GoalMember');
        $this->Topic = ClassRegistry::init('Topic');
        $this->Message = ClassRegistry::init('Message');
        $this->Invite = ClassRegistry::init('Invite');
        $this->GoalService = ClassRegistry::init('GoalService');
        $this->GlRedis = ClassRegistry::init('GlRedis');
        $this->GlRedis->changeDbSource('redis_test');
        $this->CreditCardService = ClassRegistry::init('CreditCardService');
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

    function createGoal(int $userId, array $data = [], int $termType = Term::TYPE_CURRENT)
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

    function createSimpleGoal(array $data = [], int $termType = Term::TYPE_CURRENT)
    {
        /** @var Goal $Goal */
        $Goal = ClassRegistry::init('Goal');

        $Goal->my_uid = 1;
        $Goal->current_team_id = 1;
        $default = [
            "name"             => "ゴール",
            "goal_category_id" => 1,
            "description"      => "ゴールの詳細\nです"
        ];
        $data = am($default, $data);
        $Goal->create();
        $Goal->save($data, false);
        return $Goal->getLastInsertID();
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
            "user_id"              => $this->Term->my_uid,
            "team_id"              => $this->Term->current_team_id,
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
        $termEndDate = $this->Term->getTermData($termType)['end_date'];
        $data['end_date'] = $termEndDate;
        return $data;
    }

    function setupTerm($teamId = 1)
    {
        //実行月の期間1ヶ月で生成される。開始日:当月の月初、終了日:当月の月末
        $this->Team->id = $teamId;
        $this->Team->saveField('start_term_month', 1);
        $this->Team->saveField('border_months', 1);

        $this->Team->current_team_id = $teamId;
        $this->Team->current_team = [];
        $this->Term->current_team_id = $teamId;
        $this->Term->resetTermProperty(Term::TYPE_CURRENT);
        $this->Term->resetTermProperty(Term::TYPE_NEXT);
        $this->Term->resetTermProperty(Term::TYPE_PREVIOUS);

        $this->Term->addTermData(Term::TYPE_CURRENT);
        $this->Term->addTermData(Term::TYPE_NEXT);
        $this->Term->addTermData(Term::TYPE_PREVIOUS);
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
        $this->Team->current_team = [];
        $this->Term->current_team_id = $teamId;
        $this->Term->resetTermProperty(Term::TYPE_CURRENT);
        $this->Term->resetTermProperty(Term::TYPE_NEXT);
        $this->Term->resetTermProperty(Term::TYPE_PREVIOUS);

        $this->Term->addTermData(Term::TYPE_CURRENT);
        $evaluateTermId = $this->Term->getLastInsertID();
        $term = $this->Term->findById($evaluateTermId);
        $term['Term']['start_date'] = AppUtil::dateYmd(strtotime("{$term['Term']['start_date']} -{$beforeDays} days"));
        $term['Term']['end_date'] = AppUtil::dateYmd(strtotime("{$term['Term']['end_date']} +{$afterDays} days"));
        $this->Term->save($term);
        $this->Term->addTermData(Term::TYPE_NEXT);
        $this->Term->addTermData(Term::TYPE_PREVIOUS);
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
        $this->Team->current_team = [];
        $this->Term->current_team_id = $teamId;
        $this->Term->resetTermProperty(Term::TYPE_CURRENT);
        $this->Term->resetTermProperty(Term::TYPE_NEXT);
        $this->Term->resetTermProperty(Term::TYPE_PREVIOUS);

        $this->Term->addTermData(Term::TYPE_CURRENT);
        $evaluateTermId = $this->Term->getLastInsertID();
        $term = $this->Term->findById($evaluateTermId);
        $today = strtotime(date("Y/m/d 00:00:00")) - $term['Term']['timezone'] * HOUR;
        $term['Term']['start_date'] = $today;
        $term['Term']['end_date'] = $today + $termDays * DAY;
        $this->Term->save($term);
        $this->Term->addTermData(Term::TYPE_NEXT);
        $this->Term->addTermData(Term::TYPE_PREVIOUS);
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
        $this->Team->current_team = [];
        $this->Term->current_team_id = $teamId;
        $this->Term->resetTermProperty(Term::TYPE_CURRENT);
        $this->Term->resetTermProperty(Term::TYPE_NEXT);
        $this->Term->resetTermProperty(Term::TYPE_PREVIOUS);

        $this->Term->addTermData(Term::TYPE_CURRENT);
        $evaluateTermId = $this->Term->getLastInsertID();
        $term = $this->Term->findById($evaluateTermId);
        //TODO: 現状、グラフの表示がUTCになっており、チームの期間に準拠していないため、UTC時間にする。正しくは、UTC midnight - timeOffset
        //$today = strtotime(date("Y/m/d 23:59:59")) - $term['Term']['timezone'] * HOUR;
        $timezone = $this->Team->getTimezone();
        $today = AppUtil::todayDateYmdLocal($timezone);

        $term['Term']['end_date'] = $today;
        $term['Term']['start_date'] = AppUtil::dateYmd(strtotime("{$today} -{$termDays} days"));
        //TODO: 現状、グラフの表示がUTCになっており、チームの期間に準拠していないため、timezone設定をUTCに変更。
        $term['Term']['timezone'] = 0;
        $this->Term->save($term);
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

        $startDate = $this->Term->getTermData($termType)['start_date'];
        $endDate = $this->Term->getTermData($termType)['end_date'];
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
        $termType = Term::TYPE_CURRENT
    ) {
        /** @var KeyResult $KeyResult */
        $KeyResult = ClassRegistry::init('KeyResult');
        $startDate = $this->Term->getTermData($termType)['start_date'];
        $endDate = $this->Term->getTermData($termType)['end_date'];

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

    function createTeam($data = [])
    {
        $default = [
            'start_term_month'             => 4,
            'border_months'                => 6,
            'type'                         => 3,
            'name'                         => 'Test Team.',
            'timezone'                     => 9,
            'service_use_status'           => 1,
            'country'                      => 1,
            'service_use_state_start_date' => '2017-07-20',
            'service_use_state_end_date'   => '2020-07-20',
        ];
        $team = am($default, $data);
        $this->Team->create();
        $this->Team->save($team);
        return $this->Team->getLastInsertID();
    }

    function deleteAllTeam()
    {
        $this->Team->deleteAll(['id > ' => 0]);
    }

    function createActiveUser($teamId)
    {
        $this->Team->TeamMember->User->create();
        $this->Team->TeamMember->User->save(['active_flg' => true, 'status' => TeamMember::USER_STATUS_ACTIVE], false);
        $userId = $this->Team->TeamMember->User->getLastInsertId();
        $this->Team->TeamMember->create();
        $this->Team->TeamMember->save(['user_id' => $userId, 'team_id' => $teamId, 'active_flg' => true,
                                       'status' => TeamMember::USER_STATUS_ACTIVE], false);
        return $userId;
    }

    function createTopicAndMessages($teamid, $userId, $subUserId, $latestMessageDatetime)
    {
        // save topic
        $this->Topic->create();
        $this->Topic->save([
            'team_id'                 => $teamid,
            'creator_user_id'         => $userId,
            'title'                   => 'Sample title',
            'latest_message_id'       => 1,
            'latest_message_datetime' => $latestMessageDatetime
        ], false);
        $topicId = $this->Topic->getLastInsertId();

        // save topic members
        $this->Topic->TopicMember->create();
        $this->Topic->TopicMember->save([
            'team_id'  => $teamid,
            'user_id'  => $userId,
            'topic_id' => $topicId,
        ], false);
        $this->Topic->TopicMember->create();
        $this->Topic->TopicMember->save([
            'team_id'  => $teamid,
            'user_id'  => $subUserId,
            'topic_id' => $topicId,
        ], false);

        // save messages
        $this->Message->create();
        $this->Message->save([
            'id'             => 1,
            'team_id'        => $teamid,
            'sender_user_id' => $userId,
            'topic_id'       => $topicId,
            'body'           => 'message 1',
            'created'        => $latestMessageDatetime - 1
        ], false);
        $this->Message->create();
        $this->Message->save([
            'id'             => 2,
            'team_id'        => $teamid,
            'sender_user_id' => $subUserId,
            'topic_id'       => $topicId,
            'body'           => 'message 2(latest)',
            'created'        => $latestMessageDatetime
        ], false);

        return $topicId;
    }

    function saveTopic(array $memberUserIds): int
    {
        App::uses('Topic', 'Model');
        /** @var Topic $Topic */
        $Topic = ClassRegistry::init('Topic');

        $Topic->create();
        $Topic->save([
            'team_id'         => 1,
            'creator_user_id' => 1
        ]);
        $topicId = $Topic->getLastInsertID();
        $Topic->TopicMember->create();
        $topicMemberData = [];
        foreach ($memberUserIds as $uid) {
            $topicMemberData[] = [
                'team_id'  => 1,
                'topic_id' => $topicId,
                'user_id'  => $uid
            ];
        }
        $Topic->TopicMember->saveAll($topicMemberData);
        return $topicId;
    }

    function saveTerm(int $teamId, string $startDate, int $range, bool $withNext = true)
    {
        App::uses('Term', 'Model');
        /** @var Topic $Topic */
        $Term = ClassRegistry::init('Term');

        // 精神衛生のために書く
        if ($range < 1) {
            $range = 1;
        } elseif ($range > 12) {
            $range = 12;
        }

        $currentTerm = [
            'team_id'         => $teamId,
            'start_date'      => $startDate,
            'end_date'        => date('Y-m-d', strtotime("{$startDate} + {$range}month yesterday")),
            'evaluate_status' => 0
        ];
        $this->Term->create();
        $this->Term->save($currentTerm);

        if ($withNext) {
            $nextStartDate = date('Y-m-d', strtotime("{$startDate} + {$range}month"));
            $nextTerm = [
                'team_id'         => $teamId,
                'start_date'      => $nextStartDate,
                'end_date'        => date('Y-m-d', strtotime("{$nextStartDate} + {$range}month yesterday")),
                'evaluate_status' => 0
            ];
            $this->Term->create();
            $this->Term->save($nextTerm);
        }

        $this->Term->resetAllTermProperty();

        return Hash::get($this->Term->find('first', ['conditions' => $currentTerm]), 'Term');
    }

    function createSimpleKr(array $data = [])
    {
        /** @var KeyResult $KeyResult */
        $KeyResult = ClassRegistry::init('KeyResult');

        $default = [
            "name"        => "KR Name",
            "description" => "KR description"
        ];
        $data = am($default, $data);
        $KeyResult->create();
        $KeyResult->save($data, false);
        return $KeyResult->getLastInsertID();
    }

    function createInvite($data = [])
    {
        $default = [
            'from_user_id'        => 1,
            'to_user_id'          => 2,
            'team_id'             => 1,
            'email'               => 'xxxx@isao.co.jp',
            'message'             => 'Hello',
            'email_verified'      => false,
            'email_token'         => 'testnotokenhananndemoiiyo',
            'email_token_expires' => time() + DAY
        ];
        $invite = am($default, $data);
        $this->Invite->create();
        $this->Invite->save($invite, false);
        return $this->Invite->getLastInsertID();
    }

    function _getEndOfMonthDay(int $timezone = 9)
    {
        return date('t', REQUEST_TIMESTAMP + $timezone * HOUR);
    }

    function _getLocalTimestamp(int $timezone = 9)
    {
        return REQUEST_TIMESTAMP + $timezone * HOUR;
    }

    function createCcPaidTeam(
        array $team = [],
        array $paymentSetting = [],
        array $creditCard = [],
        int $createActiveUserCount = 1
    ) {
        $this->PaymentSetting = $this->PaymentSetting ?? ClassRegistry::init('PaymentSetting');
        $this->CreditCard = $this->CreditCard ?? ClassRegistry::init('CreditCard');
        $this->ChargeHistory = $this->ChargeHistory ?? ClassRegistry::init('ChargeHistory');

        $saveTeam = array_merge(
            $team,
            [
                'service_use_status' => Team::SERVICE_USE_STATUS_PAID,
            ]
        );
        $teamId = $this->createTeam($saveTeam);

        $savePaymentSetting = array_merge(
            [
                'team_id'          => $teamId,
                'type'             => PaymentSetting::PAYMENT_TYPE_CREDIT_CARD,
                'payment_base_day' => 1
            ],
            $paymentSetting
        );
        $this->PaymentSetting->create();
        $this->PaymentSetting->save($savePaymentSetting, false);
        $paymentSettingId = $this->PaymentSetting->getLastInsertID();
        $saveCreditCard = array_merge(
            [
                'team_id'            => $teamId,
                'payment_setting_id' => $paymentSettingId,
            ],
            $creditCard
        );
        $this->CreditCard->create();
        $this->CreditCard->save($saveCreditCard, false);

        for ($i = 0; $i < $createActiveUserCount; $i++) {
            $this->createActiveUser($teamId);
        }
        return [
            $teamId,
            $paymentSettingId,
        ];
    }

    /**
     * Generate a Token from Stripe API.
     * This method should not be used on production but only for test cases.
     * For production use stripe.js instead.
     *
     * @param string $cardNumber
     * @param string $cardHolder
     * @param int    $expireMonth
     * @param int    $expireYear
     * @param string $cvc
     *
     * @return array
     */
    public function createToken(string $cardNumber): string
    {
        $result = [
            "error" => false,
            "message" => null
        ];

        $request = array(
            "card" => array(
                "number" => $cardNumber,
                "exp_month" => 11,
                "exp_year" => 2026,
                "cvc" => "123",
                "name" => "Goalous Taro"
            )
        );

        // Use public key to create token
        \Stripe\Stripe::setApiKey(STRIPE_PUBLISHABLE_KEY);

        try {
            $response = \Stripe\Token::create($request);
            $token = $response->id;
        }
        catch (Exception $e) {
            $this->log(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            $this->log($e->getTraceAsString());
            return "";
        }

        return $token;
    }

    /**
     * Get a customer for a given credit card.
     * @param string $creditCard
     *
     * @return string
     */
    function createCustomer(string $creditCard): string
    {
        $token = $this->createToken($creditCard);
        $email = "test@goalous.com";

        $res = $this->CreditCardService->registerCustomer($token, $email, "Goalous TEST");
        $this->assertNotNull($res, "Something very wrong happened");
        $this->assertArrayHasKey("customer_id", $res);
        $this->assertArrayHasKey("card", $res);

        return $res["customer_id"];
    }

    /**
     * Delete Stripe Customer
     * 
     * @param $customerId
     */
    function deleteCustomer($customerId)
    {
        $res = $this->CreditCardService->deleteCustomer($customerId);

        $this->assertNotNull($res);
        $this->assertArrayHasKey("error", $res);
        $this->assertArrayHasKey("deleted", $res);
        $this->assertFalse($res["error"]);
        $this->assertTrue($res["deleted"]);
    }

}
