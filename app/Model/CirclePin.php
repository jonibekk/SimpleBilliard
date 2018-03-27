<?php
App::uses('AppModel', 'Model');
App::uses('Circle', 'Model');

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
            'isString'      => ['rule' => ['isString']],
            'maxLength'     => ['rule' => ['maxLength', 4294967295]],
            'csv_format'    => ['rule' => ['customValidateIsCsvFormat']],
            'circle_exists'  => ['rule' => ['customValidateIsCircleExists']],
            'is_belong'     => ['rule' => ['customValidateIsBelong']],
        ],
    ];

    /**
     * Is Csv Format
     *
     * @param array $val
     *
     * @return bool
     */
    function customValidateIsCsvFormat(array $val): bool
    {
        if(!preg_match("/^'\d+(?:,\d+)*'$/", $val['circle_orders'])){
            GoalousLog::error("customValidateIsCsvFormat", $val);
            return false;
        }

        return true;
    }

    /**
     * Is Circle Exists
     *
     * @param array $val
     *
     * @return bool
     */
    function customValidateIsCircleExists(array $val): bool
    {
        $circleIds = explode(',', substr($val['circle_orders'],1,-1));
        /** @var Circle $Circle */
        $Circle = ClassRegistry::init("Circle");

        foreach ($circleIds as $key => $circleId) {
            $exists = $Circle->getById($circleId);
            if(empty($exists)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Is Belong
     *
     * @param array $val
     *
     * @return bool
     */
    function customValidateIsBelong(array $val): bool
    {
        $circleIds = explode(',', substr($val['circle_orders'],1,-1));
        /** @var CircleMember $CircleMember */
        $CircleMember = ClassRegistry::init("CircleMember");

        foreach ($circleIds as $key => $circleId) {
            $belongs = $CircleMember->isBelong($circleId);
            if(!$belongs) {
                GoalousLog::error("customValidateIsBelong", [false]);
                return false;
            }
        }

        return true;
    }

    /**
     * @param int $userId
     * @param int $teamId
     *
     * @return array|null
     */
    public function getUnique(int $userId, int $teamId)
    {
        $options = [
            'CirclePin.user_id' => $userId,
            'CirclePin.team_id' => $teamId,
            'CirclePin.del_flg' => false,
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
                // $options['id'] = $row['id'];
                if(!$this->save($row, $options)) {
                    debug($this->validationErrors); die();
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

        try {    
            $row = $this->getUnique($userId, $teamId);

            if(empty($row)){
                return true;
            }

            $orders = ',' . $row['circle_orders'] . ',';
            $find = ',' . $circleId . ',';

            if(strpos($orders, $find) !== false){
                $orders = str_replace($find, ',', $orders);
                $row['circle_orders'] = $this->getDataSource()->value(substr($orders, 1, -1), 'string');
                $options['id'] = $row['id'];
                $this->begin();

                if(!$this->save($row, $options)) {
                    throw new Exception("Error Processing update Request", 1);             
                }
                $this->commit(); 
            }       
        } catch (Exception $e) {
            $this->log(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            $this->log($e->getTraceAsString());
            $this->rollback();
            return false;
        }

        return true;
    }

    /**
     * Get Circles
     *
     * @param $userId
     * @param $teamId
     *
     * @return array
     */
    public function getJoinedCircleData(int $userId, int $teamId): array
    {
        $options = [
            'joins'      => [
                [
                    'table' => 'circle_members',
                    'alias' => 'CircleMember',
                    'type' => 'LEFT',
                    'foreignKey' => false,
                    'conditions'=> [
                        'CircleMember.circle_id = Circle.id',
                    ]
                ],
            ],
            'conditions' => [
                'CircleMember.team_id' => $teamId,
                'CircleMember.user_id' => $userId,
                'Circle.del_flg'    => false,
            ],
            'order'      => [
                'Circle.modified' => 'DESC',
            ],
            'fields'    => [
                'Circle.id',
                'Circle.name',
                'Circle.photo_file_name',
                'Circle.public_flg',
                'Circle.team_all_flg',
                'CircleMember.admin_flg',
                'CircleMember.unread_count',
            ]
        ];
        return ClassRegistry::init('Circle')->find('all', $options);
    }

    /**
     * Get Circle Pin Order Information
     *
     * @param $userId
     * @param $teamId
     *
     * @return string
     */
    public function getPinData(int $userId, int $teamId): string {
        $options = [
            'user_id' => $userId,
            'team_id' => $teamId,
        ];

        try {
            $row = $this->getUnique($userId, $teamId);
            if(!empty($row)){
                return substr($row['circle_orders'],1,-1);
            }      
        } catch (Exception $e) {    
            GoalousLog::error("[CirclePin]:",[$e->getMessage(),$e->getTraceAsString()]);
            $this->log(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            $this->log($e->getTraceAsString());
        }

        return "";
    }
}
