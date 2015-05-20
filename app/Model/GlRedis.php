<?php
App::uses('AppModel', 'Model');
App::uses('ConnectionManager', 'Model');

/**
 * GlRedis Model

 */
class GlRedis extends AppModel
{
    public $useTable = false;
    protected $_schema = array(
        'dummy' => array('type' => 'text'),
    );

    /**
     * @var Redis $Db
     */
    private $Db;

    private $config_name = 'redis';

    /**
     * @param bool $id
     * @param null $table
     * @param null $ds
     */
    public function __construct($id = false, $table = null, $ds = null)
    {
        parent::__construct($id, $table, $ds);
        $this->Db = ConnectionManager::getDataSource($this->config_name);
    }

    const KEY_TYPE_NOTIFICATION_USER = 'notification_user_key';
    const KEY_TYPE_NOTIFICATION = 'notification_key';
    const KEY_TYPE_NOTIFICATION_COUNT = 'new_notification_count_key';
    const KEY_TYPE_LOGIN_FAIL = 'login_fail_key';
    const KEY_TYPE_COUNT_BY_USER = 'count_by_user_key';
    const KEY_TYPE_TWO_FA_DEVICE_HASHES = 'two_fa_device_hashes_key';

    const FIELD_COUNT_NEW_NOTIFY = 'new_notify';

