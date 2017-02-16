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

}
