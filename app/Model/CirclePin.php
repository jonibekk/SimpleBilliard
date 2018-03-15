<?php
App::uses('AppModel', 'Model');

/**
 * CirclePin Model
 */
class CirclePin extends AppModel
{
    public function __construct($id = false, $table = null, $ds = null)
    {
        parent::__construct($id, $table, $ds);
    }

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'circle_orders'         => [
            'isString'  => [
                'rule' => ['isString',],
            ],
            'maxLength' => ['rule' => ['maxLength', 4294967295]],
        ],
    ];

    /**
     * @param int $userId
     * @param int $teamId
     *
     * @return array|null
     */
    public function getUnique(int $userId, int $teamId)
    {
        $options = [
            'conditions' => [
                'CirclePin.user_id' => $userId,
                'CirclePin.team_id' => $teamId,
                'CirclePin.del_flgl' => false,
            ],
        ];
        $res = $this->find('first', $options);

        if (empty($res)) {
            return [];
        }
        return Hash::get($res, 'CirclePin');
    }

    /**
     * Save Circle Pin Order Information
     *
     * @param $userId
     * @param $teamId
     * @param $pinOrders
     *
     * @return bool|mixed
     */
    public function insertUpdate(int $userId, int $teamId, string $pinOrders): bool {
        $db = $this->getDataSource();

        $options = [
            'user_id' => $userId,
            'team_id' => $teamId,
        ];

        $data = [
            'user_id' => $userId,
            'team_id' => $teamId,
            'circle_orders' => $db->value($pinOrders, 'string'),
            'del_flg' => false,
        ];

        try {
            $this->begin();
            $row = $this->getUnique($userId, $teamId);
            if(empty($row)){
                $this->create($data);
                $this->user_id = $userId;
                $this->team_id = $teamId;
                $this->circle_orders = $db->value($pinOrders, 'string');
                if(!$this->save($data)){
                    GoalousLog::error("[CirclePin]: Insert Failure", $data);
                    throw new Exception("Error Processing save Request", 1);
                }
                
            } else {
                $row['circle_orders'] = $db->value($pinOrders, 'string');
                if(!$this->updateAll($row, $options)) {
                    GoalousLog::error("[CirclePin]: Update Failure", $row);
                    throw new Exception("Error Processing update Request", 1);             
                }
            }
            $this->commit();         
        } catch (Exception $e) {    
            GoalousLog::error("[CirclePin]:",[$e->getMessage(),$e->getTraceAsString()]);
            $this->log(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            $this->log($e->getTraceAsString());
            $this->rollback();
            return false;
        }
        
        return true;
    }
    
    /**
     * Deletes specified circleId from circle pin order information
     * example: 3,4,5 => ,3,4,5, => (,4,) => ,3,5, => 3,5 
     * @param $userId
     * @param $teamId
     * @param $circleId
     *
     * @return bool
     */
    public function deleteId(int $userId, int $teamId, string $circleId): bool 
    {
        $options = [
            'user_id' => $userId,
            'team_id' => $teamId,
        ];

        $data = [
            'user_id' => $userId,
            'team_id' => $teamId,
            'circle_orders' => '',
            'del_flg' => false,
        ];

        try {    
            $row = $this->getUnique($userId, $teamId);
            if(empty($row)) {
                return true;
            }
                
            $orders = ',' . $row['circle_orders'] . ',';
            $find = ',' . $circleId . ',';
            if(strpos($orders, $find) !== false){
                $orders = str_replace($find, ',', $orders);
                $data['circle_orders'] = $this->getDataSource()->value(substr($orders, 1, -1), 'string');

                $this->begin();

                if(!$this->updateAll($row, $options)) {
                    GoalousLog::error("[CirclePin]: Update Failure", $row);
                    throw new Exception("Error Processing update Request", 1);             
                }

                $this->commit(); 
            }       
        } catch (Exception $e) {    
            GoalousLog::error("[CirclePin]:",[$e->getMessage(),$e->getTraceAsString()]);
            $this->log(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            $this->log($e->getTraceAsString());
            $this->rollback();
            return false;
        }

        return true;
    }
}
