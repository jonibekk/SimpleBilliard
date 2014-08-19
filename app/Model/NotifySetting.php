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
     * 通知設定タイプ
     */
    const TYPE_FEED = "feed";
    const TYPE_CIRCLE = "circle";

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

    /**
     * 指定タイプのアプリ、メールの通知設定を返却
     * ユーザ指定は単数、複数の両方対応
     * 返却値は[uid=>['app'=>true,'email'=>true],,,,]
     *
     * @param $user_ids
     * @param $type
     *
     * @return array
     */
    function getAppEmailNotifySetting($user_ids, $type)
    {
        if (!is_array($user_ids)) {
            $user_ids = [$user_ids];
        }
        $default_data = [
            'app'   => true,
            'email' => true,
        ];
        $options = array(
            'conditions' => array(
                'user_id' => $user_ids,
                'NOT' => ['user_id' => $this->me['id']]
            )
        );
        $result = $this->find('all', $options);
        $res_data = [];
        if (!empty($result)) {
            foreach ($result as $val) {
                $res_data[$val['NotifySetting']['user_id']] = $default_data;
                if (!$val['NotifySetting'][$type . '_app_flg']) {
                    //アプリがoff
                    $res_data[$val['NotifySetting']['user_id']]['app'] = false;
                }
                if (!$val['NotifySetting'][$type . '_email_flg']) {
                    //メールがoff
                    $res_data[$val['NotifySetting']['user_id']]['email'] = false;
                }
                //引数のユーザリストから除去
                if (($array_key = array_search($val['NotifySetting']['user_id'], $user_ids)) !== false) {
                    unset($user_ids[$array_key]);
                }
            }
        }
        //設定なしユーザはデフォルトを適用
        if (!empty($user_ids)) {
            foreach ($user_ids as $uid) {
                $res_data[$uid] = $default_data;
            }
        }
        return $res_data;
    }

}
