<?php
App::uses('AppHelper', 'View/Helper');

/**
 * 時差を考慮したローカライズされた時間を表示する。
 *
 * @author daikihirakata
 * @property SessionHelper $Session
 * @property TimeHelper    $Time
 */
class TimeExHelper extends AppHelper
{

    public $helpers = array(
        'Time',
        'Session',
    );

    public $timeOffset = null;

    public function __construct($View)
    {
        parent::__construct($View);
        if ($this->Session->read('Auth.User.timezone')) {
            $this->timeOffset = $this->Session->read('Auth.User.timezone');
        }
    }

    /**
     * format : Y/m/d
     *
     * @param      $date_str
     * @param null $timezone
     *
     * @return string
     */
    public function date($date_str, $timezone = null)
    {
        if ($timezone === null) {
            $timezone = $this->timeOffset;
        }
        return $this->Time->format('Y/m/d', $date_str, null, $timezone);
    }

    /**
     * format : Y/m/d
     *
     * @param        $str
     * @param null   $timezone
     * @param string $format
     *
     * @return string
     * @internal param $date_str
     */
    public function dateFormat($str, $timezone = null, $format = 'Y-m-d')
    {
        if (empty($str)) {
            return null;
        }

        if ($timezone === null) {
            $timezone = $this->timeOffset;
        }
        return $this->Time->format($format, $str, null, $timezone);
    }

    /**
     * format : n/j
     *
     * @param      $date_str
     * @param null $timezone
     *
     * @return string
     */
    public function dateNoYear($date_str, $timezone = null)
    {
        if ($timezone === null) {
            $timezone = $this->timeOffset;
        }
        return $this->Time->format('n/j', $date_str, null, $timezone);
    }

    /**
     * format : n/j H:i
     *
     * @param      $date_str
     * @param null $timezone
     *
     * @return string
     */
    public function datetimeNoYear($date_str, $timezone = null)
    {
        if ($timezone === null) {
            $timezone = $this->timeOffset;
        }
        return $this->Time->format('n/j H:i', $date_str, null, $timezone);
    }

    /**
     * format : Y/m/d H:i:s
     *
     * @param      $date_str
     * @param null $timezone
     *
     * @return string
     */
    public function fullDatetime($date_str, $timezone = null)
    {
        if ($timezone === null) {
            $timezone = $this->timeOffset;
        }
        return $this->Time->format('Y/m/d H:i:s', $date_str, null, $timezone);
    }

    /**
     * @param $unixtime
     * @param $type
     *
     * @return string
     */
    public function elapsedTime($unixtime, $type = 'normal')
    {
        $elapsed = null;
        // "Just now" | 「たった今」 $date > REQUEST_TIMESTAMP - 60sec
        if ($unixtime > strtotime("-1 minute")) {
            $elapsed = __("Just now");
        } // "1 min" | 「１分前」 $date > REQUEST_TIMESTAMP - 120sec
        elseif ($unixtime > strtotime("-2 minutes")) {
            $elapsed = __("1 min");
        } // "from 2 mins to 59 mins" | 「2分前」〜「59分前」 $date > REQUEST_TIMESTAMP - 1h
        elseif ($unixtime > strtotime("-1 hour")) {
            $elapsed = $this->elapsedMinutes($unixtime);
        } //「1時間前」〜「23時間前」 $date > REQUEST_TIMESTAMP - 1d
        elseif ($unixtime > strtotime("-1 day")) {
            $elapsed = $this->elapsedHours($unixtime);
        } //「７月１３日 [15:10]」$date > REQUEST_TIMESTAMP - 1y
        elseif ($unixtime > strtotime("-1 year")) {
            //「７月１３日」
            if ($type == 'rough') {
                $elapsed = $this->dateLocalFormat($unixtime);
            } //「７月１３日 15:10」
            else {
                $elapsed = $this->datetimeLocalFormat($unixtime);
            }
        } //「2013年７月１３日」 else
        else {
            $elapsed = $this->yearDayLocalFormat($unixtime);
        }
        $full_time = $this->fullTimeLocalFormat($unixtime);
        return "<span title='{$full_time}'>{$elapsed}</span>";
    }

    public function elapsedMinutes($unixtime)
    {
        $minutes = floor((REQUEST_TIMESTAMP - $unixtime) / 60);
        return __("%s mins", $minutes);
    }

    public function elapsedHours($unixtime)
    {
        $hours = floor((REQUEST_TIMESTAMP - $unixtime) / 60 / 60);
        return __("%s hours", $hours);
    }

    public function datetimeLocalFormat($unixtime)
    {
        $local_time = $unixtime + ($this->timeOffset * 60 * 60);

        switch (Configure::read('Config.language')) {
            case "jpn":
                $format = "%b%e日 %H:%M";
                break;
            default:
                $format = "%b %e at %l:%M%P";
        }
        return $this->Time->i18nFormat($local_time, $format);
    }

    public function dateLocalFormat($unixtime)
    {
        $local_time = $unixtime + ($this->timeOffset * 60 * 60);
        switch (Configure::read('Config.language')) {
            case "jpn":
                $format = "%b%e日";
                break;
            default:
                $format = "%b %e";
        }
        return $this->Time->i18nFormat($local_time, $format);
    }

    public function yearDayLocalFormat($unixtime)
    {
        $local_time = $unixtime + ($this->timeOffset * 60 * 60);
        switch (Configure::read('Config.language')) {
            case "jpn":
                $format = "%Y年%b%e日";
                break;
            default:
                $format = "%b %e %Y";
        }
        return $this->Time->i18nFormat($local_time, $format);
    }

    public function fullTimeLocalFormat($unixtime)
    {
        $local_time = $unixtime + ($this->timeOffset * 60 * 60);
        switch (Configure::read('Config.language')) {
            case "jpn":
                $format = "%Y年%b%e日 %H:%M";
                break;
            default:
                $format = "%b %e %Y, at %l:%M%P";
        }
        return $this->Time->i18nFormat($local_time, $format);
    }

    public function getTimezoneText($timezone)
    {
        $sign = "+";
        if ($timezone < 0) {
            $sign = "";
        }
        $text = "(GMT {$sign}{$timezone}h)";
        return $text;
    }

}
