<?php
App::uses('AppModel', 'Model');
App::uses('User', 'Model');
App::uses('Team', 'Model');

/**
 * CircleDiscoverTabOpenedUser Model
 *
 * @property User $User
 * @property Team $Team
 */
class CircleDiscoverTabOpenedUser extends AppModel {

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

        $newCircleDiscoverTabOpenedUserId = $this->getLastInsertID();
        return $newCircleDiscoverTabOpenedUserId;
    }

    /**
     * Get a record.
     *
     * @param int $userId
     * @param int $teamId
     *
     * @return mixed|false
     */
    public function getCircleDiscoverTabOpenedUser($userId, $teamId) {

        if(empty($userId) || empty($teamId)) {
            return false;
        }

        $CircleDiscoverTabOpenedUser = $this->find('first', array(
            'conditions' => array(
                'user_id' => $userId,
                'team_id' => $teamId,
            )
        ));

        if($CircleDiscoverTabOpenedUser == null) {
            return false;
        }

        return $CircleDiscoverTabOpenedUser;
    }

    /**
     * Delete records by teamid.
     *
     * @param int $teamId
     *
     * @return boolean
     */
    public function deleteByTeamId($teamId) {

        if(empty($teamId)) {
            return false;
        }

        $deletedResult = $this->deleteAll(
            array(
                'team_id' => $teamId,
            ),
            false
        );

        return $deletedResult;
    }
}