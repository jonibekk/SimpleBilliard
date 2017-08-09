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
        $this->assertEquals('2017-07-14 11:40:00', $now->setTimeZoneUser()->format('Y-m-d H:i:s'));
        $this->assertEquals('2017-07-13 20:40:00', $now->setTimeZoneTeam()->format('Y-m-d H:i:s'));

        $_15e8 = GoalousDateTime::createFromTimestamp(self::TIMESTAMP_15e8);
        $this->assertEquals('2017-07-14 02:40:00', $_15e8->format('Y-m-d H:i:s'));
        $this->assertEquals('2017-07-14 11:40:00', $_15e8->setTimeZoneUser()->format('Y-m-d H:i:s'));
        $this->assertEquals('2017-07-13 20:40:00', $_15e8->setTimeZoneTeam()->format('Y-m-d H:i:s'));

        $_15e8 = GoalousDateTime::createFromTimestamp(self::TIMESTAMP_15e8, new DateTimeZone('+09:00'));
        $this->assertEquals('2017-07-14 11:40:00', $_15e8->format('Y-m-d H:i:s'));
        $this->assertEquals('2017-07-14 11:40:00', $_15e8->setTimeZoneUser()->format('Y-m-d H:i:s'));
        $this->assertEquals('2017-07-13 20:40:00', $_15e8->setTimeZoneTeam()->format('Y-m-d H:i:s'));

        $_15e8 = new GoalousDateTime('2017-07-14 02:40:00');
        $this->assertEquals('2017-07-14 02:40:00', $_15e8->format('Y-m-d H:i:s'));
        $this->assertEquals('2017-07-14 11:40:00', $_15e8->setTimeZoneUser()->format('Y-m-d H:i:s'));
        $this->assertEquals('2017-07-13 20:40:00', $_15e8->setTimeZoneTeam()->format('Y-m-d H:i:s'));

        $_15e8 = new GoalousDateTime('2017-07-14 11:40:00', new DateTimeZone('+09:00'));
        $this->assertEquals('2017-07-14 11:40:00', $_15e8->format('Y-m-d H:i:s'));
        $this->assertEquals('2017-07-14 11:40:00', $_15e8->setTimeZoneUser()->format('Y-m-d H:i:s'));
        $this->assertEquals('2017-07-13 20:40:00', $_15e8->setTimeZoneTeam()->format('Y-m-d H:i:s'));
    }
}
