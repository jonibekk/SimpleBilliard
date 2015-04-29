<?

/**
 * Class RedisComponent
 *
 * @property Redis $Db
 */
class RedisComponent extends Object
{
    public $name = "Redis";
    public $Db;

    /**
     * @var AppController
     */
    var $Controller;

    const KEY_TYPE_NOTIFICATION_USER = 'notification_user_key';
    const KEY_TYPE_NOTIFICATION = 'notification_key';
    const KEY_TYPE_NOTIFICATION_COUNT = 'new_notification_count_key';

    static public $KEY_TYPES = [
        self::KEY_TYPE_NOTIFICATION_USER,
        self::KEY_TYPE_NOTIFICATION,
        self::KEY_TYPE_NOTIFICATION_COUNT,
    ];

    /**
     * Expire day of notification
     */
    const EXPIRE_DAY_OF_NOTIFICATION = 7;
    /**
     * Key Name: team:[team_id]:user:[user_id]:notifications:unread:[0 or 1]:
     *
     * @var array
     */
    private /** @noinspection PhpUnusedPrivateFieldInspection */
        $notification_user_key = [
        'team'          => null,
        'user'          => null,
        'notifications' => null,
        'unread'        => 1,
    ];

    /**
     * Key Name: team:[team_id]:notification:[notify_id]:
     *
     * @var array
     */
    private /** @noinspection PhpUnusedPrivateFieldInspection */
        $notification_key = [
        'team'         => null,
        'notification' => null,
    ];

    /**
     * Key Name: team:[team_id]:user:[user_id]:new_notification_count:
     *
     * @var array
     */
    private /** @noinspection PhpUnusedPrivateFieldInspection */
        $new_notification_count_key = [
        'team'                   => null,
        'user'                   => null,
        'new_notification_count' => null,
    ];

    function initialize(Controller $controller)
    {
        App::uses('ConnectionManager', 'Model');
        $this->Db = ConnectionManager::getDataSource('redis');
        $this->Controller = $controller;
    }

    function startup()
    {
    }

    function beforeRender()
    {
    }

    function shutdown()
    {
    }

    function beforeRedirect()
    {
    }

    /**
     * @param string        $key_type One of $KEY_TYPES
     * @param int           $team_id
     * @param null|int      $user_id
     * @param null|string   $notify_id
     * @param bool|int|null $unread
     */
    public function setKeyName($key_type, $team_id, $user_id = null, $notify_id = null, $unread = null)
    {
        if (!in_array($key_type, self::$KEY_TYPES)) {
            throw new RuntimeException('this is unavailable type!');
        }
        //reset key name
        foreach ($this->{$key_type} as $k => $v) {
            $this->{$key_type}[$k] = null;
        }

        $this->{$key_type}['team'] = $team_id;
        if ($user_id && array_key_exists('user', $this->{$key_type})) {
            $this->{$key_type}['user'] = $user_id;
        }
        if ($notify_id && array_key_exists('notification', $this->{$key_type})) {
            $this->{$key_type}['notification'] = $notify_id;
        }
        if ($unread !== null && array_key_exists('unread', $this->{$key_type})) {
            $this->{$key_type}['unread'] = $unread;
        }
        return;
    }

    /**
     * @param string $key_type One of $KEY_TYPES
     *
     * @return string
     */
    public function getKeyName($key_type)
    {
        if (!in_array($key_type, self::$KEY_TYPES)) {
            throw new RuntimeException('this is unavailable type!');
        }
        $key_name = "";
        foreach ($this->{$key_type} as $k => $v) {
            $key_name .= $k . ":";
            if ($v !== null) {
                $key_name .= $v . ":";
            }
        }
        return $key_name;
    }

    /**
     * generate uuid
     *
     * @return RFC
     */
    private function generateId()
    {
        return String::uuid();
    }

    /**
     * @param string $type
     * @param int    $team_id
     * @param array  $to_user_ids
     * @param int    $my_id
     * @param string $body
     * @param string $url
     * @param int    $date
     *
     * @return bool
     */
    public function setNotifications($type, $team_id, $to_user_ids = [], $my_id, $body, $url, $date)
    {
        $notify_id = $this->generateId();
        $this->setKeyName(self::KEY_TYPE_NOTIFICATION, $team_id, null, $notify_id);
        $data = [
            'id'      => $notify_id,
            'user_id' => $my_id,
            'body'    => $body,
            'url'     => $url,
            'type'    => $type,
            'date'    => $date,
        ];

        /** @noinspection PhpInternalEntityUsedInspection */
        $pipe = $this->Db->multi(Redis::PIPELINE);
        //save notification
        $pipe->hMset($this->getKeyName(self::KEY_TYPE_NOTIFICATION), $data);
        $pipe->expire($this->getKeyName(self::KEY_TYPE_NOTIFICATION),
                      60 * 60 * 24 * self::EXPIRE_DAY_OF_NOTIFICATION);
        //save notification user process
        foreach ($to_user_ids as $uid) {
            //save notification user
            $this->setKeyName(self::KEY_TYPE_NOTIFICATION_USER, $team_id, $uid, null);
            $pipe->zAdd($this->getKeyName(self::KEY_TYPE_NOTIFICATION_USER), microtime(true),
                        $notify_id);
            $this->setKeyName(self::KEY_TYPE_NOTIFICATION_USER, $team_id, $uid, null, 0);
            $pipe->zAdd($this->getKeyName(self::KEY_TYPE_NOTIFICATION_USER), microtime(true),
                        $notify_id);
            //increment
            $this->setKeyName(self::KEY_TYPE_NOTIFICATION_COUNT, $team_id, $uid);
            $pipe->incr($this->getKeyName(self::KEY_TYPE_NOTIFICATION_COUNT));
        }
        $pipe->exec();

        return true;
    }

