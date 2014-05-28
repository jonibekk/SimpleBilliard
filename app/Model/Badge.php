<?php
App::uses('AppModel', 'Model');

/**
 * Badge Model
 *
 * @property User  $User
 * @property Team  $Team
 * @property Image $Image
 */
class Badge extends AppModel
{
    /**
     * タイプ
     */
    const TYPE_PRAISE = 1;
    const TYPE_SKILL = 2;
    static public $TYPE = [null => "", self::TYPE_PRAISE => "", self::TYPE_SKILL => ""];

    /**
     * タイプの名前をセット
     */
    private function _setTypeName()
    {
        self::$TYPE[null] = __d('gl', "選択してください");
        self::$TYPE[self::TYPE_PRAISE] = __d('gl', "賞賛");
        self::$TYPE[self::TYPE_SKILL] = __d('gl', "スキル");
    }

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'user_id'    => ['uuid' => ['rule' => ['uuid']]],
        'team_id'    => ['uuid' => ['rule' => ['uuid']]],
        'name'       => ['notEmpty' => ['rule' => ['notEmpty']]],
        'active_flg' => ['boolean' => ['rule' => ['boolean']]],
        'del_flg'    => ['boolean' => ['rule' => ['boolean']]],
    ];

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [
        'User',
        'Team',
        'Image',
    ];

    function __construct($id = false, $table = null, $ds = null)
    {
        parent::__construct($id, $table, $ds);
        $this->_setTypeName();
    }

}
