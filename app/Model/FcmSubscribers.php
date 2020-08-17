<?php
App::uses('AppModel', 'Model');

/**
 * Device Model
 *
 * @property User $User
 */
class FcmSubscriber extends AppModel
{
    const BROWSER_TYPE_CHROME = 1;
    const BROWSER_TYPE_FIREFOX = 2;
    const BROWSER_TYPE_EDGE = 3;
    const BROWSER_TYPE_OTHER = 99;

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'user_id'         => [
            'notBlank' => [
                'rule' => ['notBlank'],
            ],
            'notZero'  => [
                'rule' => ['naturalNumber'],
            ],
        ],
        'subscriber'    => [
            'notBlank' => [
                'rule' => ['notBlank'],
            ],
        ],
        'browser_type'         => [
            'numeric' => [
                'rule' => ['numeric'],
            ],
        ],
        'del_flg'         => [
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
        'User',
    ];

    /**
     * add subscriber
     *
     * @param $data
     *
     * @return bool|mixed
     */
    function add($user_id, $subscriber, $version = 0, $browser_type = 99)
    {
        if (!isset($data['Device']) || empty($data['Device'])) {
            return false;
        }

        $data = array(
            'user_id' => $user_id,
            'subscriber' => json_encode($subscriber),
            'subscriber_hash' => hash('sha256', json_encode($subscriber)),
            'version' => $version,
            'browser_type' => $browser_type,

        );
        $this->set($data);
        if (!$this->validates()) {
            return false;
        }
        $this->create();
        $res = $this->save($data);
        return $res;
    }

    /**
     * get subscriber by user_id
     *
     * @param $user_id
     *
     * @return array|bool|null
     */
    function getSubscriberByUserId($user_id)
    {
        if (empty($user_id)) {
            return false;
        }

        $options = [
            'conditions' => [
                'FcmSubscriber.user_id' => $user_id,
                'FcmSubscriber.del_flg' => false,
            ],
        ];

        $data = $this->find('all', $options);
        return $data;
    }



    /**
     * delete user subscriber
     *
     * @param string $installationId
     *
     * @return array|bool|null
     */
    function deleteSubscirberByUserId(int $userId, $subscriber)
    {
        if (empty($userId) or empty($subscriber)) {
            return false;
        }

        $options = [
            'conditions' => [
                'FcmSubscriber.user_id' => $userId,
                'FcmSubscriber.subscriber_hash' => hash('sha256', json_encode($subscriber)),
                'FcmSubscriber.del_flg'      => false,
            ],
        ];

        $data = $this->find('first', $options);

        $data['FcmSubscriber']['del_flg'] = true;
        $data['FcmSubscriber']['deleted'] = GoalousDateTime::now()->getTimestamp();
        
        return $this->save($data, false);
    }


}
