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
		// 渡されたcircleIDについてcheck済みにする

		$fields = array('user_id', 'team_id', 'circle_id');

		$this->create();

		$data = [
			'id' => 2,
			'user_id' => $userId,
			'team_id' => $teamId,
			'circle_id' => $circleId
		];

		$this->set(array(
			'user_id' => $userId,
			'team_id' => $teamId,
			'circle_id' => $circleId
		));

        // if (!$this->save($data, true, $fields)) {
        if (!$this->save()) {

            return false;
        }
		// print_r($this->getDataSource()->getLog());
		GoalousLog::info('SQL', $this->getDataSource()->getLog());
		// GoalousLog::info('SQL', array_pop($this->getDataSource()->getLog()['log']));

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
		// 渡されたuser_id, team_id, circle_idのレコードがあれば返す

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

		//user_idとteam_idで検索

		// 渡されたcirlceIDと比較して、1件でもレコードがなければuncheckedサークルがあるのでtrue、全てあればfalse

		return true;
	}
}
