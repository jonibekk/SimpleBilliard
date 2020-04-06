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
     *
     * @return int|false
     */
    public function add($userId, $teamId) {

        if(empty($userId) || empty($teamId)) {
            return false;
        }

        $fields = array('user_id', 'team_id');

        $data = [
            'user_id' => $userId,
            'team_id' => $teamId,
        ];

        $this->create();
        if (!$this->save($data)) {
            return false;
        }

        $newLatestUserConfirmCircleId = $this->getLastInsertID();
        return $newLatestUserConfirmCircleId;
    }

    /**
     * Get a record.
     *
     * @param int $userId
     * @param int $teamId
     *
     * @return mixed|false
     */
    public function getLatestUserConfirmCircle($userId, $teamId) {

        if(empty($userId) || empty($teamId)) {
            return false;
        }

        $LatestUserConfirmCircle = $this->find('first', array(
            'conditions' => array(
                'user_id' => $userId,
                'team_id' => $teamId,
            )
        ));

        if($LatestUserConfirmCircle == null) {
            return false;
        }

        return $LatestUserConfirmCircle;
    }

    /**
     * Delete records by teamid without first circle members.
     *
     * @param int $teamId
     * @param int[] $memberIds
     *
     * @return boolean
     */
    public function deleteByTeamIdWithoutMembers($teamId, $memberIds) {

        if(empty($teamId) || empty($memberIds)) {
            return false;
        }

        $deletedResult = $this->deleteAll(
            array(
                'team_id' => $teamId,
                'user_id !=' => $memberIds
            ),
            false
        );

        return $deletedResult;
    }
}