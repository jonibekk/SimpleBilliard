<?php
App::uses('AppModel', 'Model');

/**
 * CirclePin Model
 */
class CirclePin extends AppModel
{
    function __construct($id = false, $table = null, $ds = null)
    {
        parent::__construct($id, $table, $ds);
        //$this->_setPublicTypeName();
    }

    /**
     * Add Circle Pin
     *
     * @param $userId
     * @param $teamId
     * @param $circleId
     * @param $pinOrder
     *
     * @return bool|mixed
     */
    function add($userId, $circleId, $pinOrder) : bool
    {
        $data = [
            'user_id' => $userId,
            'team_id' => $this->current_team_id,
            'circle_id' => $circleId,
            'pin_order' => $pinOrder,
        ];
        return $this->save($data);
    }

    public function deleteAllUserPins(int $userId) : bool
    {
        $conditions = [
            'CirclePin.user_id' => $userId,
        ];
        return $this-->deleteAll($conditions);
    }

    function getPinnedCircles()
    {
        $CircleMember = ClassRegistry::init('CircleMember');
        $my_circle_list = $CircleMember->getMyCircleList();

        $options = [
            'conditions' => [
                'circle_id'        => $my_circle_list,
            ],
            'fields'     => ['circle_id', 'pin_order'],
            'order'      => ['pin_order ASC'],
        ];

        $results = $this->find('all', $options);
        return $results;
    }
}
