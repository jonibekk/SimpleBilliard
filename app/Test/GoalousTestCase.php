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
App::import('Service', 'PaymentService');
App::uses('BaseRedisClient', 'Lib/Cache/Redis');
App::uses('AppUtil', 'Util');
App::uses('PaymentUtil', 'Util');
App::uses('Experiment', 'Model');

use Goalous\Enum as Enum;

/**
 * CakeTestCase class
 *
 * @package       Cake.TestSuite
 * @property Term                          $Term
 * @property GoalMember                    $GoalMember
 * @property Team                          $Team
 * @property GoalService                   $GoalService
 * @property GlRedis                       $GlRedis
 * @property CreditCardService             $CreditCardService
 * @property PaymentSetting                $PaymentSetting
 * @property CreditCard                    $CreditCard
 * @property ChargeHistory                 $ChargeHistory
 * @property Invoice                       $Invoice
 * @property InvoiceHistory                $InvoiceHistory
 * @property InvoiceHistoriesChargeHistory $InvoiceHistoriesChargeHistory
 * @property PaymentService                $PaymentService
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
    const CARD_ = "5555555555554444";

    const ERR_CODE_CARD_DECLINED = 'card_declined';
    const ERR_CODE_CARD_INCORRECT_CVC = "incorrect_cvc";
    const ERR_CODE_CARD_EXPIRED = 'expired_card';
    const ERR_CODE_CARD_PROCESSING_ERROR = 'processing_error';

    private $testCustomersList = array();

    /** @var string Goalous current date time (Y-m-d H:i:s) */
    public $currentDateTime = null;

    /**
     * 6 kb image file
     *
     * @var string
     */
    protected $testEncodedFileData = "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";

    protected $testFileName = "200px-Noto_Emoji_KitKat_263a.svg";


    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        ini_set('memory_limit', '2024M');
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
        BaseRedisClient::setRedisConnection('redis_test');
        $this->CreditCardService = ClassRegistry::init('CreditCardService');

        $this->currentDateTime = GoalousDateTime::now()->format('Y-m-d H:i:s');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        $this->_clearCache();
        $this->_deleteAllTestCustomers();
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

    /**
     * Create a specified number of users or target team
     *
     * @param $teamId
     * @param $count
     */
    function createActiveUsers(int $teamId, int $count)
    {
        for($n = 0; $n < $count; $n++) {
            $this->createActiveUser($teamId);
        }
    }

    /**
     * Create a single active user for the target team
     *
     * @param $teamId
     *
     * @return mixed
     */
    function createActiveUser(int $teamId)
    {
        $this->Team->TeamMember->User->create();
        $this->Team->TeamMember->User->save(['active_flg' => true, 'status' => TeamMember::USER_STATUS_ACTIVE], false);
        $userId = $this->Team->TeamMember->User->getLastInsertId();
        $this->createTeamMember($teamId, $userId);
        return $userId;
    }

    function createTeamMember($teamId, $userId, $status = TeamMember::USER_STATUS_ACTIVE)
    {
        $this->Team->TeamMember->create();
        $this->Team->TeamMember->save([
            'team_id' => $teamId,
            'user_id' => $userId,
            'status'  => $status
        ], false);
        return $this->Team->TeamMember->getLastInsertId();;
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
                'service_use_status' => Team::SERVICE_USE_STATUS_PAID
            ]
        );
        $teamId = $this->createTeam($saveTeam);

        $savePaymentSetting = array_merge(
            [
                'team_id'          => $teamId,
                'type'     => Enum\Model\PaymentSetting\Type::CREDIT_CARD,
                'payment_base_day' => 1,
                'currency'         => Enum\Model\PaymentSetting\Currency::JPY,
                'amount_per_user'  => PaymentService::AMOUNT_PER_USER_JPY,
                'company_country'  => 'JP',
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
                'customer_code'      => 'cus_BDjPwryGzOQRBI',
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

    function createInvoicePaidTeam(
        array $team = [],
        array $paymentSetting = [],
        array $invoice = [],
        int $createActiveUserCount = 1
    ) {
        $this->PaymentSetting = $this->PaymentSetting ?? ClassRegistry::init('PaymentSetting');
        $this->Invoice = $this->Invoice ?? ClassRegistry::init('Invoice');
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
                'type'             => Enum\Model\PaymentSetting\Type::INVOICE,
                'payment_base_day' => 1,
                'currency'         => Enum\Model\PaymentSetting\Currency::JPY,
                'amount_per_user'  => 1980,
                'company_country'  => 'JP',
            ],
            $paymentSetting
        );
        $this->PaymentSetting->create();
        $this->PaymentSetting->save($savePaymentSetting, false);
        $paymentSettingId = $this->PaymentSetting->getLastInsertID();
        $saveInvoice = array_merge(
            [
                'team_id'                        => $teamId,
                'payment_setting_id'             => $paymentSettingId,
                'credit_status'                  => Invoice::CREDIT_STATUS_OK,
                'company_name'                   => "株式会社これなんで商会",
                'company_post_code'              => "123-4567",
                'company_region'                 => "東京都",
                'company_city'                   => "台東区",
                'company_street'                 => "浅草橋1-2-3",
                'contact_person_first_name'      => "ゴラ男",
                'contact_person_first_name_kana' => "ごらお",
                'contact_person_last_name'       => "ゴラ橋",
                'contact_person_last_name_kana'  => "ごらはし",
                'contact_person_tel'             => "03-1234-5678",
                'contact_person_email'           => "test@goalous.com",
            ],
            $invoice
        );
        $this->Invoice->create();
        $this->Invoice->save($saveInvoice, false);
        $invoiceId = $this->Invoice->getLastInsertID();

        for ($i = 0; $i < $createActiveUserCount; $i++) {
            $this->createActiveUser($teamId);
        }
        return [
            $teamId,
            $paymentSettingId,
            $invoiceId
        ];
    }

    /**
     * @param       $teamId
     * @param array $invoiceHistory
     * @param array $chargeHistories
     *
     * @return array
     * @throws Exception
     */
    function addInvoiceHistoryAndChargeHistories(int $teamId, array $invoiceHistory = [], array $chargeHistories = []) : array
    {
        $this->addInvoiceHistory($teamId, $invoiceHistory);
        $invoiceHistoryId = $this->InvoiceHistory->getLastInsertID();
        $chargeHistoryIds = [];
        foreach ($chargeHistories as $his) {
            $this->addChargeHistory($teamId, $his);
            $chargeHistoryIds[] = $this->ChargeHistory->getLastInsertID();
        }
        $this->InvoiceHistoriesChargeHistory = $this->InvoiceHistoriesChargeHistory ?? ClassRegistry::init('InvoiceHistoriesChargeHistory');
        foreach ($chargeHistoryIds as $chargeHistoryId) {
            $this->InvoiceHistoriesChargeHistory->create();
            $this->InvoiceHistoriesChargeHistory->save([
                'invoice_history_id' => $invoiceHistoryId,
                'charge_history_id'  => $chargeHistoryId,
            ]);
        }
        return [
            $chargeHistoryIds,
            $invoiceHistoryId,
        ];
    }

    /**
     * @param       $teamId
     * @param array $invoiceHistory
     * @param array $chargeHistory
     *
     * @return array
     * @throws Exception
     */
    function addInvoiceHistoryAndChargeHistory(int $teamId, array $invoiceHistory = [], array $chargeHistory = []) : array
    {

        $this->addInvoiceHistory($teamId, $invoiceHistory);
        $invoiceHistoryId = $this->InvoiceHistory->getLastInsertID();
        $this->addChargeHistory($teamId, $chargeHistory);
        $chargeHistoryId = $this->ChargeHistory->getLastInsertID();
        $this->InvoiceHistoriesChargeHistory = $this->InvoiceHistoriesChargeHistory ?? ClassRegistry::init('InvoiceHistoriesChargeHistory');
        $this->InvoiceHistoriesChargeHistory->create();
        $this->InvoiceHistoriesChargeHistory->save([
            'invoice_history_id' => $invoiceHistoryId,
            'charge_history_id'  => $chargeHistoryId,
        ]);
        return [
            $chargeHistoryId,
            $invoiceHistoryId,
        ];
    }

    function addInvoiceHistory($teamId, $invoiceHistory = [])
    {
        $this->InvoiceHistory = $this->InvoiceHistory ?? ClassRegistry::init('InvoiceHistory');
        $this->InvoiceHistory->clear();
        $saveInvoiceHistory = am(
            [
                'team_id' => $teamId,
            ],
            $invoiceHistory
        );
        return $this->InvoiceHistory->save($saveInvoiceHistory);
    }

    function addChargeHistory($teamId, $chargeHistory = [])
    {
        $this->ChargeHistory = $this->ChargeHistory ?? ClassRegistry::init('ChargeHistory');
        $this->ChargeHistory->clear();
        $saveChargeHistory = am(
            [
                'team_id'     => $teamId,
                'currency'    => PaymentSetting::CURRENCY_TYPE_JPY,
                'result_type' => Enum\Model\ChargeHistory\ResultType::SUCCESS,
            ],
            $chargeHistory
        );
        $this->ChargeHistory->save($saveChargeHistory);
        return $this->ChargeHistory->getLastInsertID();
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
            "error"   => false,
            "message" => null
        ];

        $request = array(
            "card" => array(
                "number"    => $cardNumber,
                "exp_month" => 11,
                "exp_year"  => 2026,
                "cvc"       => "123",
                "name"      => "Goalous Taro"
            )
        );

        // Use public key to create token
        \Stripe\Stripe::setApiKey(STRIPE_PUBLISHABLE_KEY);

        try {
            $response = \Stripe\Token::create($request);
            $token = $response->id;
        } catch (Exception $e) {
            $this->Team->log(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            $this->Team->log($e->getTraceAsString());
            return "";
        }

        return $token;
    }

    /**
     * Get a customer for a given credit card.
     *
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

        // Set a list of customers to delete later
        $this->testCustomersList[$res["customer_id"]] = $res["customer_id"];

        return $res["customer_id"];
    }

    /**
     * Delete all test customers created with createCustomer function.
     */
    private function _deleteAllTestCustomers()
    {
        foreach ($this->testCustomersList as $customerId) {
            $this->CreditCardService->deleteCustomer($customerId);
            unset($this->testCustomersList[$customerId]);
        }
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

    function createCircle(array $data = [])
    {
        /** @var Circle $Circle */
        $Circle = ClassRegistry::init('Circle');

        $default = [
            "name"         => "Circle Name",
            "description"  => "Circle description",
            "public_flg"   => true,
            "team_all_flg" => false
        ];
        $data = am($default, $data);
        $Circle->create();
        $Circle->save($data, false);
        return $Circle->getLastInsertID();
    }

    /**
     * Create campaign allowed team
     *
     * @param int $teamId
     * @param int $campaignType
     * @param int $pricePlanGroupId
     *
     * @return int
     */
    function createCampaignTeam(int $teamId, int $pricePlanGroupId)
    {
        /** @var CampaignTeam $CampaignTeam */
        $CampaignTeam = ClassRegistry::init('CampaignTeam');

        // Create campaign team
        $campaignTeam = [
            'team_id'             => $teamId,
            'price_plan_group_id' => $pricePlanGroupId,
            'start_date'          => $this->currentDateTime,
        ];

        $CampaignTeam->create();
        $CampaignTeam->save($campaignTeam);

        return $CampaignTeam->getLastInsertID();
    }

    /**
     * Create PricePlanPurchaseTeam
     *
     * @param int    $teamId
     * @param string $pricePlanCode
     *
     * @return int
     */
    function createPurchasedTeam(int $teamId, string $pricePlanCode): int
    {
        /** @var PricePlanPurchaseTeam $PricePlanPurchaseTeam */
        $PricePlanPurchaseTeam = ClassRegistry::init('PricePlanPurchaseTeam');

        $PricePlanPurchaseTeam->create();
        $PricePlanPurchaseTeam->save([
            'team_id'           => $teamId,
            'price_plan_code'   => $pricePlanCode,
            'purchase_datetime' => $this->currentDateTime,
        ]);

        return $PricePlanPurchaseTeam->getLastInsertID();
    }


    function createCcCampaignTeam(int $pricePlanGroupId, string $pricePlanCode, $team = [], $paymentSetting = []): array
    {
        $team = array_merge([
            'country' => 'JP'
        ], $team);
        $paymentSetting = array_merge([
            'amount_per_user' => 0,
        ], $paymentSetting);
        list($teamId) = $this->createCcPaidTeam($team, $paymentSetting);
        $campaignTeamId = $this->createCampaignTeam($teamId, $pricePlanGroupId);
        $pricePlanPurchaseId = $this->createPurchasedTeam($teamId, $pricePlanCode);

        return [
            $teamId,
            $campaignTeamId,
            $pricePlanPurchaseId
        ];
    }

    function createInvoiceCampaignTeam(int $pricePlanGroupId, string $pricePlanCode, $team = [], $paymentSetting = []): array
    {
        $team = am([
            'country' => 'JP',
            'timezone' => 9
        ], $team);
        $paymentSetting = am([
            'company_country' => 'JP',
            'currency' => Enum\Model\PaymentSetting\Currency::JPY,
            'amount_per_user' => 0,
        ], $paymentSetting);
        list ($teamId, $paymentSettingId, $invoiceId) = $this->createInvoicePaidTeam($team, $paymentSetting, []);
        $campaignTeamId = $this->createCampaignTeam($teamId, $pricePlanGroupId);
        $pricePlanPurchaseId = $this->createPurchasedTeam($teamId, $pricePlanCode);

        return [
            $teamId,
            $campaignTeamId,
            $pricePlanPurchaseId
        ];
    }

    function createExperiments(array $experiments)
    {
        /** @var Experiment $Experiment */
        $Experiment = ClassRegistry::init('Experiment');
        foreach ($experiments as $experiment) {
            $experiment = $Experiment->create([
                'name'    => $experiment[0],
                'team_id' => $experiment[1],
            ]);
            $Experiment->save($experiment);
        }
    }
}
