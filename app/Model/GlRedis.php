<?php
App::uses('AppModel', 'Model');
App::uses('ConnectionManager', 'Model');
App::uses('NotifySetting', 'Model');

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
     * Skip checking if my_id actually defined
     *
     * @var bool
     */
    private $skipCheckMyId = false;

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
    const KEY_TYPE_MESSAGE_USER = 'message_user_key';
    const KEY_TYPE_MESSAGE = 'message_key';
    const KEY_TYPE_NOTIFICATION_COUNT = 'new_notification_count_key';
    const KEY_TYPE_LOGIN_FAIL = 'login_fail_key';
    const KEY_TYPE_TWO_FA_FAIL = 'two_fa_fail_key';
    const KEY_TYPE_COUNT_BY_USER = 'count_by_user_key';
    const KEY_TYPE_COUNT_MESSAGE_BY_USER = 'count_message_by_user_key';
    const KEY_TYPE_TWO_FA_DEVICE_HASHES = 'two_fa_device_hashes_key';
    const KEY_TYPE_PRE_UPLOAD_FILE = 'pre_upload_file_key';
    const KEY_TYPE_ACCESS_USER = 'access_user_key';
    const KEY_TYPE_TEAM_INSIGHT = 'team_insight';
    const KEY_TYPE_GROUP_INSIGHT = 'group_insight';
    const KEY_TYPE_CIRCLE_INSIGHT = 'circle_insight';
    const KEY_TYPE_TEAM_RANKING = 'team_ranking';
    const KEY_TYPE_GROUP_RANKING = 'group_ranking';
    const KEY_TYPE_SETUP_GUIDE_STATUS = 'setup_guide_status';
    const KEY_TYPE_FAIL_EMAIL_VERIFY_DIGIT_CODE = 'fail_email_verify_digit_code';
    const KEY_TYPE_CHANGED_TERM = 'changed_term';
    const KEY_TYPE_MST_CAMPAIGN_PLANS = 'mst_campaign_plans';
    const KEY_TYPE_MAP_SES_AND_JWT = 'map_ses_and_jwt';

    const FIELD_COUNT_NEW_NOTIFY = 'new_notify';
    const FIELD_SETUP_LAST_UPDATE_TIME = "setup_last_update_time";

    static public $KEY_TYPES = [
        self::KEY_TYPE_NOTIFICATION_USER,
        self::KEY_TYPE_NOTIFICATION,
        self::KEY_TYPE_NOTIFICATION_COUNT,
        self::KEY_TYPE_LOGIN_FAIL,
        self::KEY_TYPE_TWO_FA_FAIL,
        self::KEY_TYPE_COUNT_BY_USER,
        self::KEY_TYPE_COUNT_MESSAGE_BY_USER,
        self::KEY_TYPE_TWO_FA_DEVICE_HASHES,
        self::KEY_TYPE_PRE_UPLOAD_FILE,
        self::KEY_TYPE_MESSAGE,
        self::KEY_TYPE_MESSAGE_USER,
        self::KEY_TYPE_ACCESS_USER,
        self::KEY_TYPE_TEAM_INSIGHT,
        self::KEY_TYPE_GROUP_INSIGHT,
        self::KEY_TYPE_CIRCLE_INSIGHT,
        self::KEY_TYPE_TEAM_RANKING,
        self::KEY_TYPE_GROUP_RANKING,
        self::KEY_TYPE_SETUP_GUIDE_STATUS,
        self::KEY_TYPE_FAIL_EMAIL_VERIFY_DIGIT_CODE,
        self::KEY_TYPE_CHANGED_TERM,
        self::KEY_TYPE_MST_CAMPAIGN_PLANS,
        self::KEY_TYPE_MAP_SES_AND_JWT
    ];

    /**
     * Expire day of notification
     */
    const EXPIRE_DAY_OF_NOTIFICATION = 14;
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
     * Key Name: team:[team_id]:user:[user_id]:messages:unread:[0 or 1]:
     *
     * @var array
     */
    private /** @noinspection PhpUnusedPrivateFieldInspection */
        $message_user_key = [
        'team'     => null,
        'user'     => null,
        'messages' => null,
        'unread'   => 1,
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
     * Key Name: team:[team_id]:message:[notify_id]:
     *
     * @var array
     */
    private /** @noinspection PhpUnusedPrivateFieldInspection */
        $message_key = [
        'team'    => null,
        'message' => null,
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
     * Key Name: team:[team_id]:user:[user_id]:new_notification_count:
     *
     * @var array
     */
    private /** @noinspection PhpUnusedPrivateFieldInspection */
        $count_message_by_user_key = [
        'team'          => null,
        'user'          => null,
        'message_count' => null,
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
     * Key Name: email:[email]:device:[device_hash]:fail_email_verify_digit_code_count:
     *
     * @var array
     */
    private /** @noinspection PhpUnusedPrivateFieldInspection */
        $fail_email_verify_digit_code = [
        'email'      => null,
        'device'     => null,
        'fail_count' => null,
    ];

    /**
     * Key Name: user:[user_id]:device:[device_hash]:two_fa:fail_count:
     *
     * @var array
     */
    private /** @noinspection PhpUnusedPrivateFieldInspection */
        $two_fa_fail_key = [
        'user'       => null,
        'device'     => null,
        'two_fa'     => null,
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

    /**
     * Key Name: team:[team_id]:user:[user_id]:pre_upload_file_hash:[hash]
     *
     * @var array
     */
    private /** @noinspection PhpUnusedPrivateFieldInspection */
        $pre_upload_file_key = [
        'team'            => null,
        'user'            => null,
        'unique_id'       => null,
        'pre_upload_file' => null,
    ];

    /**
     * Key Name: team:[team_id]:date:[date]:timezone:[timezone]:access_user:
     *
     * @var array
     */
    private /** @noinspection PhpUnusedPrivateFieldInspection */
        $access_user_key = [
        'team'        => null,
        'date'        => null,
        'timezone'    => null,
        'access_user' => null,
    ];

    /**
     * Key Name: team:[team_id]:start:[date]:end:[date]:timezone:[timezone]:team_insight:
     *
     * @var array
     */
    private /** @noinspection PhpUnusedPrivateFieldInspection */
        $team_insight = [
        'team'         => null,
        'start'        => null,
        'end'          => null,
        'timezone'     => null,
        'team_insight' => null,
    ];

    /**
     * Key Name: team:[team_id]:start:[date]:end:[date]:timezone:[timezone]:group:[group_id]:group_insight:
     *
     * @var array
     */
    private /** @noinspection PhpUnusedPrivateFieldInspection */
        $group_insight = [
        'team'          => null,
        'start'         => null,
        'end'           => null,
        'timezone'      => null,
        'group'         => null,
        'group_insight' => null,
    ];

    /**
     * Key Name: team:[team_id]:start:[date]:end:[date]:timezone:[timezone]:circle_insight:
     *
     * @var array
     */
    private /** @noinspection PhpUnusedPrivateFieldInspection */
        $circle_insight = [
        'team'           => null,
        'start'          => null,
        'end'            => null,
        'timezone'       => null,
        'circle_insight' => null,
    ];

    /**
     * Key Name: team:[team_id]:start:[date]:end:[date]:timezone:[timezone]:team_ranking:
     *
     * @var array
     */
    private /** @noinspection PhpUnusedPrivateFieldInspection */
        $team_ranking = [
        'team'         => null,
        'start'        => null,
        'end'          => null,
        'timezone'     => null,
        'team_ranking' => null,
    ];

    /**
     * Key Name: team:[team_id]:start:[date]:end:[date]:timezone:[timezone]:group:[group_id]:group_ranking:
     *
     * @var array
     */
    private /** @noinspection PhpUnusedPrivateFieldInspection */
        $group_ranking = [
        'team'          => null,
        'start'         => null,
        'end'           => null,
        'timezone'      => null,
        'group'         => null,
        'group_ranking' => null,
    ];

    /**
     * Key Name: user:[user_id]
     *
     * @var array
     */
    private /** @noinspection PhpUnusedPrivateFieldInspection */
        $setup_guide_status = [
        'user' => null,
    ];

    /**
     * Key Name: team:[team_id]
     *
     * @var array
     */
    private /** @noinspection PhpUnusedPrivateFieldInspection */
        $changed_term = [
        'team'         => null,
        'changed_term' => null,
    ];

    /**
     * @var array
     */
    private /** @noinspection PhpUnusedPrivateFieldInspection */
        $map_ses_and_jwt = [
        'team'            => null,
        'user'            => null,
        'map_ses_and_jwt' => null,
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
     * キーを指定して削除
     * $targetに"*"は利用できない
     * $targetの例:
     *   前方一致: 'hoge*'
     *   後方一致: '*hoge'
     *   中間一致: '*hoge*'
     *
     * @param string $pattern
     *
     * @return int
     * @throws Exception
     */
    public function deleteKeys(string $pattern)
    {
        if ($pattern == '*') {
            throw new Exception('cannot use "*" for target. if want to delete all key, use method deleteAllData()');
        }
        $keys = $this->Db->keys($pattern);
        /** @noinspection PhpInternalEntityUsedInspection */
        $pipe = $this->Db->multi(Redis::PIPELINE);
        $prefix = $this->Db->config['prefix'];
        foreach ($keys as $k) {
            $k = str_replace($prefix, "", $k);
            $pipe->delete($k);
        }
        $pipe->exec();
    }

    /**
     * Delete users' active team lists from cache
     *
     * @param int[] $userIds
     */
    public function deleteUserTeamList(array $userIds)
    {
        $targetKeys = [];
        foreach ($userIds as $userId) {
            $pattern =
                Cache::settings('team_info')['prefix'] .
                $this->getCacheKey(CACHE_KEY_TEAM_LIST, true, $userId, false);
            $targetKeys[] = $pattern;
        }
        $pipe = $this->Db->multi(Redis::PIPELINE);
        $prefix = $this->Db->config['prefix'];
        foreach ($targetKeys as $k) {
            $k = str_replace($prefix, "", $k);
            $pipe->delete($k);
        }
        $pipe->exec();
    }

    /**
     * @param string        $key_type One of $KEY_TYPES
     * @param int           $team_id
     * @param null|int      $user_id
     * @param null|string   $notify_id
     * @param bool|int|null $unread
     * @param null|string   $email
     * @param null|string   $device
     * @param null|string   $unique_id
     * @param null|string   $date
     * @param null|string   $timezone
     * @param null|string   $start
     * @param null|string   $end
     * @param null|string   $group
     *
     * @return string
     */
    private function getKeyName(
        $key_type,
        $team_id = null,
        $user_id = null,
        $notify_id = null,
        $unread = null,
        $email = null,
        $device = null,
        $unique_id = null,
        $date = null,
        $timezone = null,
        $start = null,
        $end = null,
        $group = null
    )
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
        if ($unique_id !== null && array_key_exists('unique_id', $this->{$key_type})) {
            $this->{$key_type}['unique_id'] = $unique_id;
        }
        if ($notify_id && array_key_exists('message', $this->{$key_type})) {
            $this->{$key_type}['message'] = $notify_id;
        }
        if ($date && array_key_exists('date', $this->{$key_type})) {
            $this->{$key_type}['date'] = $date;
        }
        if ($timezone && array_key_exists('timezone', $this->{$key_type})) {
            $this->{$key_type}['timezone'] = $timezone;
        }
        if ($start && array_key_exists('start', $this->{$key_type})) {
            $this->{$key_type}['start'] = $start;
        }
        if ($end && array_key_exists('end', $this->{$key_type})) {
            $this->{$key_type}['end'] = $end;
        }
        if ($group && array_key_exists('group', $this->{$key_type})) {
            $this->{$key_type}['group'] = $group;
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
        return CakeText::uuid();
    }

    /**
     * @param string $type        Notification type
     * @param int    $team_id     Team ID for the notification
     * @param array  $to_user_ids User IDs of notification receivers
     * @param int    $my_id       User ID of notification sender
     * @param string $body        Notification message
     * @param string $url         Page URL when the notification is clicked
     * @param int    $date        UNIX timestamp of when the notification was created
     * @param int    $post_id     ID of post related to the notification
     * @param array  $options
     *
     * @return bool TRUE = succesfully save notification into REDIS.
     */
    public function setNotifications(
        $type,
        $team_id,
        $to_user_ids = [],
        $my_id,
        $body,
        $url,
        $date,
        $post_id = null,
        $options = []
    )
    {

        $parameterErrorArray = $this->_validateGlRedisParameters($type, $team_id, $my_id, $body, $url, $date);

        if (!empty($parameterErrorArray)) {
            GoalousLog::error("Parameter error at " . implode(",", $parameterErrorArray) . " | Trace:\n" .
                implode("\n", AppUtil::getMethodCallerTrace()));
            return false;
        }

        $notify_id = $this->generateId();
        if (!empty($post_id)) {
            // $post_idが渡ってきている場合はメッセージ
            // で1ポストあたり1notifyなのでnotify_idをpost_idで置き換える
            $notify_id = $post_id;
        }
        // if not message, attach notify id for the url.
        if ($type != NotifySetting::TYPE_MESSAGE) {
            if (isset($url['?'])) {
                $url['?'] = am($url['?'], ['notify_id' => $notify_id]);
            } else {
                $url = am($url, ['?' => ['notify_id' => $notify_id]]);
            }
        }
        $data = [
            'id'            => $notify_id,
            'user_id'       => $my_id,
            'body'          => $body,
            'url'           => Router::url($url, true),
            'type'          => $type,
            'to_user_count' => count($to_user_ids),
            'created'       => $date,
            'options'       => $options,
        ];
        /** @noinspection PhpInternalEntityUsedInspection */
        $pipe = $this->Db->multi(Redis::PIPELINE);
        //save notification
        if ($type == NotifySetting::TYPE_MESSAGE) {
            $pipe->hMset($this->getKeyName(self::KEY_TYPE_MESSAGE, $team_id, null, $notify_id), $data);
            $pipe->expire($this->getKeyName(self::KEY_TYPE_MESSAGE, $team_id, null, $notify_id),
                60 * 60 * 24 * self::EXPIRE_DAY_OF_NOTIFICATION);
        } else {
            $pipe->hMset($this->getKeyName(self::KEY_TYPE_NOTIFICATION, $team_id, null, $notify_id), $data);
            $pipe->expire($this->getKeyName(self::KEY_TYPE_NOTIFICATION, $team_id, null, $notify_id),
                60 * 60 * 24 * self::EXPIRE_DAY_OF_NOTIFICATION);
        }

        $score = substr_replace((string)(microtime(true) * 10000), '1', -1, 1);

        //save notification user process
        foreach ($to_user_ids as $uid) {
            //save notification user
            if ($type == NotifySetting::TYPE_MESSAGE) {
                $pipe->zAdd($this->getKeyName(self::KEY_TYPE_MESSAGE_USER, $team_id, $uid, null), $score, $notify_id);
                $pipe->zAdd($this->getKeyName(self::KEY_TYPE_MESSAGE_USER, $team_id, $uid, null, 0), $score,
                    $notify_id);
            } else {
                $pipe->zAdd($this->getKeyName(self::KEY_TYPE_NOTIFICATION_USER, $team_id, $uid, null), $score,
                    $notify_id);
                $pipe->zAdd($this->getKeyName(self::KEY_TYPE_NOTIFICATION_USER, $team_id, $uid, null, 0), $score,
                    $notify_id);
                //increment
                $pipe->hIncrBy($this->getKeyName(self::KEY_TYPE_COUNT_BY_USER, $team_id, $uid),
                    self::FIELD_COUNT_NEW_NOTIFY, 1);
            }

        }
        $pipe->exec();
        //メッセージの通知数は1スレッドあたり1だけなのでKEY_TYPE_MESSAGE_USERのレコード数
        //なのでredisに書き込みが終わった後じゃないとカウントできないからここでやる
        foreach ($to_user_ids as $uid) {
            if ($type == NotifySetting::TYPE_MESSAGE) {
                $this->updateCountOfNewMessageNotification($team_id, $uid, $pipe);
            }
        }
        $pipe->exec();
        return true;
    }

    /**
     * メッセージの未読件数の更新処理
     * $pipeで使う場合とそうでない場合があるので、両方に対応できるようにしている
     * $pipe: $this->Db->multi(Redis::PIPELINE)
     *
     * @param              $team_id
     * @param              $uid
     * @param Redis        $pipe
     *
     * @return bool
     */
    function updateCountOfNewMessageNotification($team_id, $uid, Redis $pipe = null)
    {
        $message_notify_count = $this->Db->zCard($this->getKeyName(self::KEY_TYPE_MESSAGE_USER, $team_id, $uid));

        if (is_object($pipe)) {
            $pipe->hSet(
                $this->getKeyName(self::KEY_TYPE_COUNT_MESSAGE_BY_USER, $team_id, $uid),
                self::FIELD_COUNT_NEW_NOTIFY, $message_notify_count);
        } else {
            $this->Db->hSet(
                $this->getKeyName(self::KEY_TYPE_COUNT_MESSAGE_BY_USER, $team_id, $uid),
                self::FIELD_COUNT_NEW_NOTIFY, $message_notify_count);
        }
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
     * @param $team_id
     * @param $user_id
     *
     * @return int
     */
    function getCountOfNewMessageNotification($team_id, $user_id)
    {
        $count = $this->Db->hGet($this->getKeyName(self::KEY_TYPE_COUNT_MESSAGE_BY_USER, $team_id, $user_id),
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
     * @param  $team_id
     * @param  $user_id
     *
     * @return bool
     */
    function deleteCountOfNewMessageNotification($team_id, $user_id)
    {
        $res = $this->Db->hDel($this->getKeyName(self::KEY_TYPE_COUNT_MESSAGE_BY_USER, $team_id, $user_id),
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
        $pipe->zDelete($this->getKeyName(self::KEY_TYPE_NOTIFICATION_USER, $team_id, $user_id),
            $notify_id);
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
     * @return array
     */
    function getNotifications($team_id, $user_id, $limit = null, $from_date = null)
    {
        if ($from_date === 0) {
            $from_date = null;
        }
        
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
        } else {
            $notify_list = $this->Db->zRevRangeByScore($this->getKeyName(self::KEY_TYPE_NOTIFICATION_USER, $team_id,
                $user_id),
                $from_date, -1,
                ['limit' => [1, $limit], 'withscores' => true]);
        }
        if (empty($notify_list)) {
            return $notify_list;
        }
        /** @noinspection PhpInternalEntityUsedInspection */
        $pipe = $this->Db->multi(Redis::PIPELINE);
        foreach ($notify_list as $notify_id => $score) {
            $pipe->hGetAll($this->getKeyName(self::KEY_TYPE_NOTIFICATION, $team_id, $user_id, $notify_id));
        }
        $pipe_res = $pipe->exec();

        foreach ($pipe_res as $k => $v) {
            if (empty($v)) {
                unset($pipe_res[$k]);
                continue;
            }
            if (!key_exists('id', $v)) {
                GoalousLog::error('Empty notification content:', $v);
                unset($pipe_res[$k]);
                continue;
            }
            $score = $notify_list[$v['id']];
            $pipe_res[$k]['score'] = $score;
            if (substr_compare((string)$score, "1", -1, 1) === 0) {
                $pipe_res[$k]['unread_flg'] = true;
            } else {
                $pipe_res[$k]['unread_flg'] = false;

            }
        }

        return array_values($pipe_res);
    }

    public function getNotifyIds($team_id, $user_id, $limit = null, $from_date = null)
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
        } else {
            $notify_list = $this->Db->zRevRangeByScore($this->getKeyName(self::KEY_TYPE_NOTIFICATION_USER, $team_id,
                $user_id),
                $from_date, -1,
                ['limit' => [1, $limit], 'withscores' => true]);
        }
        return $notify_list;
    }

    /**
     * @param int  $team_id
     * @param int  $user_id
     * @param null $limit
     * @param null $from_date
     *
     * @return array
     */
    function getMessageNotifications($team_id, $user_id, $limit = null, $from_date = null)
    {
        $delete_time_from = (string)((microtime(true) - (60 * 60 * 24 * self::EXPIRE_DAY_OF_NOTIFICATION)) * 10000);
        //delete from notification user
        $this->Db->zRemRangeByScore($this->getKeyName(self::KEY_TYPE_MESSAGE_USER, $team_id, $user_id), 0,
            $delete_time_from);

        if ($limit === null) {
            $limit = -1;
        }
        if ($from_date === null) {
            if ($limit !== -1) {
                $limit--;
            }
            $notify_list = $this->Db->zRevRange($this->getKeyName(self::KEY_TYPE_MESSAGE_USER, $team_id, $user_id),
                0, $limit, true);
        } else {
            $notify_list = $this->Db->zRevRangeByScore($this->getKeyName(self::KEY_TYPE_MESSAGE_USER, $team_id,
                $user_id),
                $from_date, -1,
                ['limit' => [1, $limit], 'withscores' => true]);
        }
        if (empty($notify_list)) {
            return [];
        }
        /** @noinspection PhpInternalEntityUsedInspection */
        $pipe = $this->Db->multi(Redis::PIPELINE);
        foreach ($notify_list as $notify_id => $score) {
            $pipe->hGetAll($this->getKeyName(self::KEY_TYPE_MESSAGE, $team_id, $user_id, $notify_id));
        }
        $pipe_res = $pipe->exec();
        foreach ($pipe_res as $k => $v) {
            if (empty($v)) {
                GoalousLog::error('Empty notification content:', $pipe_res);
                unset($pipe_res[$k]);
                continue;
            } elseif (!key_exists('id', $v)) {
                GoalousLog::error('Empty notification content:', $v);
                unset($pipe_res[$k]);
                continue;
            }
            $score = $notify_list[$v['id']];
            $pipe_res[$k]['score'] = $score;
            if (substr_compare((string)$score, "1", -1, 1) === 0) {
                $pipe_res[$k]['unread_flg'] = true;
            } else {
                $pipe_res[$k]['unread_flg'] = false;

            }
        }
        return array_values($pipe_res);
    }

    /**
     * @param    int|string $userId
     * @param               $ipAddress
     *
     * @return bool|string
     * @throws Exception
     */
    function makeDeviceHash($userId, $ipAddress = null)
    {
        $browscap = new \BrowscapPHP\Browscap();
        $browser_info = $browscap->getBrowser(CakeRequest::header('User-Agent'));
        if (empty($browser_info) === true) {
            return false;
        }
        $platform = $browser_info->platform;
        $browser = $browser_info->browser;
        if (empty($platform) === true || empty($browser) === true) {
            return false;
        }
        return Security::hash($platform . $browser . $userId . $ipAddress, 'sha1', true);
    }

    /**
     * @param $team_id
     * @param $user_id
     * @param $ip_address
     *
     * @return int
     */
    function saveDeviceHash($team_id, $user_id, $ip_address = null)
    {
        $key = $this->getKeyName(self::KEY_TYPE_TWO_FA_DEVICE_HASHES, $team_id, $user_id);
        $hash_key = $this->makeDeviceHash($user_id, $ip_address);
        $ex_date = time() + TWO_FA_TTL;
        $res = $this->Db->hSet($key, $hash_key, $ex_date);
        $this->Db->setTimeout($key, TWO_FA_TTL);
        return $res;
    }

    /**
     * @param $team_id
     * @param $user_id
     * @param $ip_address
     *
     * @return bool
     */
    function isExistsDeviceHash($team_id, $user_id, $ip_address = null)
    {
        $key = $this->getKeyName(self::KEY_TYPE_TWO_FA_DEVICE_HASHES, $team_id, $user_id);
        $hash_key = $this->makeDeviceHash($user_id, $ip_address);
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
        $count = $this->Db->get($key);
        if ($count !== false && $count >= ACCOUNT_LOCK_COUNT) {
            return true;
        }
        return false;
    }

    /**
     * @param string      $email
     * @param string|null $ipAddress
     *
     * @return int
     */
    function resetLoginFailedCount(string $email, $ipAddress = null)
    {
        $device = $this->makeDeviceHash($email, $ipAddress);
        $key = $this->getKeyName(self::KEY_TYPE_LOGIN_FAIL, null, null, null, null, $email, $device);
        return $this->Db->del($key);
    }

    /**
     * @param string      $email
     * @param string|null $ipAddress
     *
     * @return int $count It's failed count
     */
    function incrementLoginFailedCount(string $email, $ipAddress = null): int
    {
        $device = $this->makeDeviceHash($email, $ipAddress);
        $key = $this->getKeyName(self::KEY_TYPE_LOGIN_FAIL, null, null, null, null, $email, $device);
        $count = $this->Db->incr($key);
        $this->Db->setTimeout($key, ACCOUNT_LOCK_TTL);
        return $count;
    }

    /**
     * @param      $email
     * @param null $ip_address
     *
     * @return bool
     */
    function isEmailVerifyCodeLocked($email, $ip_address = null)
    {
        $device = $this->makeDeviceHash($email, $ip_address);
        $key = $this->getKeyName(self::KEY_TYPE_FAIL_EMAIL_VERIFY_DIGIT_CODE, null, null, null, null, $email, $device);
        $count = $this->Db->incr($key);
        if ($count !== false && $count >= EMAIL_VERIFY_CODE_LOCK_COUNT) {
            return true;
        }
        $this->Db->setTimeout($key, EMAIL_VERIFY_CODE_LOCK_TTL);
        return false;
    }

    /**
     * @param      $user_id
     * @param null $ip_address
     *
     * @return bool
     */
    function isTwoFaAccountLocked($user_id, $ip_address = null)
    {
        $device = $this->makeDeviceHash($user_id, $ip_address);
        $key = $this->getKeyName(self::KEY_TYPE_TWO_FA_FAIL, null, $user_id, null, null, null, $device);
        $count = $this->Db->incr($key);
        if ($count !== false && $count >= ACCOUNT_LOCK_COUNT) {
            return true;
        }
        $this->Db->setTimeout($key, ACCOUNT_LOCK_TTL);
        return false;
    }

    /**
     * delete message notify.
     *
     * @param $team_id
     * @param $user_id
     * @param $notify_id
     *
     * @return int
     */
    function deleteMessageNotify($team_id, $user_id, $notify_id)
    {
        $key = $this->getKeyName(self::KEY_TYPE_MESSAGE_USER, $team_id, $user_id);
        return $this->Db->zRem($key, $notify_id);
    }

    function savePreUploadFile($file_info, $team_id, $user_id)
    {
        $file = [
            'info'    => $file_info,
            'content' => file_get_contents($file_info['tmp_name']),
        ];
        $file['info']['remote'] = true;

        $hash_key = $this->generateId();
        $key = $this->getKeyName(self::KEY_TYPE_PRE_UPLOAD_FILE, $team_id, $user_id, null, null, null, null, $hash_key);

        // Set new memory limit
        // With a file of 88M, PHP have a memory usage peak of 168M.
        // With a file of 97M, the consumption goes to 281M (over the initial 256M limit).
        $memoryLimit = trim(ini_get('memory_limit'));
        AppUtil::iniSetMemoryLimitAtLeast('384M');

        // Serialize the uploaded file
        $this->Db->set($key, serialize($file));
        $this->Db->setTimeout($key, PRE_FILE_TTL);

        // Put back to the previous settings
        ini_set('memory_limit', $memoryLimit);

        return $hash_key;
    }

    function getPreUploadedFile($team_id, $user_id, $hash_key)
    {
        $key = $this->getKeyName(self::KEY_TYPE_PRE_UPLOAD_FILE, $team_id, $user_id, null, null, null, null, $hash_key);
        return unserialize($this->Db->get($key));
    }

    function delPreUploadedFile($team_id, $user_id, $hash_key)
    {
        $key = $this->getKeyName(self::KEY_TYPE_PRE_UPLOAD_FILE, $team_id, $user_id, null, null, null, null, $hash_key);
        return $this->Db->del($key);
    }

    /**
     * ユーザーのサイトアクセス日をタイムゾーンごとに保存
     *
     * @param int   $team_id
     * @param int   $user_id
     * @param int   $access_time アクセス時間 (unix timestamp, UTC)
     * @param array $timezones   タイムゾーンのリスト
     *
     * @return int
     */
    function saveAccessUser($team_id, $user_id, $access_time, $timezones)
    {
        /** @noinspection PhpInternalEntityUsedInspection */
        $pipe = $this->Db->multi(Redis::PIPELINE);
        foreach ($timezones as $timezone) {
            $access_date = date('Y-m-d', $access_time + intval($timezone * HOUR));
            $pipe->sAdd($this->getKeyName(self::KEY_TYPE_ACCESS_USER, $team_id, null, null, null, null, null,
                null, $access_date, $timezone), $user_id);
        }
        $pipe->exec();
        return true;
    }

    /**
     * パターンにマッチしたkeyを削除
     *
     * @param $pattern
     *
     * @return int
     */
    function dellKeys($pattern)
    {
        if ($pattern == "*") {
            throw new RuntimeException(__("Not allowed to specify."));
        }
        $keys = $this->Db->keys($pattern);
        /** @noinspection PhpInternalEntityUsedInspection */
        $pipe = $this->Db->multi(Redis::PIPELINE);
        $env_name = ENV_NAME . ":";
        foreach ($keys as $key) {
            $key = preg_replace("/^{$env_name}/", "", $key);
            $pipe->delete($key);
        }
        $pipe->exec();
        return count($keys);
    }

    function getKeyCount($pattern)
    {
        $keys = $this->Db->keys($pattern);
        return count($keys);
    }

    /**
     * サイトにアクセスしたユーザーのIDリストを返す
     *
     * @param int       $team_id
     * @param string    $access_date アクセス日付
     * @param int|float $timezone    タイムゾーン
     *
     * @return array
     */
    function getAccessUsers($team_id, $access_date, $timezone)
    {
        return $this->Db->sMembers($this->getKeyName(self::KEY_TYPE_ACCESS_USER, $team_id, null, null, null,
            null, null, null, $access_date, $timezone));
    }

    /**
     * サイトにアクセスしたユーザーデータを削除
     *
     * @param int       $team_id
     * @param string    $access_date アクセス日付
     * @param int|float $timezone    タイムゾーン
     *
     * @return int
     */
    function delAccessUsers($team_id, $access_date, $timezone)
    {
        return $this->Db->del($this->getKeyName(self::KEY_TYPE_ACCESS_USER, $team_id, null, null, null,
            null, null, null, $access_date, $timezone));
    }

    /**
     * チーム集計データを保存
     *
     * @param $team_id
     * @param $start_date
     * @param $end_date
     * @param $timezone
     * @param $insight
     * @param $expire
     *
     * @return bool
     */
    function saveTeamInsight($team_id, $start_date, $end_date, $timezone, $insight, $expire = WEEK)
    {
        $key = $this->getKeyName(self::KEY_TYPE_TEAM_INSIGHT, $team_id, null, null, null, null, null, null, null,
            $timezone, $start_date, $end_date);
        $this->Db->set($key, json_encode($insight));
        return $this->Db->setTimeout($key, $expire);
    }

    /**
     * チーム集計データを返す
     *
     * @param $team_id
     * @param $start_date
     * @param $end_date
     * @param $timezone
     *
     * @return mixed
     */
    function getTeamInsight($team_id, $start_date, $end_date, $timezone)
    {
        $insight_str = $this->Db->get($this->getKeyName(self::KEY_TYPE_TEAM_INSIGHT, $team_id,
            null, null, null, null, null, null, null,
            $timezone, $start_date, $end_date));
        return json_decode($insight_str, true);

    }

    /**
     * グループ集計データを保存
     *
     * @param $team_id
     * @param $start_date
     * @param $end_date
     * @param $timezone
     * @param $group_id
     * @param $insight
     * @param $expire
     *
     * @return bool
     */
    function saveGroupInsight($team_id, $start_date, $end_date, $timezone, $group_id, $insight, $expire = WEEK)
    {
        $key = $this->getKeyName(self::KEY_TYPE_GROUP_INSIGHT, $team_id, null, null, null, null, null, null, null,
            $timezone, $start_date, $end_date, $group_id);
        $this->Db->set($key, json_encode($insight));
        return $this->Db->setTimeout($key, $expire);
    }

    /**
     * グループ集計データを返す
     *
     * @param $team_id
     * @param $start_date
     * @param $end_date
     * @param $timezone
     * @param $group_id
     *
     * @return mixed
     */
    function getGroupInsight($team_id, $start_date, $end_date, $timezone, $group_id)
    {
        $insight_str = $this->Db->get($this->getKeyName(self::KEY_TYPE_GROUP_INSIGHT, $team_id,
            null, null, null, null, null, null, null,
            $timezone, $start_date, $end_date, $group_id));
        return json_decode($insight_str, true);

    }

    /**
     * サークル集計データを保存
     *
     * @param $team_id
     * @param $start_date
     * @param $end_date
     * @param $timezone
     * @param $insight
     * @param $expire
     *
     * @return bool
     */
    function saveCircleInsight($team_id, $start_date, $end_date, $timezone, $insight, $expire = WEEK)
    {
        $key = $this->getKeyName(self::KEY_TYPE_CIRCLE_INSIGHT, $team_id, null, null, null, null, null, null, null,
            $timezone, $start_date, $end_date);
        $this->Db->set($key, json_encode($insight));
        return $this->Db->setTimeout($key, $expire);
    }

    /**
     * サークル集計データを返す
     *
     * @param $team_id
     * @param $start_date
     * @param $end_date
     * @param $timezone
     *
     * @return mixed
     */
    function getCircleInsight($team_id, $start_date, $end_date, $timezone)
    {
        $insight_str = $this->Db->get($this->getKeyName(self::KEY_TYPE_CIRCLE_INSIGHT, $team_id,
            null, null, null, null, null, null, null,
            $timezone, $start_date, $end_date));
        return json_decode($insight_str, true);
    }

    /**
     * チームランキングを保存
     *
     * @param $team_id
     * @param $start_date
     * @param $end_date
     * @param $timezone
     * @param $type
     * @param $ranking
     * @param $expire
     *
     * @return bool
     */
    function saveTeamRanking($team_id, $start_date, $end_date, $timezone, $type, $ranking, $expire = WEEK)
    {
        $key = $this->getKeyName(self::KEY_TYPE_TEAM_RANKING, $team_id, null, null, null, null, null, null, null,
            $timezone, $start_date, $end_date);
        $this->Db->hSet($key, $type, json_encode($ranking));
        return $this->Db->setTimeout($key, $expire);
    }

    /**
     * チームランキングを返す
     *
     * @param $team_id
     * @param $start_date
     * @param $end_date
     * @param $timezone
     * @param $type
     *
     * @return mixed
     */
    function getTeamRanking($team_id, $start_date, $end_date, $timezone, $type)
    {
        $ranking_str = $this->Db->hGet($this->getKeyName(self::KEY_TYPE_TEAM_RANKING, $team_id,
            null, null, null, null, null, null, null,
            $timezone, $start_date, $end_date), $type);
        return json_decode($ranking_str, true);

    }

    /**
     * グループランキングを保存
     *
     * @param $team_id
     * @param $start_date
     * @param $end_date
     * @param $timezone
     * @param $group_id
     * @param $type
     * @param $ranking
     * @param $expire
     *
     * @return bool
     */
    function saveGroupRanking($team_id, $start_date, $end_date, $timezone, $group_id, $type, $ranking, $expire = WEEK)
    {
        $key = $this->getKeyName(self::KEY_TYPE_GROUP_RANKING, $team_id, null, null, null, null, null, null, null,
            $timezone, $start_date, $end_date, $group_id);
        $this->Db->hSet($key, $type, json_encode($ranking));
        return $this->Db->setTimeout($key, $expire);

    }

    /**
     * グループランキングを返す
     *
     * @param $team_id
     * @param $start_date
     * @param $end_date
     * @param $timezone
     * @param $group_id
     * @param $type
     *
     * @return mixed
     */
    function getGroupRanking($team_id, $start_date, $end_date, $timezone, $group_id, $type)
    {
        $ranking_str = $this->Db->hGet($this->getKeyName(self::KEY_TYPE_GROUP_RANKING, $team_id,
            null, null, null, null, null, null, null,
            $timezone, $start_date, $end_date, $group_id), $type);
        return json_decode($ranking_str, true);
    }

    /**
     * Save Setup guide complete status
     *
     * @param  $user_id
     * @param  $status
     * @param  $expire
     *
     * @return bool
     */
    function saveSetupGuideStatus($user_id, $status, $expire = SETUP_GUIDE_EXIPIRE_SEC_BY_REDIS)
    {
        $this->Db->set($key = $this->getKeyName(self::KEY_TYPE_SETUP_GUIDE_STATUS, null, $user_id),
            json_encode($status));
        return $this->Db->setTimeout($key, $expire);
    }

    /**
     * Get Setup guide complete status
     *
     * @param  $user_id
     *
     * @return mixed
     */
    function getSetupGuideStatus($user_id)
    {
        $setup_guide_status = $this->Db->get($this->getKeyName(self::KEY_TYPE_SETUP_GUIDE_STATUS, null, $user_id));
        return json_decode($setup_guide_status, true);
    }

    /**
     * Delete setup guide complete status
     *
     * @param  $user_id
     *
     * @return bool
     */
    function deleteSetupGuideStatus($user_id)
    {
        return $this->Db->del($this->getKeyName(self::KEY_TYPE_SETUP_GUIDE_STATUS, null, $user_id));
    }

    /**
     * Save before change term for notification
     *
     * @param int   $teamId
     * @param array $term
     *
     * @return bool
     */
    function saveChangedTerm(int $teamId)
    {
        $expire = WEEK * 2;
        $key = $this->getKeyName(self::KEY_TYPE_CHANGED_TERM, $teamId);
        $this->Db->set($key, true);
        return $this->Db->setTimeout($key, $expire);
    }

    /**
     * Get whether changed term
     *
     * @param  $teamId
     *
     * @return mixed
     */
    function getChangedTerm(int $teamId): bool
    {
        $ret = $this->Db->get($this->getKeyName(self::KEY_TYPE_CHANGED_TERM, $teamId));
        return !empty($ret);
    }

    /**
     * Save master data for campaign price plans
     *
     * @param int   $groupId
     * @param array $plans
     *
     * @return bool
     */
    function saveMstCampaignPlans(int $groupId, array $plans)
    {
        $expire = MONTH;
        $key = $this->getKeyNameForMstCampaignPlans($groupId);
        /** @noinspection PhpUndefinedFunctionInspection */
        $saveData = base64_encode(msgpack_pack($plans));
        $this->Db->set($key, $saveData);
        return $this->Db->setTimeout($key, $expire);
    }

    /**
     * Get key name for campaign price plans
     *
     * @param int $groupId
     *
     * @return bool
     */
    function getKeyNameForMstCampaignPlans(int $groupId)
    {
        $key = self::KEY_TYPE_MST_CAMPAIGN_PLANS . ":" . $groupId;
        return $key;
    }

    /**
     * Get master data for campaign price plans
     *
     * @param  $groupId
     *
     * @return array
     */
    function getMstCampaignPlans(int $groupId): array
    {
        $key = $this->getKeyNameForMstCampaignPlans($groupId);
        $data = $this->Db->get($key);
        /** @noinspection PhpUndefinedFunctionInspection */
        $ret = msgpack_unpack(base64_decode($data));
        if (empty($ret)) {
            return [];
        }
        return $ret;
    }

    /**
     * Delete master data for campaign price plans
     * ※Currently, this method is called only for unit tests
     *
     * @param  $groupId
     *
     * @return bool
     */
    function deleteMstCampaignPlans(int $groupId): bool
    {
        $key = $this->getKeyNameForMstCampaignPlans($groupId);
        return $this->Db->del($key);
    }

    /**
     * @param $type
     * @param $teamId
     * @param $myId
     * @param $body
     * @param $url
     * @param $date
     *
     * @return array List of parameters with error
     */
    private function _validateGlRedisParameters($type, $teamId, $myId, $body, $url, $date): array
    {
        $errorParameters = [];

        //validate parameters
        if (empty($type)) {
            $errorParameters[] = 'type empty';
        }
        if (empty ($teamId)) {
            $errorParameters[] = 'team_id empty';
        } elseif (!ctype_digit(strval($teamId))) {
            $errorParameters[] = 'team_id not int';
        } elseif ($teamId <= 0) {
            $errorParameters[] = 'team_id not positive';
        }
        if (!$this->skipCheckMyId) {
            if (empty($myId)) {
                $errorParameters[] = 'my_id empty';
            } elseif (!ctype_digit(strval($myId))) {
                $errorParameters[] = 'my_id not int';
            } elseif ($myId <= 0) {
                $errorParameters[] = 'myId not positive';
            }
        }
        if (empty($body)) {
            $errorParameters[] = 'body empty';
        }
        if (empty($url)) {
            $errorParameters[] = 'url empty';
        }
        if (empty ($date)) {
            $errorParameters[] = 'date empty';
        } elseif (!is_numeric($date)) {
            $errorParameters[] = 'date not numeric';
        } elseif ($date <= 0) {
            $errorParameters[] = 'date not positive';
        }

        return $errorParameters;
    }

    /**
     * Save mapping between session id and jwt
     *
     * @param int       $teamId
     * @param int       $userId
     * @param string    $sessionId
     * @param string    $jwt
     * @param float|int $expire
     *
     * @return bool
     */
    function saveMapSesAndJwt(int $teamId, int $userId, string $sessionId, $expire = 60 * 24 * 30 * 3): JwtAuthentication
    {
        App::uses('AccessAuthenticator', 'Lib/Auth');
        $jwt = AccessAuthenticator::publish($userId, $teamId)->getJwtAuthentication();
        $key = $this->getKeyMapSesAndJwt($teamId, $userId, $sessionId);
        $this->Db->set($key, $jwt->token());
        $this->Db->setTimeout($key, $expire);
        return $jwt;
    }

    /**
     * Delete mapping between session id and jwt
     *
     * @param int       $teamId
     * @param int       $userId
     * @param string    $sessionId
     */
    function delMapSesAndJwt(int $teamId, int $userId, string $sessionId)
    {
        $key = $this->getKeyMapSesAndJwt($teamId, $userId, $sessionId);
        $this->Db->del($key);
    }

    /**
     * Get jwt from session id
     *
     * @param int    $teamId
     * @param int    $userId
     * @param string $sessionId
     *
     * @return mixed
     */
    function getMapSesAndJwt(int $teamId, int $userId, string $sessionId): string
    {
        $key = $this->getKeyMapSesAndJwt($teamId, $userId, $sessionId);
        return $this->Db->get($key) ?? "";
    }

    function getKeyMapSesAndJwt(int $teamId, int $userId, string $sessionId): string
    {
        $key = $this->getKeyName(self::KEY_TYPE_MAP_SES_AND_JWT, $teamId, $userId);
        $key .= $sessionId;
        return $key;
    }

    /**
     * Set skip checking my_id
     *
     * @param bool $skip
     */
    public function setSkipCheckMyId(bool $skip)
    {
        $this->skipCheckMyId = $skip;
    }


    /**
     * Save member count each circle as multiple
     *
     * @param array $memberCountEachCircle key: circle_id, value: member_count
     *
     * @return void
     */
    function saveMultiCircleMemberCount(array $memberCountEachCircle)
    {
        $expire = 2 * WEEK;

        $keyValueList = [];
        foreach($memberCountEachCircle as $circleId => $memberCount) {
            $key = $this->getKeyNameForCircleMemberCount($circleId);
            $keyValueList[$key] = $memberCount;
        }
        $pipe = $this->Db->multi(Redis::PIPELINE);
        $prefix = $this->Db->config['prefix'];
        foreach ($keyValueList as $k => $v) {
            $k = str_replace($prefix, "", $k);
            $pipe->set($k, $v);
            $pipe->setTimeout($k, $expire);
        }
        $pipe->exec();
    }

    /**
     * Get key name for circle member count
     *
     * @param int $circleId
     *
     * @return bool
     */
    function getKeyNameForCircleMemberCount(int $circleId)
    {
        $key = "circle:${circleId}:member_count";
        return $key;
    }

    /**
     * Get master data for circle member count
     *
     * @param  $circleIds
     *
     * @return array
     */
    function getMultiCircleMemberCount(array $circleIds): array
    {
        $pipe = $this->Db->multi(Redis::PIPELINE);
        $prefix = $this->Db->config['prefix'];
        foreach ($circleIds as $circleId) {
            $k = $this->getKeyNameForCircleMemberCount($circleId);
            $k = str_replace($prefix, "", $k);
            $pipe->get($k);
        }
        $values = $pipe->exec();
        if (empty($values)) {
            return [];
        }

        // Make list key: circle id, value: member count
        $ret = array_combine($circleIds, $values);
        // Delete element if value is empty string
        $ret = array_filter($ret, 'strlen');
        // Convert member count to int
        $ret = array_map('intval', $ret);
        return $ret;
    }

    /**
     * Delete circle member count as multiple
     *
     * @param  $circleId
     *
     * @return bool
     */
    function deleteMultiCircleMemberCount(array $circleIds)
    {
        $pipe = $this->Db->multi(Redis::PIPELINE);
        $prefix = $this->Db->config['prefix'];
        foreach ($circleIds as $circleId) {
            $k = $this->getKeyNameForCircleMemberCount($circleId);
            $k = str_replace($prefix, "", $k);
            $pipe->del($k);
        }
        $pipe->exec();
    }

}
