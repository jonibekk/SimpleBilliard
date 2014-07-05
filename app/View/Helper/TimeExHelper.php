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

}
