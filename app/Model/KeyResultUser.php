<?php
App::uses('AppModel', 'Model');

/**
 * KeyResultUser Model
 *
 * @property Team      $Team
 * @property KeyResult $KeyResult
 * @property User      $User
 */
class KeyResultUser extends AppModel
{
    /**
     * タイプ
     */
    const TYPE_COLLABORATOR = 0;
    const TYPE_OWNER = 1;

    static public $TYPE = [
        self::TYPE_COLLABORATOR => "",
        self::TYPE_OWNER        => "",
    ];

    /**
     * タイプの表示名をセット
     */
    private function _setTypeName()
    {
        self::$TYPE[self::TYPE_COLLABORATOR] = __d('gl', "コラボレータ");
        self::$TYPE[self::TYPE_OWNER] = __d('gl', "オーナ");
    }

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'type'    => [
            'numeric' => [
                'rule' => ['numeric'],
            ],
        ],
        'del_flg' => [
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
        'KeyResult',
        'User',
    ];

    function __construct($id = false, $table = null, $ds = null)
    {
        parent::__construct($id, $table, $ds);
        $this->_setTypeName();
    }

    function add($kr_id, $uid = null, $type = self::TYPE_COLLABORATOR)
    {
        if (!$uid) {
            $uid = $this->my_uid;
        }
        $skr_user = [
            'team_id'       => $this->current_team_id,
            'user_id'       => $uid,
            'type'          => $type,
            'key_result_id' => $kr_id,
        ];
        $res = $this->save($skr_user);
        return $res;
    }

}
