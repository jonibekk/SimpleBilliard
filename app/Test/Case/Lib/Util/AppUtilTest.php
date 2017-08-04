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

    public function test_getEndDate()
    {
        $res = AppUtil::getEndDate('2016-04-01', 6);
        $this->assertEquals($res, '2016-09-30');

        $res = AppUtil::getEndDate('2016-04-01', 1);
        $this->assertEquals($res, '2016-04-30');

        $res = AppUtil::getEndDate('2016-04-01', 12);
        $this->assertEquals($res, '2017-03-31');
    }

    public function test_convStrToArr()
    {
        /* Empty */
        $emailsStr = "";
        $res = AppUtil::convStrToArr($emailsStr);
        $this->assertEquals($res, []);

        /* Not Empty */
        $emailsStr = "a";
        $res = AppUtil::convStrToArr($emailsStr);
        $this->assertEquals($res, ['a']);

        /* Only line feed */
        $emailsStr = "\r";
        $res = AppUtil::convStrToArr($emailsStr);
        $this->assertEquals($res, []);

        $emailsStr = "\n";
        $res = AppUtil::convStrToArr($emailsStr);
        $this->assertEquals($res, []);

        $emailsStr = "\r\n";
        $res = AppUtil::convStrToArr($emailsStr);
        $this->assertEquals($res, []);

        /* Multi lines */
        $emailsStr = "a\r\nb";
        $res = AppUtil::convStrToArr($emailsStr);
        $this->assertEquals($res, ['a', 'b']);

        $emailsStr = "\r\na\r\nb\nc\rd\r";
        $res = AppUtil::convStrToArr($emailsStr);
        $this->assertEquals($res, ['a', 'b', 'c', 'd']);

        /* Trim */
        $emailsStr = "a ";
        $res = AppUtil::convStrToArr($emailsStr);
        $this->assertEquals($res, ['a']);

        $emailsStr = " a";
        $res = AppUtil::convStrToArr($emailsStr);
        $this->assertEquals($res, ['a']);

        $emailsStr = "   　a 　";
        $res = AppUtil::convStrToArr($emailsStr);
        $this->assertEquals($res, ['a']);

        $emailsStr = "  a \n bbbbbb \n ccc cc \n ddd　d";
        $res = AppUtil::convStrToArr($emailsStr);
        $this->assertEquals($res, ['a', 'bbbbbb', 'ccc cc', 'ddd d']);

        /* Ignore empty lines */
        $emailsStr = "\n\n\na\r\n\r\nb\n ";
        $res = AppUtil::convStrToArr($emailsStr, true);
        $this->assertEquals($res, [0 => 'a', 2 => 'b']);
    }

    public function test_moveMonthYm()
    {
        /* Move +1 month  */
        list($y, $m) = AppUtil::moveMonthYm(2016, 1);
        $this->assertEquals($y, 2016);
        $this->assertEquals($m, 2);

        list($y, $m) = AppUtil::moveMonthYm(2016, 11);
        $this->assertEquals($y, 2016);
        $this->assertEquals($m, 12);

        list($y, $m) = AppUtil::moveMonthYm(2016, 12);
        $this->assertEquals($y, 2017);
        $this->assertEquals($m, 1);

        /* Move >12 month  */
        list($y, $m) = AppUtil::moveMonthYm(2016, 1, 12);
        $this->assertEquals($y, 2017);
        $this->assertEquals($m, 1);

        list($y, $m) = AppUtil::moveMonthYm(2016, 12, 12);
        $this->assertEquals($y, 2017);
        $this->assertEquals($m, 12);

        list($y, $m) = AppUtil::moveMonthYm(2016, 12, 13);
        $this->assertEquals($y, 2018);
        $this->assertEquals($m, 1);

        /* Move -1 month  */
        list($y, $m) = AppUtil::moveMonthYm(2016, 2, -1);
        $this->assertEquals($y, 2016);
        $this->assertEquals($m, 1);

        list($y, $m) = AppUtil::moveMonthYm(2016, 1, -1);
        $this->assertEquals($y, 2015);
        $this->assertEquals($m, 12);

        list($y, $m) = AppUtil::moveMonthYm(2016, 12, -1);
        $this->assertEquals($y, 2016);
        $this->assertEquals($m, 11);

        /* Move <-12 month  */
        list($y, $m) = AppUtil::moveMonthYm(2016, 1, -12);
        $this->assertEquals($y, 2015);
        $this->assertEquals($m, 1);

        list($y, $m) = AppUtil::moveMonthYm(2016, 12, -12);
        $this->assertEquals($y, 2015);
        $this->assertEquals($m, 12);

        list($y, $m) = AppUtil::moveMonthYm(2016, 1, -13);
        $this->assertEquals($y, 2014);
        $this->assertEquals($m, 12);
    }
}