    /**
     * @param $team_id
     * @param $user_id
     *
     * @return int
     */
    function getCountOfNewNotification($team_id, $user_id)
    {
        $this->setKeyName(self::KEY_TYPE_NOTIFICATION_COUNT, $team_id, $user_id);
        $count = $this->Db->get($this->getKeyName(self::KEY_TYPE_NOTIFICATION_COUNT));
        return ($count === false) ? 0 : (int)$count;
    }

    /**
     * @param  $team_id
     * @param  $user_id
     *
     * @return bool
     */
    function deleteCountOfNewNotification($team_id, $user_id)
    {
        $this->setKeyName(self::KEY_TYPE_NOTIFICATION_COUNT, $team_id, $user_id);
        $res = $this->Db->del($this->getKeyName(self::KEY_TYPE_NOTIFICATION_COUNT));
        return (bool)$res;
    }

    /**
     * @param        $team_id
     * @param        $user_id
     * @param string $notify_id
     * @param int    $unread
     *
     * @return bool
     */
    function changeReadStatusOfNotification($team_id, $user_id, $notify_id, $unread = 0)
    {
        $this->setKeyName(self::KEY_TYPE_NOTIFICATION, $team_id, null, $notify_id);
        $notify_date = $this->Db->hGet($this->getKeyName(self::KEY_TYPE_NOTIFICATION), 'date');
        if ($notify_date === false) {
            return false;
        }

        /** @noinspection PhpInternalEntityUsedInspection */
        $pipe = $this->Db->multi(Redis::PIPELINE);

        //delete set
        $this->setKeyName(self::KEY_TYPE_NOTIFICATION_USER, $team_id, $user_id);
        $deleted_count = $pipe->zDelete($this->getKeyName(self::KEY_TYPE_NOTIFICATION_USER), $notify_id);
        if ($deleted_count === 0) {
            return false;
        }
        //add set for unread status
        $this->setKeyName(self::KEY_TYPE_NOTIFICATION_USER, $team_id, $user_id, null, $unread);
        $pipe->zAdd($this->getKeyName(self::KEY_TYPE_NOTIFICATION_USER), $notify_date,
                    $notify_id);
        //add set for all
        $this->setKeyName(self::KEY_TYPE_NOTIFICATION_USER, $team_id, $user_id);
        $pipe->zAdd($this->getKeyName(self::KEY_TYPE_NOTIFICATION_USER), $notify_date,
                    $notify_id);

        $pipe->exec();

        return true;
    }

    /**
     * @param int  $team_id
     * @param int  $user_id
     * @param null $limit
     * @param null $from_date
     *
     * @return array|null
     */
    function getNotification($team_id, $user_id, $limit = null, $from_date = null)
    {
        $this->setKeyName(self::KEY_TYPE_NOTIFICATION_USER, $team_id, $user_id);
        //delete from notification user
        $this->Db->zRemRangeByScore($this->getKeyName(self::KEY_TYPE_NOTIFICATION_USER), 0,
                                    time() - (60 * 60 * 24 * self::EXPIRE_DAY_OF_NOTIFICATION));

        if ($limit === null) {
            $limit = -1;
        }
        if ($from_date === null) {
            if ($limit !== -1) {
                $limit--;
            }
            $notify_list = $this->Db->zRevRange($this->getKeyName(self::KEY_TYPE_NOTIFICATION_USER), 0, $limit);
        }
        else {
            $notify_list = $this->Db->zRevRangeByScore($this->getKeyName(self::KEY_TYPE_NOTIFICATION_USER),
                                                       $from_date, -1,
                                                       ['limit' => [1, $limit]]);
        }
        if (empty($notify_list)) {
            return null;
        }
        /** @noinspection PhpInternalEntityUsedInspection */
        $pipe = $this->Db->multi(Redis::PIPELINE);
        foreach ($notify_list as $notify_id) {
            $this->setKeyName(self::KEY_TYPE_NOTIFICATION, $team_id, $user_id, $notify_id);
            $pipe->hGetAll($this->getKeyName(self::KEY_TYPE_NOTIFICATION));
        }
        $pipe_res = $pipe->exec();
        return $pipe_res;
    }
}
