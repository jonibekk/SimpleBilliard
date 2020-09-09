<?php
App::uses('AppModel', 'Model');

/**
 * Device Model
 *
 * @property User $User
 */
class Subscription extends AppModel
{
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
     * @param $userId, $subscription
     *
     * @return bool|mixed
     */
    function add($userId, $subscription)
    {
        $data = array(
            'user_id' => $userId,
            'subscription' => json_encode($subscription),
            'subscription_hash' => hash('sha256', json_encode($subscription)),
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
     * get subscription by user id
     *
     * @param $userId
     *
     * @return array
     */
    function getSubscriptionByUserId($userId)
    {
        if (empty($userId)) {
            return false;
        }

        $options = [
            'conditions' => [
                'Subscription.user_id' => $userId,
                'Subscription.del_flg' => false,
            ],
        ];

        $data = $this->find('all', $options);
        return $data;
    }



    /**
     * delete user subscription
     *
     * @param $userId, $subscription
     *
     * @return bool
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
     * @param userId, subscription
     *
     * @return array|bool
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

    /**
     * check if user owns subscription
     *
     * @param $userId, subscription
     *
     * @return bool
     */
    function checkSubscription(int $userId, $subscription)
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

        $res =  $this->find('all', $options['conditions']);
        return count($res) > 0;
        
    }

}
