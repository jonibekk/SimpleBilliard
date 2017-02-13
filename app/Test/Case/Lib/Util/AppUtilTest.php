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

    function test_timeOffsetFromUtcMidnight()
    {
        $this->assertEquals(0, AppUtil::timeOffsetFromUtcMidnight(strtotime('00:00:00')));
        $this->assertEquals(0, AppUtil::timeOffsetFromUtcMidnight(strtotime('00:29:59')));
        $this->assertEquals(0.5, AppUtil::timeOffsetFromUtcMidnight(strtotime('00:30:00')));
        $this->assertEquals(0.5, AppUtil::timeOffsetFromUtcMidnight(strtotime('00:59:59')));
        $this->assertEquals(1.5, AppUtil::timeOffsetFromUtcMidnight(strtotime('01:30:00')));
        $this->assertEquals(23.5, AppUtil::timeOffsetFromUtcMidnight(strtotime('23:59:59')));
    }

}
