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
     * get notifications form redis.
     * return value like this.
     * $array = [
     * [
     * 'User'         => [
     * 'id'               => 1,
     * 'display_username' => 'test taro',
     * 'photo_file_name'  => null,
     * ],
     * 'Notification' => [
     * 'title'      => 'test taroさんがあなたの投稿にコメントしました。',
     * 'url'        => 'http://192.168.50.4/post_permanent/1/from_notification:1',
     * 'unread_flg' => false,
     * 'created'    => '1429643033',
     * ]
     * ],
     * [
     * 'User'         => [
     * 'id'               => 2,
     * 'display_username' => 'test jiro',
     * 'photo_file_name'  => null,
     * ],
     * 'Notification' => [
     * 'title'      => 'test jiroさんがあなたの投稿にコメントしました。',
     * 'url'        => 'http://192.168.50.4/post_permanent/2/from_notification:1',
     * 'unread_flg' => false,
     * 'created'    => '1429643033',
     * ]
     * ],
     * ];
     *
     * @param null|int $limit
     * @param null|int $page
     *
     * @return array
     */
    function getNotification($limit = null, $page = 1)
    {
        //$this->Redis->get();
        $data = [
            [
                'User'         => [
                    'id'               => 1,
                    'display_username' => 'test taro',
                    'photo_file_name'  => null,
                ],
                'Notification' => [
                    'title'      => 'test taroさんがあなたの投稿にコメントしました。',
                    'body'       => 'この通知機能マジ最高だね！',
                    'url'        => 'http://192.168.50.4/post_permanent/1/from_notification:1',
                    'unread_flg' => false,
                    'created'    => '1429643033',
                ]
            ],
            [
                'User'         => [
                    'id'               => 2,
                    'display_username' => 'test jiro',
                    'photo_file_name'  => null,
                ],
                'Notification' => [
                    'title'      => 'test jiroさんがあなたの投稿にコメントしました。',
                    'body'       => 'ほんと半端く良いわ！',
                    'url'        => 'http://192.168.50.4/post_permanent/2/from_notification:1',
                    'unread_flg' => false,
                    'created'    => '1429643033',
                ]
            ],
        ];

        return $data;
    }

    /**
     * set notifications
     *
     * @param array|int $to_user_ids
     * @param int       $type
     *
     * @return bool
     */
    function setNotifications($to_user_ids, $type)
    {

        return true;
    }

    /**
     * get count of new notifications from redis.
     *
     * @return int
     */
    function getCountNewNotification()
    {
        return 10;
    }

    /**
     * delete count of new notifications form redis.
     *
     * @return bool
     */
    function resetCountNewNotification()
    {
        return true;
    }

    /**
     * increment count of new notifications from redis.
     *
     * @param int $user_id
     *
     * @return bool
     */
    function incCountNewNotification($user_id)
    {
        return true;
    }

    /**
     * change read status of notification.
     *
     * @param int $id
     *
     * @return bool
     */
    function changeReadStatusNotification($id)
    {
        return true;

    }

}
