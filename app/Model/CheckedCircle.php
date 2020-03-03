<?php
App::uses('AppModel', 'Model');
App::uses('Circle', 'Model');
App::uses('User', 'Model');
App::uses('Team', 'Model');

/**
 * CheckedCircle Model
 *
 * @property Circle $Circle
 * @property User $User
 * @property Team $Team
 */
class CheckedCircle extends AppModel {

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
        'Circle' => array(
            'className' => 'Circle',
            'foreignKey' => 'circle_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
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
     * @param int $circleId
     *
     * @return int|false
     */
    public function add($userId, $teamId, $circleId) {

        $fields = array('user_id', 'team_id', 'circle_id');

        $data = [
            'user_id' => $userId,
            'team_id' => $teamId,
            'circle_id' => $circleId
        ];

        $this->create();
        if (!$this->save($data)) {
            return false;
        }

        $newCheckedCircleId = $this->getLastInsertID();
        return $newCheckedCircleId;
    }

    /**
     * Get a record.
     *
     * @param int $userId
     * @param int $teamId
     * @param int $circleId
     *
     * @return mixed
     */
    public function getCheckedCircle($userId, $teamId, $circleId) {

        $checkedCircle = $this->find('first', array(
            'conditions' => array(
                'user_id' => $userId,
                'team_id' => $teamId,
                'circle_id' => $circleId,
            )
        ));

        if($checkedCircle == null) {
            return false;
        }

        return $checkedCircle;
    }

    /**
     * Check exist unchecked new circle.
     * You can use for the one circle.
     *
     * @param int $userId
     * @param int $teamId
     * @param array $circleIds
     *
     * @return boolean
     */
    public function isExistUncheckedCircle($userId, $teamId, $circleIds) {

        // search circle_ids by user_id & team_id
        $checkedCircles = $this->find('list', array(
            'conditions' => array(
                'user_id' => $userId,
                'team_id' => $teamId,
            ),
            'fields' => array(
                'circle_id'
            )
        ));

        // sort array's key
        $checkedCircles = array_values($checkedCircles);
        $circleIds = array_values($circleIds);

        if($circleIds !== $checkedCircles) {
            return true;
        }

        return false;
    }


    /**
     * get unchecked circleIds.
     *
     * @param int $userId
     * @param int $teamId
     *
     * @return array
     */
    public function getCheckedCircleIds($userId, $teamId) {

        // search circle_ids by user_id & team_id
        $checkedCircles = $this->find('all', array(
            'conditions' => array(
                'user_id' => $userId,
                'team_id' => $teamId,
            ),
            'fields' => array(
                'circle_id'
            )
        ));

        return $checkedCircles;
    }
}
