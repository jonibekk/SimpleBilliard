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

    function initialize()
    {
        App::uses('ConnectionManager', 'Model');
        $this->Db = ConnectionManager::getDataSource('redis');
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
     * @param string      $key_type One of $KEY_TYPES
     * @param int         $team_id
     * @param null|int    $user_id
     * @param null|string $notify_id
     */
    public function setKeyName($key_type, $team_id, $user_id = null, $notify_id = null)
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
            if ($v) {
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
     * @param int    $date
     *
     * @return bool
     */
    public function setNotifications($type, $team_id, $to_user_ids = [], $my_id, $body, $date)
    {
        $notify_id = $this->generateId();
        $this->setKeyName(self::KEY_TYPE_NOTIFICATION, $team_id, null, $notify_id);
        $data = [
            'id'      => $notify_id,
            'user_id' => $my_id,
            'body'    => $body,
            'type'    => $type,
            'date'    => $date,
        ];
        //save notification
        $this->Db->hMset($this->getKeyName(self::KEY_TYPE_NOTIFICATION), $data);
        $this->Db->expire($this->getKeyName(self::KEY_TYPE_NOTIFICATION),
                          60 * 60 * 24 * self::EXPIRE_DAY_OF_NOTIFICATION);
        //save notification user process
        foreach ($to_user_ids as $uid) {
            //save notification user
            $this->setKeyName(self::KEY_TYPE_NOTIFICATION_USER, $team_id, $uid);
            $this->Db->zAdd($this->getKeyName(self::KEY_TYPE_NOTIFICATION_USER), time(),
                            $this->getKeyName(self::KEY_TYPE_NOTIFICATION));
            //increment
            $this->setKeyName(self::KEY_TYPE_NOTIFICATION_COUNT, $team_id, $uid);
            $this->Db->incr($this->getKeyName(self::KEY_TYPE_NOTIFICATION_COUNT));
        }
        return true;
    }

    /**
     * @param $team_id
     * @param $user_id
     *
     * @return bool|int|string
     */
    function getCountOfNewNotification($team_id, $user_id)
    {
        $this->setKeyName(self::KEY_TYPE_NOTIFICATION_COUNT, $team_id, $user_id);
        $count = $this->Db->get($this->getKeyName(self::KEY_TYPE_NOTIFICATION_COUNT));
        return ($count === false) ? 0 : $count;
    }
}
