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

    function test_setDefaultTimeZone()
    {
        // User
        GoalousDateTime::setDefaultTimeZoneUserByHour(0);
        $this->assertEquals('+00:00', GoalousDateTime::getDefaultTimeZoneUser()->getName());
        GoalousDateTime::setDefaultTimeZoneUserByHour(9);
        $this->assertEquals('+09:00', GoalousDateTime::getDefaultTimeZoneUser()->getName());
        GoalousDateTime::setDefaultTimeZoneUserByHour(-6);
        $this->assertEquals('-06:00', GoalousDateTime::getDefaultTimeZoneUser()->getName());
        GoalousDateTime::setDefaultTimeZoneUserByHour(5.75);
        $this->assertEquals('+05:45', GoalousDateTime::getDefaultTimeZoneUser()->getName());
        GoalousDateTime::setDefaultTimeZoneUserByHour(-5.75);
        $this->assertEquals('-05:45', GoalousDateTime::getDefaultTimeZoneUser()->getName());

        // Team
        GoalousDateTime::setDefaultTimeZoneTeamByHour(0);
        $this->assertEquals('+00:00', GoalousDateTime::getDefaultTimeZoneTeam()->getName());
        GoalousDateTime::setDefaultTimeZoneTeamByHour(9);
        $this->assertEquals('+09:00', GoalousDateTime::getDefaultTimeZoneTeam()->getName());
        GoalousDateTime::setDefaultTimeZoneTeamByHour(-6);
        $this->assertEquals('-06:00', GoalousDateTime::getDefaultTimeZoneTeam()->getName());
        GoalousDateTime::setDefaultTimeZoneTeamByHour(5.75);
        $this->assertEquals('+05:45', GoalousDateTime::getDefaultTimeZoneTeam()->getName());
        GoalousDateTime::setDefaultTimeZoneTeamByHour(-5.75);
        $this->assertEquals('-05:45', GoalousDateTime::getDefaultTimeZoneTeam()->getName());
    }

    function test_setDateTime()
    {
        GoalousDateTime::setTestNow('2017-07-14T02:40:00+00:00');
        GoalousDateTime::setDefaultTimeZoneUserByHour(9);
        GoalousDateTime::setDefaultTimeZoneTeamByHour(-6);
        $now = GoalousDateTime::now();
        $this->assertEquals('2017-07-14 02:40:00', $now->format('Y-m-d H:i:s'));
        $now->setTimeZoneUser();
        $this->assertEquals('2017-07-14 11:40:00', $now->format('Y-m-d H:i:s'));
        $now->setTimeZoneTeam();
        $this->assertEquals('2017-07-13 20:40:00', $now->format('Y-m-d H:i:s'));

        $_15e8 = GoalousDateTime::createFromTimestamp(self::TIMESTAMP_15e8);
        $this->assertEquals('2017-07-14 02:40:00', $_15e8->format('Y-m-d H:i:s'));
        $_15e8->setTimeZoneUser();
        $this->assertEquals('2017-07-14 11:40:00', $_15e8->format('Y-m-d H:i:s'));
        $_15e8->setTimeZoneTeam();
        $this->assertEquals('2017-07-13 20:40:00', $_15e8->format('Y-m-d H:i:s'));

        $_15e8 = GoalousDateTime::createFromTimestamp(self::TIMESTAMP_15e8, new DateTimeZone('+09:00'));
        $this->assertEquals('2017-07-14 11:40:00', $_15e8->format('Y-m-d H:i:s'));
        $_15e8->setTimeZoneUser();
        $this->assertEquals('2017-07-14 11:40:00', $_15e8->format('Y-m-d H:i:s'));
        $_15e8->setTimeZoneTeam();
        $this->assertEquals('2017-07-13 20:40:00', $_15e8->format('Y-m-d H:i:s'));

        $_15e8 = new GoalousDateTime('2017-07-14 02:40:00');
        $this->assertEquals('2017-07-14 02:40:00', $_15e8->format('Y-m-d H:i:s'));
        $_15e8->setTimeZoneUser();
        $this->assertEquals('2017-07-14 11:40:00', $_15e8->format('Y-m-d H:i:s'));
        $_15e8->setTimeZoneTeam();
        $this->assertEquals('2017-07-13 20:40:00', $_15e8->format('Y-m-d H:i:s'));

        $_15e8 = new GoalousDateTime('2017-07-14 11:40:00', new DateTimeZone('+09:00'));
        $this->assertEquals('2017-07-14 11:40:00', $_15e8->format('Y-m-d H:i:s'));
        $_15e8->setTimeZoneUser();
        $this->assertEquals('2017-07-14 11:40:00', $_15e8->format('Y-m-d H:i:s'));
        $_15e8->setTimeZoneTeam();
        $this->assertEquals('2017-07-13 20:40:00', $_15e8->format('Y-m-d H:i:s'));
    }

    /**
     * this is test for php bug will fix on GoalousDateTime
     * https://bugs.php.net/bug.php?id=74173
     */
    function test_setTimeZone()
    {
        $_15e8 = new GoalousDateTime('2017-07-14 02:40:00');
        $this->assertEquals('2017-07-14 02:40:00', $_15e8->format('Y-m-d H:i:s'));
        $_15e8->setTimeZone(new DateTimeZone('+0900'));
        $this->assertEquals('2017-07-14 11:40:00', $_15e8->format('Y-m-d H:i:s'));
        $_15e8->setTimeZone(new DateTimeZone('-0600'));
        $this->assertEquals('2017-07-13 20:40:00', $_15e8->format('Y-m-d H:i:s'));

        // timestamp must not change on modifying TimeZone in a row
        $_15e8 = new GoalousDateTime('2017-07-14 11:40:00', new DateTimeZone('+0900'));
        $this->assertEquals(self::TIMESTAMP_15e8, $_15e8->timestamp);
        $_15e8->setTimeZone(new DateTimeZone('+1200'));
        $this->assertEquals(self::TIMESTAMP_15e8, $_15e8->getTimestamp());
        $_15e8->setTimeZone(new DateTimeZone('-0800'));
        $this->assertEquals(self::TIMESTAMP_15e8, $_15e8->timestamp);
        $_15e8->setTimeZone(new DateTimeZone('+0100'));
        $this->assertEquals(self::TIMESTAMP_15e8, $_15e8->timestamp);
        $_15e8->setTimeZone(new DateTimeZone('-1500'));
        $this->assertEquals('2017-07-13 11:40:00', $_15e8->format('Y-m-d H:i:s'));
        $this->assertEquals(self::TIMESTAMP_15e8, $_15e8->timestamp);
        $_15e8->setTimeZone(new DateTimeZone('+0900'));
        // must be same as first (= +09:00)
        $this->assertEquals('2017-07-14 11:40:00', $_15e8->format('Y-m-d H:i:s'));
        $this->assertEquals(self::TIMESTAMP_15e8, $_15e8->getTimestamp());
    }

    public function provideDateChangeByTimeZoneSetting()
    {
        yield ['2017-12-31 20:00:00',      '+0000', '2018-01-01 05:00:00', '+0900'];
        yield ['2017-02-28 20:00:00',      '+0000', '2017-03-01 05:00:00', '+0900'];
        yield ['2018-01-01 05:00:00',      '+0900', '2017-12-31 17:00:00', '-0300'];
        yield ['2017-03-01 05:00:00',      '+0300', '2017-02-28 20:00:00', '-0600'];
        yield ['2017-03-01 05:00:00+0300', '+0300', '2017-02-28 20:00:00', '-0600'];
        yield ['2017-03-01 05:00:00-0600', '+0300', '2017-03-01 05:00:00', '-0600'];
    }

    /**
     * @dataProvider provideDateChangeByTimeZoneSetting
     */
    function test_dateAddByTimeZone($srcDateTime, $srcTimeZone, $destDateTime, $destTimeZone)
    {
        $day = new GoalousDateTime($srcDateTime, new DateTimeZone($srcTimeZone));
        $day->setTimeZone(new DateTimeZone($destTimeZone));
        $this->assertEquals($destDateTime, $day->format('Y-m-d H:i:s'));
    }
}
