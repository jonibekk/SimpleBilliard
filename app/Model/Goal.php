<?php
App::uses('AppModel', 'Model');

/**
 * Goal Model
 *
 * @property User         $User
 * @property Team         $Team
 * @property GoalCategory $GoalCategory
 * @property Post         $Post
 */
class Goal extends AppModel
{
    /**
     * ステータス
     */
    const STATUS_DOING = 0;
    const STATUS_PAUSE = 1;
    const STATUS_COMPLETE = 2;
    static public $STATUS = [self::STATUS_DOING => "", self::STATUS_PAUSE => "", self::STATUS_COMPLETE => ""];

    /**
     * ステータスの名前をセット
     */
    private function _setStatusName()
    {
        self::$STATUS[self::STATUS_DOING] = __d('gl', "進行中");
        self::$STATUS[self::STATUS_PAUSE] = __d('gl', "中断");
        self::$STATUS[self::STATUS_COMPLETE] = __d('gl', "完了");
    }

    /**
     * Display field
     *
     * @var string
     */
    public $displayField = 'goal';

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'valued_flg'   => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
        'evaluate_flg' => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
        'status'       => [
            'numeric' => [
                'rule' => ['numeric'],
            ],
        ],
        'priority'     => [
            'numeric' => [
                'rule' => ['numeric'],
            ],
        ],
        'del_flg'      => [
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
        'User',
        'Team',
        'GoalCategory',
    ];

    /**
     * hasMany associations
     *
     * @var array
     */
    public $hasMany = [
        'Post'
    ];

    function __construct($id = false, $table = null, $ds = null)
    {
        parent::__construct($id, $table, $ds);
        $this->_setStatusName();
    }
}
