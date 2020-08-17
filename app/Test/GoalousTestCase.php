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
App::import('Service', 'AttachedFileService');
App::import('Service', 'PostFileService');
App::import('Service', 'PostResourceService');
App::uses('CircleMember', 'Model');

use Goalous\Enum as Enum;

use Goalous\Enum\Model\AttachedFile\AttachedModelType as AttachedModelType;
use Mockery as mock;

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
    const CARD_DECLINED         = "4000000000000002";
    const CARD_INCORRECT_CVC    = "4000000000000127";
    const CARD_EXPIRED          = "4000000000000069";
    const CARD_PROCESSING_ERROR = "4000000000000119";
    const CARD_INCORRECT_NUMBER = "4242424242424241";
    const CARD_CHARGE_FAIL      = "4000000000000341";
    // Valid Cards
    const CARD_VISA       = "4012888888881881";
    const CARD_MASTERCARD = "5555555555554444";
    const CARD_           = "5555555555554444";

    const ERR_CODE_CARD_DECLINED         = 'card_declined';
    const ERR_CODE_CARD_INCORRECT_CVC    = "incorrect_cvc";
    const ERR_CODE_CARD_EXPIRED          = 'expired_card';
    const ERR_CODE_CARD_PROCESSING_ERROR = 'processing_error';

    private $testCustomersList = array();

    /** @var string Goalous current date time (Y-m-d H:i:s) */
    public $currentDateTime = null;

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
        $GoalMember->save(
            [
                'goal_id' => $goalId,
                'user_id' => $userId,
                'team_id' => $teamId,
                'type'    => $goalMemberType,
            ]
        );
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
        $termType = Term::TYPE_CURRENT,
        $tkrFlg = false,
        $valueUnit = 0
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
            'value_unit'    => $valueUnit,
            'current_value' => $currentValue,
            'start_date'    => $startDate,
            'end_date'      => $endDate,
            'priority'      => $priority,
            'tkr_flg'       => $tkrFlg,
        ];
        $KeyResult->create();
        $KeyResult->save($kr, false);
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
        for ($n = 0; $n < $count; $n++) {
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

    protected function createTeamMember($teamId, $userId, $status = TeamMember::USER_STATUS_ACTIVE)
    {
        $this->Team->TeamMember->create();
        $this->Team->TeamMember->save(
            [
                'team_id' => $teamId,
                'user_id' => $userId,
                'status'  => $status
            ],
            false
        );
        return $this->Team->TeamMember->getLastInsertId();;
    }

    function createTopicAndMessages($teamid, $userId, $subUserId, $latestMessageDatetime)
    {
        // save topic
        $this->Topic->create();
        $this->Topic->save(
            [
                'team_id'                 => $teamid,
                'creator_user_id'         => $userId,
                'title'                   => 'Sample title',
                'latest_message_id'       => 1,
                'latest_message_datetime' => $latestMessageDatetime
            ],
            false
        );
        $topicId = $this->Topic->getLastInsertId();

        // save topic members
        $this->Topic->TopicMember->create();
        $this->Topic->TopicMember->save(
            [
                'team_id'  => $teamid,
                'user_id'  => $userId,
                'topic_id' => $topicId,
            ],
            false
        );
        $this->Topic->TopicMember->create();
        $this->Topic->TopicMember->save(
            [
                'team_id'  => $teamid,
                'user_id'  => $subUserId,
                'topic_id' => $topicId,
            ],
            false
        );

        // save messages
        $this->Message->create();
        $this->Message->save(
            [
                'id'             => 1,
                'team_id'        => $teamid,
                'sender_user_id' => $userId,
                'topic_id'       => $topicId,
                'body'           => 'message 1',
                'created'        => $latestMessageDatetime - 1
            ],
            false
        );
        $this->Message->create();
        $this->Message->save(
            [
                'id'             => 2,
                'team_id'        => $teamid,
                'sender_user_id' => $subUserId,
                'topic_id'       => $topicId,
                'body'           => 'message 2(latest)',
                'created'        => $latestMessageDatetime
            ],
            false
        );

        return $topicId;
    }

    function saveTopic(array $memberUserIds): int
    {
        App::uses('Topic', 'Model');
        /** @var Topic $Topic */
        $Topic = ClassRegistry::init('Topic');

        $Topic->create();
        $Topic->save(
            [
                'team_id'         => 1,
                'creator_user_id' => 1
            ]
        );
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
        int $createActiveUserCount = 1,
        bool $skipPayment = false
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
                'type'             => Enum\Model\PaymentSetting\Type::CREDIT_CARD,
                'payment_base_day' => 15,
                'payment_skip_flg' => ($skipPayment) ? 1 : 0,
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
        int $createActiveUserCount = 1,
        bool $skipPayment = false
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
                'payment_base_day' => 15,
                'payment_skip_flg' => ($skipPayment) ? 1 : 0,
                'currency'         => Enum\Model\PaymentSetting\Currency::JPY,
                'amount_per_user'  => PaymentService::AMOUNT_PER_USER_JPY,
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
        $savedInvoice = $this->Invoice->save($saveInvoice, false);
        $invoiceId = $savedInvoice['Invoice']['id'];

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
    function addInvoiceHistoryAndChargeHistories(
        int $teamId,
        array $invoiceHistory = [],
        array $chargeHistories = []
    ): array {
        $this->addInvoiceHistory($teamId, $invoiceHistory);
        $invoiceHistoryId = $this->InvoiceHistory->getLastInsertID();
        $chargeHistoryIds = [];
        foreach ($chargeHistories as $his) {
            $this->addChargeHistory($teamId, $his);
            $chargeHistoryIds[] = $this->ChargeHistory->getLastInsertID();
        }
        $this->InvoiceHistoriesChargeHistory = $this->InvoiceHistoriesChargeHistory ?? ClassRegistry::init(
                'InvoiceHistoriesChargeHistory'
            );
        foreach ($chargeHistoryIds as $chargeHistoryId) {
            $this->InvoiceHistoriesChargeHistory->create();
            $this->InvoiceHistoriesChargeHistory->save(
                [
                    'invoice_history_id' => $invoiceHistoryId,
                    'charge_history_id'  => $chargeHistoryId,
                ]
            );
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
    function addInvoiceHistoryAndChargeHistory(
        int $teamId,
        array $invoiceHistory = [],
        array $chargeHistory = []
    ): array {
        $this->addInvoiceHistory($teamId, $invoiceHistory);
        $invoiceHistoryId = $this->InvoiceHistory->getLastInsertID();
        $this->addChargeHistory($teamId, $chargeHistory);
        $chargeHistoryId = $this->ChargeHistory->getLastInsertID();
        $this->InvoiceHistoriesChargeHistory = $this->InvoiceHistoriesChargeHistory ?? ClassRegistry::init(
                'InvoiceHistoriesChargeHistory'
            );
        $this->InvoiceHistoriesChargeHistory->create();
        $this->InvoiceHistoriesChargeHistory->save(
            [
                'invoice_history_id' => $invoiceHistoryId,
                'charge_history_id'  => $chargeHistoryId,
            ]
        );
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
        $PricePlanPurchaseTeam->save(
            [
                'team_id'           => $teamId,
                'price_plan_code'   => $pricePlanCode,
                'purchase_datetime' => $this->currentDateTime,
            ]
        );

        return $PricePlanPurchaseTeam->getLastInsertID();
    }

    function createCcCampaignTeam(int $pricePlanGroupId, string $pricePlanCode, $team = [], $paymentSetting = []): array
    {
        $team = array_merge(
            [
                'country' => 'JP'
            ],
            $team
        );
        $paymentSetting = array_merge(
            [
                'amount_per_user' => 0,
            ],
            $paymentSetting
        );
        list($teamId) = $this->createCcPaidTeam($team, $paymentSetting);
        $campaignTeamId = $this->createCampaignTeam($teamId, $pricePlanGroupId);
        $pricePlanPurchaseId = $this->createPurchasedTeam($teamId, $pricePlanCode);

        return [
            $teamId,
            $campaignTeamId,
            $pricePlanPurchaseId
        ];
    }

    function createInvoiceCampaignTeam(
        int $pricePlanGroupId,
        string $pricePlanCode,
        $team = [],
        $paymentSetting = []
    ): array {
        $team = am(
            [
                'country'  => 'JP',
                'timezone' => 9
            ],
            $team
        );
        $paymentSetting = am(
            [
                'company_country' => 'JP',
                'currency'        => Enum\Model\PaymentSetting\Currency::JPY,
                'amount_per_user' => 0,
            ],
            $paymentSetting
        );
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
            $experiment = $Experiment->create(
                [
                    'name'    => $experiment[0],
                    'team_id' => $experiment[1],
                ]
            );
            $Experiment->save($experiment);
        }
    }

    /**
     * Get base64 encoded test file
     *
     * @param string $fileName Filename
     *
     * @return string
     */
    protected function getTestFileData(string $fileName = ""): string
    {
        $path = APP . "Test" . DS . "Images" . DS;

        if (empty($fileName)) {
            $path .= $this->getTestFileName();
        } else {
            $path .= $fileName;
        }

        if (!file_exists($path)) {
            throw new RuntimeException("Missing test file: " . $this->getTestFileName());
        }
        $rawFile = file_get_contents($path);

        return base64_encode($rawFile);
    }

    protected function getTestFileDataBase64WithHeader(): string
    {
        return 'data:image/png;base64,' . $this->getTestFileData();
    }

    /**
     * Get test file name
     *
     * @return string
     */
    protected function getTestFileName(): string
    {
        return "test.png";
    }

    /**
     * Insert translation language option to a team
     *
     * @param int    $teamId
     * @param string $language
     *
     * @throws Exception
     */
    protected function insertTranslationLanguage(int $teamId, string $language)
    {
        /** @var TeamTranslationLanguage $TeamTranslationLanguage */
        $TeamTranslationLanguage = ClassRegistry::init('TeamTranslationLanguage');

        $data = [
            'team_id'  => $teamId,
            'language' => $language
        ];

        $TeamTranslationLanguage->create();
        $TeamTranslationLanguage->save($data, false);
    }

    /**
     * Get long article. 67,187 characters
     *
     * @return string
     */
    protected function getLongArticle(): string
    {
        $path = APP . "Test" . DS . "Files" . DS . 'article.txt';

        $article = file_get_contents($path);

        return $article;
    }

    /**
     * Create a new post with attachment
     *
     * @param int $circleId
     * @param int $userId
     * @param int $teamId
     * @param int $fileCount
     * @param int $videoCount
     *
     * @return array
     *              [post_id, [post_file,...], [video_stream,...]]
     * @throws Exception
     */
    protected final function createNewCirclePost(
        int $circleId,
        int $userId,
        int $teamId,
        int $fileCount = 0,
        int $videoCount = 0
    ): array {
        /** @var TransactionManager $TransactionManager */
        $TransactionManager = ClassRegistry::init('TransactionManager');
        /** @var Post $Post */
        $Post = ClassRegistry::init('Post');
        /** @var PostShareCircle $PostShareCircle */
        $PostShareCircle = ClassRegistry::init('PostShareCircle');
        /** @var CircleMember $CircleMember */
        $CircleMember = ClassRegistry::init('CircleMember');
        /** @var Circle $Circle */
        $Circle = ClassRegistry::init('Circle');
        /** @var AttachedFileService $AttachedFileService */
        $AttachedFileService = ClassRegistry::init('AttachedFileService');
        /** @var PostFileService $PostFileService */
        $PostFileService = ClassRegistry::init('PostFileService');
        /** @var PostResourceService $PostResourceService */
        $PostResourceService = ClassRegistry::init('PostResourceService');
        /** @var Video $Video */
        $Video = ClassRegistry::init('Video');
        /** @var VideoStream $VideoStream */
        $VideoStream = ClassRegistry::init('VideoStream');

        try {
            $TransactionManager->begin();
            $postBody = "New post in circle $circleId by user $userId in team $teamId";

            if ($videoCount > 1) {
                throw new InvalidArgumentException('Too many videos');
            }
            if ($fileCount + $videoCount > 10) {
                throw new InvalidArgumentException("Too many files");
            }

            if ($fileCount > 0) {
                $postBody .= " with $fileCount files";
            }
            if ($videoCount > 0) {
                $postBody .= " with $videoCount videos";
            }

            $Post->create();
            $postData = [
                'body'    => $postBody,
                'user_id' => $userId,
                'team_id' => $teamId,
                'type'    => Post::TYPE_NORMAL
            ];
            $savedPost = $Post->save($postData, false);
            $postId = $savedPost['Post']['id'];
            $postCreated = $savedPost['Post']['created'];
            $updateCondition = [
                'CircleMember.user_id'   => $userId,
                'CircleMember.circle_id' => $circleId
            ];
            $CircleMember->updateAll(['last_posted' => $postCreated], $updateCondition);
            $PostShareCircle->add($postId, [$circleId], $teamId);
            $CircleMember->incrementUnreadCount([$circleId], true, $teamId, $userId);
            $Circle->updateLatestPosted($circleId);

            //Save files

            $addedFiles = [];
            $addedVideos = [];
            $postFileIndex = 0;

            for ($i = 0; $i < $fileCount; $i++) {
                $UploadedFile = new UploadedFile($this->getTestFileDataBase64WithHeader(), 'test.jpg', false);

                /** @var AttachedFileEntity $attachedFile */
                $attachedFile = $AttachedFileService->add(
                    $userId,
                    $teamId,
                    $UploadedFile,
                    AttachedModelType::TYPE_MODEL_POST()
                );

                $postResourceType = $PostResourceService->getPostResourceTypeFromAttachedFileType(
                    $attachedFile['file_type']
                );

                $PostResourceService->addResourcePost(
                    $postId,
                    $postResourceType,
                    $attachedFile['id'],
                    $postFileIndex
                );
                $addedPostFile = $PostFileService->add($postId, $attachedFile['id'], $teamId, $postFileIndex);

                $addedFiles[] = $addedPostFile->toArray();
                $postFileIndex++;
            }

            for ($i = 0; $i < $videoCount; $i++) {
                $newVideo = [
                    'user_id'   => $userId,
                    'team_id'   => $teamId,
                    'file_name' => "video $i"
                ];
                $Video->create();
                $video = $Video->save($newVideo, false);

                $newVideoStream = [
                    'video_id'         => $video['Video']['id'],
                    'output_version'   => 1,
                    'transcode_status' => Enum\Model\Video\VideoTranscodeStatus::TRANSCODE_COMPLETE,
                ];
                $VideoStream->create();
                $videoStream = $VideoStream->save($newVideoStream, false);
                $addedVideos[] = $videoStream['VideoStream'];
                $PostResourceService->addResourcePost(
                    $postId,
                    Enum\Model\Post\PostResourceType::VIDEO_STREAM(),
                    $videoStream['VideoStream']['id'],
                    $postFileIndex
                );
                $postFileIndex++;
            }
            $TransactionManager->commit();
        } catch (Exception $e) {
            $TransactionManager->rollback();
            throw $e;
        }

        return [$postId, $addedFiles, $addedVideos];
    }

    protected function createAttachedFile(
        int $userId,
        int $teamId,
        Enum\Model\AttachedFile\AttachedFileType $type,
        Enum\Model\AttachedFile\AttachedModelType $modelType
    ): AttachedFileEntity {
        /** @var AttachedFile $AttachedFile */
        $AttachedFile = ClassRegistry::init('AttachedFile');

        $fileName = "user_" . $userId . "_team_" . $teamId . ".test";

        $newData = [
            'user_id'               => $userId,
            'team_id'               => $teamId,
            'attached_file_name'    => $fileName,
            'file_type'             => $type->getValue(),
            'file_ext'              => 'test',
            'file_size'             => 123,
            'model_type'            => $modelType->getValue(),
            'display_file_list_flg' => true,
            'removable_flg'         => true,
        ];

        $AttachedFile->create();
        /** @var AttachedFileEntity $newAttachedFile */
        $newAttachedFile = $AttachedFile->useType()->useEntity()->save($newData, false);

        return $newAttachedFile;
    }

    /**
     * Create new comment file
     *
     * @param int $commentId
     * @param int $userId
     * @param int $teamId
     * @param int $count Number of files to create
     *
     * @return CommentFileEntity[]
     */
    protected function createCommentFile(int $commentId, int $userId, int $teamId, int $count = 1): array
    {
        /** @var CommentFile $CommentFile */
        $CommentFile = ClassRegistry::init('CommentFile');

        $result = [];

        for ($indexNum = 0; $indexNum < $count; $indexNum++) {
            $newAttachedFile = $this->createAttachedFile(
                $userId,
                $teamId,
                Enum\Model\AttachedFile\AttachedFileType::TYPE_FILE_DOC(),
                Enum\Model\AttachedFile\AttachedModelType::TYPE_MODEL_COMMENT()
            );

            $newCommentFile = [
                'comment_id'       => $commentId,
                'attached_file_id' => $newAttachedFile['id'],
                'team_id'          => $teamId,
                'index_num'        => $indexNum
            ];

            $CommentFile->create();
            $result[] = $CommentFile->useType()->useEntity()->save($newCommentFile, false);
        }

        return $result;
    }

    protected function createCircleMember(int $circleId, int $teamId, int $userId, array $options = []): array
    {
        $mainData = [
            'circle_id' => $circleId,
            'team_id'   => $teamId,
            'user_id'   => $userId
        ];

        $newData = array_merge($mainData, $options);
        /** @var CircleMember $CircleMember */
        $CircleMember = ClassRegistry::init('CircleMember');

        $CircleMember->create();
        $insertedData = $CircleMember->save($newData, false);

        return $insertedData['CircleMember'];
    }

    protected function createTranslatorClientMock(string $sourceLanguage = null, string $translation = null)
    {
        $translatorClient = mock::mock('GoogleTranslatorClient');

        if (empty($sourceLanguage)) {
            $sourceLanguage = "en";
        }
        if (empty($translation)) {
            $translation = 'Esta es una muestra de traducción.';
        }

        $returnValue = new TranslationResult($sourceLanguage, $translation, '');

        $translatorClient->shouldReceive('translate')
            ->once()
            ->andReturn($returnValue);

        ClassRegistry::addObject(GoogleTranslatorClient::class, $translatorClient);
    }

    protected function updateTeamServiceStatus(int $teamId, int $newStatus)
    {
        /** @var Team $Team */
        $Team = ClassRegistry::init('Team');

        $Team->updateAll(
            [
                'Team.service_use_status' => $newStatus
            ],
            ['Team.id' => $teamId]
        );
    }

    protected function updateTeamMemberStatus(int $teamId, int $userId, int $status)
    {
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init('TeamMember');

        $TeamMember->updateAll(
            [
                'TeamMember.status' => $status
            ],
            ['TeamMember.user_id' => $userId, 'TeamMember.team_id' => $teamId]
        );
    }
}
