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
        if($val['circle_orders'] === "") {
            return true;
        } 
        if(!preg_match("/^\d+(?:,\d+)*$/", $val['circle_orders'])){
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
        if($val['circle_orders'] === "") {
            return true;
        }
        $circleIds = explode(',', $val['circle_orders']);
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
        if($val['circle_orders'] === "") {
            return true;
        } 
        $circleIds = explode(',', $val['circle_orders']);
        /** @var CircleMember $CircleMember */
        $CircleMember = ClassRegistry::init("CircleMember");

        foreach ($circleIds as $key => $circleId) {
            $belongs = $CircleMember->isBelong($circleId);
            if(!$belongs) {
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
            'conditions' => [
                'CirclePin.user_id' => $userId,
                'CirclePin.team_id' => $teamId,
                'CirclePin.del_flg' => false,
            ],
            'fields'    => [
                'CirclePin.id',
                'CirclePin.user_id',
                'CirclePin.team_id',
                'CirclePin.circle_orders',
            ],
        ];

        $res = $this->find('first', $options);

        if (empty($res)) {
            return [];
        }
        return Hash::get($res, 'CirclePin');
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
                'Circle.modified',
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
                $returnValue = $row['circle_orders'] == null ? "" : $row['circle_orders'];
                return $returnValue;
            }      
        } catch (Exception $e) {    
            GoalousLog::error("[CirclePin]:",[$e->getMessage(),$e->getTraceAsString()]);
        }

        return "";
    }
}
