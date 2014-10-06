<?php
App::uses('AppModel', 'Model');

/**
 * KeyResult Model
 *
 * @property Team              $Team
 * @property Goal              $Goal
 * @property Follower          $Follower
 * @property KeyResultUser     $KeyResultUser
 */
class KeyResult extends AppModel
{
    /**
     * 目標値の単位
     */
    const UNIT_PERCENT = 0;
    const UNIT_NUMBER = 1;
    const UNIT_BINARY = 2;
    const UNIT_YEN = 3;
    const UNIT_DOLLAR = 4;

    static public $UNIT = [
        self::UNIT_PERCENT => "",
        self::UNIT_NUMBER  => "",
        self::UNIT_BINARY  => "",
        self::UNIT_YEN     => "",
        self::UNIT_DOLLAR  => "",
    ];

    /**
     * 目標値の単位の表示名をセット
     */
    private function _setUnitName()
    {
        self::$UNIT[self::UNIT_PERCENT] = __d('gl', "%");
        self::$UNIT[self::UNIT_NUMBER] = __d('gl', "数値");
        self::$UNIT[self::UNIT_BINARY] = __d('gl', 'ON/OFF');
        self::$UNIT[self::UNIT_YEN] = __d('gl', '¥');
        self::$UNIT[self::UNIT_DOLLAR] = __d('gl', '$');
    }

    public $priority_list = [
        1 => 1,
        2 => 2,
        3 => 3,
        4 => 4,
        5 => 5,
    ];

    /**
     * Display field
     *
     * @var string
     */
    public $displayField = 'name';

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'name'       => [
            'notEmpty' => [
                'rule' => 'notEmpty',
            ],
        ],
        'valued_flg' => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
        'special_flg' => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
        'del_flg'     => [
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
        'Team',
        'Goal',
    ];

    public $hasMany = [
        'KeyResultUser' => [
            'dependent' => true,
        ],
        'Leader'        => [
            'className' => 'KeyResultUser',
        ],
        'Collaborator'  => [
            'className' => 'KeyResultUser',
        ],
        'MyCollabo'     => [
            'className' => 'KeyResultUser',
        ],
        'Follower',
        'MyFollow'      => [
            'className' => 'Follower',
        ],
    ];

    function __construct($id = false, $table = null, $ds = null)
    {
        parent::__construct($id, $table, $ds);
        $this->_setUnitName();
    }

    function getCollaboGoalList($user_id)
    {
        $key_result_ids = $this->KeyResultUser->getCollaboKeyResultList($user_id);
        $options = [
            'conditions' => [
                'id' => $key_result_ids,
            ],
            'fields'     => [
                'goal_id'
            ],
        ];
        $res = $this->find('list', $options);
        return $res;
    }

}