    static public $KEY_TYPES = [
        self::KEY_TYPE_NOTIFICATION_USER,
        self::KEY_TYPE_NOTIFICATION,
        self::KEY_TYPE_NOTIFICATION_COUNT,
        self::KEY_TYPE_LOGIN_FAIL,
        self::KEY_TYPE_COUNT_BY_USER,
        self::KEY_TYPE_TWO_FA_DEVICE_HASHES,
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
        $count_by_user_key = [
        'team'  => null,
        'user'  => null,
        'count' => null,
    ];

    /**
     * Key Name: email:[email]:device:[device_hash]:fail_count:
     *
     * @var array
     */
    private /** @noinspection PhpUnusedPrivateFieldInspection */
        $login_fail_key = [
        'email'      => null,
        'device'     => null,
        'fail_count' => null,
    ];

    /**
     * Key Name: team:[team_id]:user:[user_id]:two_fa_device_hashes:
     *
     * @var array
     */
    private /** @noinspection PhpUnusedPrivateFieldInspection */
        $two_fa_device_hashes_key = [
        'team'                 => null,
        'user'                 => null,
        'two_fa_device_hashes' => null,
    ];

    public function changeDbSource($config_name = "redis_test")
    {
        unset($this->Db);
        $this->config_name = $config_name;
        $this->Db = ConnectionManager::getDataSource($config_name);
    }

    /**
     *  Attention! delete all data!
     */
    public function deleteAllData()
    {
        $keys = $this->Db->keys('*');
        $prefix = $this->Db->config['prefix'];
        foreach ($keys as $k) {
            $keys[$k] = str_replace($prefix, "", $k);
        }
        return $this->Db->del($keys);
    }

    /**
     * @param string        $key_type One of $KEY_TYPES
     * @param int           $team_id
     * @param null|int      $user_id
     * @param null|string   $notify_id
     * @param bool|int|null $unread
     * @param null|string   $email
     * @param null|string   $device
     *
     * @return string
     */
    private function getKeyName($key_type, $team_id = null, $user_id = null, $notify_id = null, $unread = null, $email = null, $device = null)
    {
        if (!in_array($key_type, self::$KEY_TYPES)) {
            throw new RuntimeException('this is unavailable type!');
        }

        //reset key name
        foreach ($this->{$key_type} as $k => $v) {
            $this->{$key_type}[$k] = null;
        }
        if ($team_id && array_key_exists('team', $this->{$key_type})) {
            $this->{$key_type}['team'] = $team_id;
        }
        if ($user_id && array_key_exists('user', $this->{$key_type})) {
            $this->{$key_type}['user'] = $user_id;
        }
        if ($notify_id && array_key_exists('notification', $this->{$key_type})) {
            $this->{$key_type}['notification'] = $notify_id;
        }
        if ($unread !== null && array_key_exists('unread', $this->{$key_type})) {
            $this->{$key_type}['unread'] = $unread;
        }
        if ($email !== null && array_key_exists('email', $this->{$key_type})) {
            $this->{$key_type}['email'] = $email;
        }
        if ($device !== null && array_key_exists('device', $this->{$key_type})) {
            $this->{$key_type}['device'] = $device;
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
     * @return string
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
        $data = [
            'id'      => $notify_id,
            'user_id' => $my_id,
            'body'    => $body,
            'url'     => Router::url(array_merge($url, ['notify_id' => $notify_id]), true),
            'type'    => $type,
            'created' => $date,
        ];
        /** @noinspection PhpInternalEntityUsedInspection */
        $pipe = $this->Db->multi(Redis::PIPELINE);
        //save notification
        $pipe->hMset($this->getKeyName(self::KEY_TYPE_NOTIFICATION, $team_id, null, $notify_id), $data);
        $pipe->expire($this->getKeyName(self::KEY_TYPE_NOTIFICATION, $team_id, null, $notify_id),
                      60 * 60 * 24 * self::EXPIRE_DAY_OF_NOTIFICATION);

        $score = substr_replace((string)(microtime(true) * 10000), '1', -1, 1);
        //save notification user process
        foreach ($to_user_ids as $uid) {
            //save notification user
            $pipe->zAdd($this->getKeyName(self::KEY_TYPE_NOTIFICATION_USER, $team_id, $uid, null), $score,
                        $notify_id);
            $pipe->zAdd($this->getKeyName(self::KEY_TYPE_NOTIFICATION_USER, $team_id, $uid, null, 0), $score,
                        $notify_id);
            //increment
            $pipe->hIncrBy($this->getKeyName(self::KEY_TYPE_COUNT_BY_USER, $team_id, $uid),
                           self::FIELD_COUNT_NEW_NOTIFY, 1);
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
        $count = $this->Db->hGet($this->getKeyName(self::KEY_TYPE_COUNT_BY_USER, $team_id, $user_id),
                                 self::FIELD_COUNT_NEW_NOTIFY);
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
        $res = $this->Db->hDel($this->getKeyName(self::KEY_TYPE_COUNT_BY_USER, $team_id, $user_id),
                               self::FIELD_COUNT_NEW_NOTIFY);
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
        $notify_date = $this->Db->hGet($this->getKeyName(self::KEY_TYPE_NOTIFICATION, $team_id, null, $notify_id),
                                       'created');
        if ($notify_date === false) {
            return false;
        }

        $notify_date = substr_replace((string)((float)($notify_date) * 10000), $unread, -1, 1);

        /** @noinspection PhpInternalEntityUsedInspection */
        $pipe = $this->Db->multi(Redis::PIPELINE);

        //delete set
        $deleted_count = $pipe->zDelete($this->getKeyName(self::KEY_TYPE_NOTIFICATION_USER, $team_id, $user_id),
                                        $notify_id);
        if ($deleted_count === 0) {
            return false;
        }
        $pipe->zDelete($this->getKeyName(self::KEY_TYPE_NOTIFICATION_USER, $team_id, $user_id, null, 0), $notify_id);
        $pipe->zDelete($this->getKeyName(self::KEY_TYPE_NOTIFICATION_USER, $team_id, $user_id, null, 1), $notify_id);

        //add set for unread status
        $pipe->zAdd($this->getKeyName(self::KEY_TYPE_NOTIFICATION_USER, $team_id, $user_id, null, $unread),
                    $notify_date,
                    $notify_id);
        //add set for all
        $pipe->zAdd($this->getKeyName(self::KEY_TYPE_NOTIFICATION_USER, $team_id, $user_id), $notify_date,
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
    function getNotifications($team_id, $user_id, $limit = null, $from_date = null)
    {
        $delete_time_from = (string)((microtime(true) - (60 * 60 * 24 * self::EXPIRE_DAY_OF_NOTIFICATION)) * 10000);
        //delete from notification user
        $this->Db->zRemRangeByScore($this->getKeyName(self::KEY_TYPE_NOTIFICATION_USER, $team_id, $user_id), 0,
                                    $delete_time_from);

        if ($limit === null) {
            $limit = -1;
        }
        if ($from_date === null) {
            if ($limit !== -1) {
                $limit--;
            }
            $notify_list = $this->Db->zRevRange($this->getKeyName(self::KEY_TYPE_NOTIFICATION_USER, $team_id, $user_id),
                                                0, $limit, true);
        }
        else {
            $notify_list = $this->Db->zRevRangeByScore($this->getKeyName(self::KEY_TYPE_NOTIFICATION_USER, $team_id,
                                                                         $user_id),
                                                       $from_date, -1,
                                                       ['limit' => [1, $limit], 'withscores' => true]);
        }
        if (empty($notify_list)) {
            return null;
        }
        /** @noinspection PhpInternalEntityUsedInspection */
        $pipe = $this->Db->multi(Redis::PIPELINE);
        foreach ($notify_list as $notify_id => $score) {
            $pipe->hGetAll($this->getKeyName(self::KEY_TYPE_NOTIFICATION, $team_id, $user_id, $notify_id));
        }
        $pipe_res = $pipe->exec();
        foreach ($pipe_res as $k => $v) {
            $score = $notify_list[$v['id']];
            $pipe_res[$k]['score'] = $score;
            if (substr_compare((string)$score, "1", -1, 1) === 0) {
                $pipe_res[$k]['unread_flg'] = true;
            }
            else {
                $pipe_res[$k]['unread_flg'] = false;

            }
        }
        return $pipe_res;
    }

    /**
     * @param      $user_id
     * @param null $ip_address
     *
     * @return bool|string
     */
    function makeDeviceHash($user_id, $ip_address = null)
    {
        $browser_info = get_browser(CakeRequest::header('User-Agent'));
        if (empty($browser_info) === true) {
            return false;
        }

        $platform = $browser_info->platform;
        $browser = $browser_info->browser;
        if (empty($platform) === true || empty($browser) === true) {
            return false;
        }
        return Security::hash($platform . $browser . $user_id . $ip_address, 'sha1', true);
    }

    /**
     * @param $team_id
     * @param $user_id
     *
     * @return int
     */
    function saveDeviceHash($team_id, $user_id)
    {
        $key = $this->getKeyName(self::KEY_TYPE_TWO_FA_DEVICE_HASHES, $team_id, $user_id);
        $hash_key = $this->makeDeviceHash($user_id);
        $ex_date = time() + TWO_FA_TTL;
        $res = $this->Db->hSet($key, $hash_key, $ex_date);
        $this->Db->setTimeout($key, TWO_FA_TTL);
        return $res;
    }

    /**
     * @param $team_id
     * @param $user_id
     *
     * @return bool
     */
    function isExistsDeviceHash($team_id, $user_id)
    {
        $key = $this->getKeyName(self::KEY_TYPE_TWO_FA_DEVICE_HASHES, $team_id, $user_id);
        $hash_key = $this->makeDeviceHash($user_id);
        $res = $this->Db->hGet($key, $hash_key);
        if (!$res) {
            return false;
        }
        if (time() > (int)$res) {
            return false;
        }
        //if exists then set new timeout
        $this->Db->setTimeout($key, TWO_FA_TTL);

        return true;
    }

    /**
     * @param $team_id
     * @param $user_id
     *
     * @return int
     */
    function deleteDeviceHash($team_id, $user_id)
    {
        $key = $this->getKeyName(self::KEY_TYPE_TWO_FA_DEVICE_HASHES, $team_id, $user_id);
        return $this->Db->del($key);
    }

    /**
     * @param      $email
     * @param null $ip_address
     *
     * @return bool
     */
    function isAccountLocked($email, $ip_address = null)
    {
        $device = $this->makeDeviceHash($email, $ip_address);
        $key = $this->getKeyName(self::KEY_TYPE_LOGIN_FAIL, null, null, null, null, $email, $device);
        $count = $this->Db->incr($key);
        if ($count !== false && $count >= ACCOUNT_LOCK_COUNT) {
            return true;
        }
        $this->Db->setTimeout($key, ACCOUNT_LOCK_TTL);
        return false;
    }

}

