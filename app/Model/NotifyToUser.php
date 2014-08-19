<?php
App::uses('AppModel', 'Model');

/**
 * NotifyToUser Model
 *
 * @property Notification $Notification
 * @property User         $User
 * @property Team         $Team
 */
class NotifyToUser extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'unread_flg' => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
        'del_flg'    => array(
            'boolean' => array(
                'rule' => array('boolean'),
            ),
        ),
    ];

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = array(
        'Notification',
        'User',
        'Team',
    );

    /**
     * 通知IDと送信ユーザIDのリストを返却
     *
     * @param $notify_ids
     *
     * @return array
     * key = notification_id
     * value = user_id
     */
    function getNotifyIdUserIdList($notify_ids)
    {
        $primary_backup = $this->primaryKey;
        $this->primaryKey = "notification_id";
        $options = [
            'conditions' => [
                'notification_id' => $notify_ids,
            ],
            'fields'     => [
                'user_id'
            ],
        ];
        $res = $this->find('list', $options);
        $this->primaryKey = $primary_backup;
        return $res;
    }
}
