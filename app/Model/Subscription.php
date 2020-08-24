<?php
App::uses('AppModel', 'Model');

/**
 * Device Model
 *
 * @property User $User
 */
class Subscription extends AppModel
{
    const BROWSER_TYPE_CHROME = 1;
    const BROWSER_TYPE_FIREFOX = 2;
    const BROWSER_TYPE_EDGE = 3;
    const BROWSER_TYPE_OTHER = 99;

    public $useTable = "subscriptions";
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
        'subscription'    => [
            'notBlank' => [
                'rule' => ['notBlank'],
            ],
        ],
        'subscription_hash'    => [
            'notBlank' => [
                'rule' => ['notBlank'],
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
     * add subscription
     *
     * @param $data
     *
     * @return bool|mixed
     */
    function add($user_id, $subscription)
    {

        $data = array(
            'user_id' => $user_id,
            'subscription' => json_encode($subscription),
            'subscription_hash' => hash('sha256', json_encode($subscription)),
        );
        GoalousLog::error(print_r($data, true));
        $this->set($data);
        if (!$this->validates()) {
            return false;
        }
        $this->create();
        $res = $this->save($data);
        GoalousLog::error(print_r($res, true));
        return $res;
    }

    /**
     * get subscription by user_id
     *
     * @param $user_id
     *
     * @return array|bool|null
     */
    function getSubscriptionByUserId($user_id)
    {
        if (empty($user_id)) {
            return false;
        }

        $options = [
            'conditions' => [
                'Subscription.user_id' => $user_id,
                'Subscription.del_flg' => false,
            ],
        ];

        $data = $this->find('all', $options);
        return $data;
    }



    /**
     * delete user subscription
     *
     * @param string $installationId
     *
     * @return array|bool|null
     */
    function deleteSubscription(int $userId, $subscription)
    {
        if (empty($userId) or empty($subscription)) {
            return false;
        }

        $options = [
            'conditions' => [
                'user_id' => $userId,
                'subscription_hash' => hash('sha256', json_encode($subscription)),
            ],
        ];

        
        return $this->deleteAll($options['conditions']);
    }

    /**
     * update subscription
     *
     * @param string $installationId
     *
     * @return array|bool|null
     */
    function updateSubscription(int $userId, $subscription)
    {
        if (empty($userId) or empty($subscription)) {
            return false;
        }

        $options = [
            'conditions' => [
                'Subscription.subscription_hash' => hash('sha256', json_encode($subscription)),
                'Subscription.del_flg'      => false,
            ],
        ];

        $data = $this->find('first', $options);
        if (!$data) {
            return $this->add($userId, $subscription);
        }
        if ($data['Subscription']['user_id'] == $userId) {
            return true;
        }
        $data['Subscription']['user_id'] = $userId;
        
        return $this->save($data);
    }

}
