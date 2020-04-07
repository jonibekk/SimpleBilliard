<?php
App::uses('AppModel', 'Model');
App::uses('User', 'Model');
App::uses('Team', 'Model');

/**
 * LatestUserConfirmCircle Model
 *
 * @property User $User
 * @property Team $Team
 */
class LatestUserConfirmCircle extends AppModel {

    public $actsAs = [
        'SoftDeletable' => [
            'delete' => false,
        ],
    ];

    /**
    * Validation rules
    *
    * @var array
    */
    public $validate = [
        'del_flg' => [
            'boolean' => ['rule' => ['boolean'],],
        ],
    ];

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = array(
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'user_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'Team' => array(
            'className' => 'Team',
            'foreignKey' => 'team_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );

    /**
     * Insert new record.
     *
     * @param int $userId
     * @param int $teamId
     * @param int $latestConfirmCircleId
     *
     * @return int|false
     */
    public function add($userId, $teamId, $latestConfirmCircleId) {

        if(empty($userId) || empty($teamId) || empty($latestConfirmCircleId)) {
            return false;
        }

        $fields = array('user_id', 'team_id', 'latest_confirm_circle_id');

        $data = [
            'user_id' => $userId,
            'team_id' => $teamId,
            'latest_confirm_circle_id' => $latestConfirmCircleId,
        ];

        $this->create();
        if (!$this->save($data)) {
            return false;
        }

        $newLatestUserConfirmCircleId = $this->getLastInsertID();
        return $newLatestUserConfirmCircleId;
    }

    /**
     * Update a record.
     *
     * @param int $userId
     * @param int $teamId
     * @param int $latestConfirmCircleId
     *
     * @return boolean
     */
    public function update($userId, $teamId, $latestConfirmCircleId) {

        if(empty($userId) || empty($teamId) || empty($latestConfirmCircleId)) {
            return false;
        }

        $fields = array('user_id', 'team_id', 'latest_confirm_circle_id');

        $LatestUserConfirmCircle = $this->find('first', array(
            'conditions' => array(
                'user_id' => $userId,
                'team_id' => $teamId,
            ),
        ));

        if($LatestUserConfirmCircle == null) {
            return false;
        }

        $this->id = $LatestUserConfirmCircle["LatestUserConfirmCircle"]["id"];

        $result = $this->saveField('latest_confirm_circle_id', $latestConfirmCircleId);

        if (!$result) {
            return false;
        }

        return true;
    }

    /**
     * Get a circle id that latest user confirmed.
     *
     * @param int $userId
     * @param int $teamId
     *
     * @return mixed|false
     */
    public function getLatestUserConfirmCircleId($userId, $teamId) {

        if(empty($userId) || empty($teamId)) {
            return false;
        }

        $res = $this->find('first', array(
            'conditions' => array(
                'user_id' => $userId,
                'team_id' => $teamId,
            ),
            'fields' => array(
                'latest_confirm_circle_id'
            )
        ));

        if($res == null) {
            return false;
        }

        return $res["LatestUserConfirmCircle"]["latest_confirm_circle_id"];
    }
}