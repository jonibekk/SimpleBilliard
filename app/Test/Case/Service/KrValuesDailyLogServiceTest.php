<?php
App::uses('GoalousTestCase', 'Test');
App::import('Service', 'KrValuesDailyLogService');

/**
 * KrValuesDailyLogServiceTest Class
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 2016/12/08
 * Time: 17:50
 *
 * @property KrValuesDailyLogService $KrValuesDailyLogService
 */
class KrValuesDailyLogServiceTest extends GoalousTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->setDefaultTeamIdAndUid();
        $this->KrValuesDailyLogService = ClassRegistry::init('KrValuesDailyLogService');
        $this->Goal = ClassRegistry::init('Goal');
    }

    function test_deleteCache()
    {
        $uid = 1;
        $this->GlRedis->my_uid = $uid;
        $this->GlRedis->current_team_id = 1;
        $this->Goal->my_uid = $uid;
        $this->Goal->current_team_id = 1;

        $goalId = 1;
        $date = date('Y-m-d');
        // あくまで削除できているかのテストなのでキャッシュに入れるデータは適当で良い
        $data = ["test"];
        // ユーザー別進捗日次ログキャッシュ保存
        $this->KrValuesDailyLogService->writeKrValueDailyLogToCache($uid, $date, $data);
        // ゴール別進捗日次ログキャッシュ保存
        $this->KrValuesDailyLogService->writeGoalKrValueDailyLogToCache($goalId, $date, $data);

        // ユーザー別進捗日次ログキャッシュが有効であること
        $ret = $this->KrValuesDailyLogService->getKrValueDailyLogFromCache($uid, $date);
        $this->assertEqual($ret, $data);

        // ゴール別進捗日次ログキャッシュが有効であること
        $ret = $this->KrValuesDailyLogService->getGoalKrValueDailyLogFromCache($goalId, $date);
        $this->assertEqual($ret, $data);

        /* キャッシュ削除 */
        $this->KrValuesDailyLogService->deleteCache();

        $ret= $this->KrValuesDailyLogService->getKrValueDailyLogFromCache($uid, $date);
        $this->assertEmpty($ret);
        $ret = $this->KrValuesDailyLogService->getGoalKrValueDailyLogFromCache($goalId, $date);
        $this->assertEmpty($ret);
    }

    function test_getGoalKrValueDailyLogFromCache()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function test_getKrValueDailyLogFromCache()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function test_saveAsBulk()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function test_writeGoalKrValueDailyLogToCache()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function test_writeKrValueDailyLogToCache()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }
}
