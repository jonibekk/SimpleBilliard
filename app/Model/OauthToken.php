<?php
App::uses('AppModel', 'Model');

// ToDo - 大樹さん、これもう使わないですよね？

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
        self::$TYPE[self::TYPE_FB] = __("Facebook");
        self::$TYPE[self::TYPE_GOOGLE] = __("Google");
    }

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'type'    => ['numeric' => ['rule' => ['numeric'],],],
        'uid'     => ['notBlank' => ['rule' => ['notBlank'],],],
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

    function __construct($id = false, $table = null, $ds = null)
    {
        parent::__construct($id, $table, $ds);
        $this->_setProviderTypeName();
    }

}
