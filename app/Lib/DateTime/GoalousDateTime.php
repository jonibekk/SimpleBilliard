<?php

class GoalousDateTime extends \Carbon\Carbon
{
    /**
     * goalous app default time zone of user
     * @var DateTimeZone
     */
    private static $defaultDateTimeZoneUser = null;

    /**
     * goalous app default time zone of team
     * @var DateTimeZone
     */
    private static $defaultDateTimeZoneTeam = null;

    /**
     * set user timezone to current instance
     * @return self
     */
    function setTimeZoneUser(): self
    {
        if (is_null(static::$defaultDateTimeZoneUser)) {
            return $this;
        }
        $this->setTimezone(static::$defaultDateTimeZoneUser);
        $this->timestamp($this->getTimestamp());
        return $this;
    }

    /**
     * set team timezone to current instance
     * @return self
     */
    function setTimeZoneTeam(): self
    {
        if (is_null(static::$defaultDateTimeZoneTeam)) {
            return $this;
        }
        $this->setTimezone(static::$defaultDateTimeZoneTeam);
        $this->timestamp($this->getTimestamp());
        return $this;
    }

    /**
     * change specify hours to DateTimeZone
     * @example self::hourToDateTimeZone(1) return new DateTimeZone("+01:00")
     * @param float $hour
     *
     * @return DateTimeZone
     */
    static function hourToDateTimeZone(float $hour): DateTimeZone
    {
        $offsetSign = (0 <= $hour) ? '+' : '-';
        $hourAbs = abs(intval($hour)); // example: 5.5 to 5, -5.5 to 5
        return new DateTimeZone(
            sprintf(
                '%s%02d:%02d',  // example: 5.5 to '+05:30', -5.5 to '-05:30'
                $offsetSign,
                $hourAbs, // example: 5.5 to 5, -5.5 to 5
                60 * (abs($hour) - $hourAbs) // example: 5.5 to (60 * 0.5), -5.5 to (60 * 0.5)
            )
        );
    }

    /**
     * set user default TimeZone by specified hour
     *
     * @param float $hour
     */
    static function setDefaultTimeZoneUserByHour(float $hour)
    {
        static::$defaultDateTimeZoneUser = static::hourToDateTimeZone($hour);
    }

    /**
     * @return DateTimeZone|null
     */
    static function getDefaultTimeZoneUser()
    {
        return static::$defaultDateTimeZoneUser;
    }

    /**
     * set team default TimeZone by specified hour
     *
     * @param float $hour
     */
    static function setDefaultTimeZoneTeamByHour(float $hour)
    {
        static::$defaultDateTimeZoneTeam = static::hourToDateTimeZone($hour);
    }

    /**
     * @return DateTimeZone|null
     */
    static function getDefaultTimeZoneTeam()
    {
        return static::$defaultDateTimeZoneTeam;
    }
}
