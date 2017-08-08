<?php
App::uses('GoalousTestCase', 'Test');
App::uses('GoalousDateTime', 'DateTime');

/**
 * GoalousDateTimeTest Test Case
 */
class GoalousDateTimeTest extends GoalousTestCase
{
    // timestamp 1500000000 = 2017-07-14T02:40:00+00:00 (2017-07-14 02:40:00 on UTC)
    const TIMESTAMP_15e8 = 1500000000;

    function test_timestampOnTimeZone()
    {
        $this->assertEquals(
            self::TIMESTAMP_15e8,
            (new GoalousDateTime('2017/07/14 02:40:00', 'UTC'))->timestamp
        );
        $this->assertEquals(
            self::TIMESTAMP_15e8,
            (new GoalousDateTime('2017/07/14 02:40:00'))->timestamp // UTC
        );
        $this->assertEquals(
            self::TIMESTAMP_15e8 + (-9 * 3600),
            (new GoalousDateTime('2017/07/14 02:40:00', 'JST'))->timestamp
        );
    }

    function test_setGlobalTimeZoneByHour_TimeZoneTranslation()
    {
        GoalousDateTime::setGlobalTimeZoneByHour(0);
        $this->assertEquals('+00:00', GoalousDateTime::getGlobalTimeZone()->getName());
        GoalousDateTime::setGlobalTimeZoneByHour(1);
        $this->assertEquals('+01:00', GoalousDateTime::getGlobalTimeZone()->getName());
        GoalousDateTime::setGlobalTimeZoneByHour(5.5);
        $this->assertEquals('+05:30', GoalousDateTime::getGlobalTimeZone()->getName());
        GoalousDateTime::setGlobalTimeZoneByHour(5.75);
        $this->assertEquals('+05:45', GoalousDateTime::getGlobalTimeZone()->getName());
        GoalousDateTime::setGlobalTimeZoneByHour(-5.5);
        $this->assertEquals('-05:30', GoalousDateTime::getGlobalTimeZone()->getName());
        GoalousDateTime::setGlobalTimeZoneByHour(-5.75);
        $this->assertEquals('-05:45', GoalousDateTime::getGlobalTimeZone()->getName());
    }

    function test_setGlobalTimeZoneByHour_TimeStampIsEqual()
    {
        $timezoneOffsets = [-24, -12, -1, 0, 1, 12, 24];
        foreach ($timezoneOffsets as $offsets) {
            GoalousDateTime::setGlobalTimeZoneByHour($offsets);

            $dateTimeCreatedFromDateFormat    = new GoalousDateTime('2017-07-14 02:40:00'); // only datetime
            $dateTimeCreatedFromDateFormatUTC = new GoalousDateTime('2017-07-14T02:40:00+00:00'); // datetime with tz
            $dateTimeCreatedFromDateFormat_0100 = new GoalousDateTime('2017-07-14T02:40:00+01:00'); // datetime with +01:00
            $dateTimeCreatedFromTimestamp     = GoalousDateTime::createFromTimestamp(self::TIMESTAMP_15e8);

            // create from date format string
            $this->assertEquals(
                self::TIMESTAMP_15e8,
                $dateTimeCreatedFromDateFormat->timestamp
            );
            // create from date format string UTC
            $this->assertEquals(
                self::TIMESTAMP_15e8,
                $dateTimeCreatedFromDateFormatUTC->timestamp
            );
            // create from date format string +01:00
            $this->assertEquals(
                self::TIMESTAMP_15e8 + (-1 * 60 * 60),
                $dateTimeCreatedFromDateFormat_0100->timestamp
            );
            // create from timestamp
            $this->assertEquals(
                self::TIMESTAMP_15e8,
                $dateTimeCreatedFromTimestamp->timestamp
            );
        }
    }

    function test_setGlobalTimeZoneByHour_DateFormatting()
    {
        GoalousDateTime::setGlobalTimeZoneByHour(-6);
        $datetime = new GoalousDateTime('2017-07-14 02:40:00');
        $this->assertEquals(
            '2017-07-13 20:40:00',
            $datetime->format('Y-m-d H:i:s')
        );
        $this->assertEquals(self::TIMESTAMP_15e8, $datetime->timestamp);// timestamp should be equal

        GoalousDateTime::setGlobalTimeZoneByHour(0);
        $datetime = new GoalousDateTime('2017-07-14 02:40:00');
        $this->assertEquals(
            '2017-07-14 02:40:00',
            $datetime->format('Y-m-d H:i:s')
        );
        $this->assertEquals(self::TIMESTAMP_15e8, $datetime->timestamp);// timestamp should be equal

        GoalousDateTime::setGlobalTimeZoneByHour(9);
        $datetime = new GoalousDateTime('2017-07-14 02:40:00');
        $this->assertEquals(
            '2017-07-14 11:40:00',
            $datetime->format('Y-m-d H:i:s')
        );
        $this->assertEquals(self::TIMESTAMP_15e8, $datetime->timestamp);// timestamp should be equal
    }

    function test_setGlobalTimeZoneByHour_now()
    {
        \Carbon\Carbon::setTestNow('2017-07-14T02:40:00+00:00');
        GoalousDateTime::setGlobalTimeZoneByHour(-6);
        $datetime = GoalousDateTime::now();
        $this->assertEquals(
            '2017-07-13 20:40:00',
            $datetime->format('Y-m-d H:i:s')
        );
        $this->assertEquals(self::TIMESTAMP_15e8, $datetime->timestamp);// timestamp should be equal

        GoalousDateTime::setGlobalTimeZoneByHour(0);
        $datetime = GoalousDateTime::now();
        $this->assertEquals(
            '2017-07-14 02:40:00',
            $datetime->format('Y-m-d H:i:s')
        );
        $this->assertEquals(self::TIMESTAMP_15e8, $datetime->timestamp);// timestamp should be equal

        GoalousDateTime::setGlobalTimeZoneByHour(9);
        $datetime = GoalousDateTime::now();
        $this->assertEquals(
            '2017-07-14 11:40:00',
            $datetime->format('Y-m-d H:i:s')
        );
        $this->assertEquals(self::TIMESTAMP_15e8, $datetime->timestamp);// timestamp should be equal

        // if specify timezone passed, it must be passed timezone (can use default Carbon ...)
        $datetime = GoalousDateTime::now(new DateTimeZone('-0600'));
        $this->assertEquals(
            '2017-07-13 20:40:00',
            $datetime->format('Y-m-d H:i:s')
        );
        $this->assertEquals(self::TIMESTAMP_15e8, $datetime->timestamp);// timestamp should be equal
    }
}
