<?php
App::uses('AppModel', 'Model');

/**
 * OauthToken Model
 *
 * @property User $User
 */
class OauthToken extends AppModel
{
    /**
     * プロバイダタイプ
     */
    const TYPE_FB = 1;
    const TYPE_GOOGLE = 2;
    static public $TYPE = [self::TYPE_FB => "", self::TYPE_GOOGLE => ""];

    /**
     * プロバイダタイプをセット
     */
    private function _setProviderTypeName()
    {
        self::$TYPE[self::TYPE_FB] = __d('gl', "Facebook");
        self::$TYPE[self::TYPE_GOOGLE] = __d('gl', "Google");
    }

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'user_id' => ['uuid' => ['rule' => ['uuid'],],],
        'type'    => ['numeric' => ['rule' => ['numeric'],],],
        'uid'     => ['notEmpty' => ['rule' => ['notEmpty'],],],
        'del_flg' => ['boolean' => ['rule' => ['boolean'],],],
    ];

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [
        'User',
    ];

    function __construct()
    {
        parent::__construct();
        $this->_setProviderTypeName();
    }

}
