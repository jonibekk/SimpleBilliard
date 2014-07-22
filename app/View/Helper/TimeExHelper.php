<?php

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
     * @param  $date_str
     *
     * @return string
     */
    public function date($date_str)
    {
        return $this->Time->format('Y/m/d', $date_str, null, $this->timeOffset);
    }

    /**
     * format : n/j
     *
     * @param $date_str
     *
     * @return string
     */
    public function dateNoYear($date_str)
    {
        return $this->Time->format('n/j', $date_str, null, $this->timeOffset);
    }

    /**
     * format : n/j H:i
     *
     * @param  $date_str
     *
     * @return string
     */
    public function datetimeNoYear($date_str)
    {
        return $this->Time->format('n/j H:i', $date_str, null, $this->timeOffset);
    }

    /**
     * format : Y/m/d H:i:s
     *
     * @param  $date_str
     *
     * @return string
     */
    public function fullDatetime($date_str)
    {
        return $this->Time->format('Y/m/d H:i:s', $date_str, null, $this->timeOffset);
    }

    /**
     * @param $unixtime
     *
     * @return string
     */
    public function elapsedTime($unixtime)
    {
        $elapsed = null;
        //「たった今」 $date > time() - 60sec
        if ($unixtime > strtotime("-1 minute")) {
            $elapsed = __d('time', "たった今");
        }
        //「１分前」 $date > time() - 120sec
        elseif ($unixtime > strtotime("-2 minutes")) {
            $elapsed = __d('time', "1分前");
        }
        //「2分前」〜「59分前」 $date > time() - 1h
        elseif ($unixtime > strtotime("-1 hour")) {
            $elapsed = $this->elapsedMinutes($unixtime);
        }
        //「1時間前」〜「23時間前」 $date > time() - 1d
        elseif ($unixtime > strtotime("-1 day")) {
            $elapsed = $this->elapsedHours($unixtime);
        }
        //「７月１３日 15:10」$date > time() - 1y
        elseif ($unixtime > strtotime("-1 year")) {
            $elapsed = $this->datetimeLocalFormat($unixtime);
        }
        //「2013年７月１３日」 else
        else {
            $elapsed = $this->yearDayLocalFormat($unixtime);
        }
        $full_time = $this->fullTimeLocalFormat($unixtime);
        return "<span title='{$full_time}'>{$elapsed}</span>";
    }

    public function elapsedMinutes($unixtime)
    {
        $minutes = floor((time() - $unixtime) / 60);
        return __d('time', "%d分前", $minutes);
    }

    public function elapsedHours($unixtime)
    {
        $hours = floor((time() - $unixtime) / 60 / 60);
        return __d('time', "%d時間前", $hours);
    }

    public function datetimeLocalFormat($unixtime)
    {
        $local_time = $unixtime + ($this->timeOffset * 60 * 60);
        $res = __dc("time", '%d月%d日 %d:%d', LC_TIME,
                    date('n', $local_time),
                    date('j', $local_time),
                    date('H', $local_time),
                    date('i', $local_time)
        );
        return $res;
    }

    public function yearDayLocalFormat($unixtime)
    {
        $local_time = $unixtime + ($this->timeOffset * 60 * 60);
        $res = __dc("time", '%d年%d月%d日', LC_TIME,
                    date('Y', $local_time),
                    date('n', $local_time),
                    date('j', $local_time)
        );
        return $res;
    }

    public function fullTimeLocalFormat($unixtime)
    {
        $local_time = $unixtime + ($this->timeOffset * 60 * 60);
        $res = __dc("time", '%d年%d月%d日 %d:%d', LC_TIME,
                    date('Y', $local_time),
                    date('n', $local_time),
                    date('j', $local_time),
                    date('H', $local_time),
                    date('i', $local_time)
        );
        return $res;
    }

}
