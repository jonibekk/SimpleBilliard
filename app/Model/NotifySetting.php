<?php
App::uses('AppModel', 'Model');

/**
 * NotifySetting Model
 *
 * @property User $User
 */
class NotifySetting extends AppModel
{
    /**
     * 通知タイプ
     */
    const TYPE_FEED_APP = "feed_app_flg";
    const TYPE_FEED_MAIL = "feed_email_flg";
    const TYPE_CIRCLE_APP = "circle_app_flg";
    const TYPE_CIRCLE_EMAIL = "circle_email_flg";

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'feed_app_flg'     => [
            'boolean' => [
                'rule'       => ['boolean'],
                'allowEmpty' => true,
            ],
        ],
        'feed_email_flg'   => [
            'boolean' => [
                'rule'       => ['boolean'],
                'allowEmpty' => true,
            ],
        ],
        'circle_app_flg'   => [
            'boolean' => [
                'rule'       => ['boolean'],
                'allowEmpty' => true,
            ],
        ],
        'circle_email_flg' => [
            'boolean' => [
                'rule'       => ['boolean'],
                'allowEmpty' => true,
            ],
        ],
        'del_flg'          => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
    ];

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [
        'User'
    ];

    function isOnNotify($user_id, $type)
    {
        $options = array(
            'conditions' => array(
                'user_id' => $user_id,
            )
        );
        $result = $this->find('first', $options);
        if (empty($result)) {
            return true;
        }
        if ($result['NotifySetting'][$type]) {
            return true;
        }
        else {
            return false;
        }
    }

}
