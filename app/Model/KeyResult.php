<?php
App::uses('AppModel', 'Model');

/**
 * KeyResult Model
 *
 * @property Team          $Team
 * @property Goal          $Goal
 * @property KeyResultUser $KeyResultUser
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
        'name'        => [
            'notEmpty' => [
                'rule' => ['notEmpty'],
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
        'KeyResultUser'
    ];

    function __construct($id = false, $table = null, $ds = null)
    {
        parent::__construct($id, $table, $ds);
        $this->_setUnitName();
    }

}
