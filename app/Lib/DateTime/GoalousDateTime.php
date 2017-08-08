<?php

class GoalousDateTime extends \Carbon\Carbon
{
    /**
     * goalous app global timezone
     * @var DateTimeZone
     */
    private static $globalDateTimeZone = null;

    function __construct($time = null, $tz = null)
    {
        parent::__construct($time, $tz);
        if (is_null($tz) && static::$globalDateTimeZone instanceof DateTimeZone) {
            $this->setTimezone(static::$globalDateTimeZone);
            $this->timestamp($this->getTimestamp());
        }
    }

    /**
     * set GoalousDateTime timezone by specified hour
     *
     * @param float $hour
     */
    static function setGlobalTimeZoneByHour(float $hour)
    {
        $offsetSign = (0 <= $hour) ? '+' : '-';
        $hourAbs = abs(intval($hour)); // transform example: 5.5 to 5, -5.5 to 5
        static::$globalDateTimeZone = new DateTimeZone(
            sprintf(
                '%s%02d:%02d',
                $offsetSign,
                $hourAbs, // transform example: 5.5 to 5, -5.5 to 5
                60 * (abs($hour) - $hourAbs) // transform example: 5.5 to (60 * 0.5), -5.5 to (60 * 0.5)
                )
        );
    }

    /**
     * set GoalousDateTime timezone by specified string
     *
     * @param string $timezone
     */
    static function setGlobalTimeZoneByString(string $timezone)
    {
        static::$globalDateTimeZone = new DateTimeZone($timezone);
    }

    /**
     * @return DateTimeZone|null
     */
    static function getGlobalTimeZone()
    {
        return static::$globalDateTimeZone;
    }
}
