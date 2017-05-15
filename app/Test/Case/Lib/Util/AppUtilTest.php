<?php
App::uses('GoalousTestCase', 'Test');
App::uses('AppUtil', 'Util');

/**
 * AppUtil Test Case
 */
class AppUtilTest extends GoalousTestCase
{
    function test_getEndDateByTimezone()
    {
        $this->markTestIncomplete();
    }

    function test_getTimestampByTimezone()
    {
        $this->markTestIncomplete();
    }

    function test_isVector()
    {
        $this->markTestIncomplete();
    }

    function test_formatBigFloat()
    {
        $this->markTestIncomplete();
    }

    function test_isHash()
    {
        $this->markTestIncomplete();
    }

    function test_diffDays()
    {
        $this->markTestIncomplete();
    }

    function test_dateYmd()
    {
        $this->markTestIncomplete();
    }

    function test_between()
    {
        $this->markTestIncomplete();
    }

    function test_floor()
    {
        $this->markTestIncomplete();
    }

    function test_diffHourFloorByMin()
    {
        $baseTime = strtotime('00:00:00');
        //30min
        $this->assertEquals(0, AppUtil::diffHourFloorByMinute(strtotime('00:00:00'), $baseTime));
        $this->assertEquals(0, AppUtil::diffHourFloorByMinute(strtotime('00:29:59'), $baseTime));
        $this->assertEquals(0.5, AppUtil::diffHourFloorByMinute(strtotime('00:30:00'), $baseTime));
        $this->assertEquals(0.5, AppUtil::diffHourFloorByMinute(strtotime('00:59:59'), $baseTime));
        $this->assertEquals(1.5, AppUtil::diffHourFloorByMinute(strtotime('01:30:00'), $baseTime));
        $this->assertEquals(23.5, AppUtil::diffHourFloorByMinute(strtotime('23:59:59'), $baseTime));
        $this->assertEquals(0.5, AppUtil::diffHourFloorByMinute(strtotime('00:59:00'), strtotime('00:29:00')));
        $this->assertEquals(0.5, AppUtil::diffHourFloorByMinute(strtotime('01:01:00'), strtotime('00:31:00')));
        $this->assertEquals(0, AppUtil::diffHourFloorByMinute(strtotime('01:30:00'), strtotime('01:01:00')));
        //15min
        $this->assertEquals(0, AppUtil::diffHourFloorByMinute(strtotime('00:14:59'), $baseTime, 15));
        $this->assertEquals(0.25, AppUtil::diffHourFloorByMinute(strtotime('00:15:00'), $baseTime, 15));
        $this->assertEquals(1.5, AppUtil::diffHourFloorByMinute(strtotime('01:30:00'), $baseTime, 15));
        $this->assertEquals(23.75, AppUtil::diffHourFloorByMinute(strtotime('23:59:59'), $baseTime, 15));
        $this->assertEquals(0.25, AppUtil::diffHourFloorByMinute(strtotime('00:58:00'), strtotime('00:29:00'), 15));
        $this->assertEquals(0.75, AppUtil::diffHourFloorByMinute(strtotime('01:16:00'), strtotime('00:31:00'), 15));
        $this->assertEquals(0.25, AppUtil::diffHourFloorByMinute(strtotime('01:30:00'), strtotime('01:01:00'), 15));

    }

    public function test_rangeYmI18n()
    {
        // english
        CakeSession::write("Config.language", 'eng');
        $this->assertEquals(
            AppUtil::rangeYmI18n('2017-01', '2017-03'),
            ['2017-01' => 'Jan 2017', '2017-02' => 'Feb 2017', '2017-03' => 'Mar 2017']
        );
        $this->assertEquals(
            AppUtil::rangeYmI18n('2017-11', '2018-01'),
            ['2017-11' => 'Nov 2017', '2017-12' => 'Dec 2017', '2018-01' => 'Jan 2018']
        );
        $this->assertEquals(
            AppUtil::rangeYmI18n('2017-01', '2017-01'),
            ['2017-01' => 'Jan 2017']
        );
        $this->assertEquals(
            AppUtil::rangeYmI18n('2017-01', '2016-12'),
            []
        );

        // TODO: 言語設定をjpnにしてテストする必要があるが、Sessionにデータが入らないためテストが通らない
        //       要調査。
        // CakeSession::write("Config.language", 'jpn');
        // $this->assertEquals(
        //     AppUtil::rangeYmFormatted('2017-01', '2017-03'),
        //     ['2017-01' => '2017年01月', '2017-02' => '2017年02月', '2017-03' => '2017年03月']
        // );
        // $this->assertEquals(
        //     AppUtil::rangeYmFormatted('2017-11', '2018-01'),
        //     ['2017-11' => '2017年11月', '2017-12' => '2017年12月', '2018-01' => '2018年01月']
        // );
        // $this->assertEquals(
        //     AppUtil::rangeYmFormatted('2017-01', '2017-01'),
        //     ['2017-01' => '2017年01月']
        // );
        // $this->assertEquals(
        //     AppUtil::rangeYmFormatted('2017-01', '2016-12'),
        //     []
        // );
        // CakeSession::write("Config.language", 'eng');

        // upper limit
        $this->assertEquals(
            count(AppUtil::rangeYmI18n('2017-01', '2037-02')),
            240
        );
    }

}
