<?php
App::uses('AppModel', 'Model');

/**
 * Notification Model
 *
 * @property User $User
 * @property Team $Team
 * @property User $FromUser
 */
class Notification extends AppModel
{
    /**
     * 通知タイプ
     */
    const TYPE_GOAL = 1;
    const TYPE_POST = 2;
    static public $TYPE = [
        self::TYPE_GOAL => "",
        self::TYPE_POST => "",
    ];

    /**
     * 通知タイプの名前をセット
     */
    private function _setTypeName()
    {
        //TODO ここには通知のタイトルの文言が入る。とりあえず、仮
        self::$TYPE[self::TYPE_GOAL] = __d('gl', "ゴールの通知");
        self::$TYPE[self::TYPE_POST] = __d('gl', "投稿の通知");
    }

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'user_id'    => ['uuid' => ['rule' => ['uuid'],],],
        'team_id'    => ['uuid' => ['rule' => ['uuid'],],],
        'type'       => ['numeric' => ['rule' => ['numeric'],],],
        'unread_flg' => ['boolean' => ['rule' => ['boolean'],],],
        'del_flg'    => ['boolean' => ['rule' => ['boolean'],],],
    ];

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [
        'User',
        'Team',
        'FromUser' => ['className' => 'User', 'foreignKey' => 'from_user_id',],
    ];

    function __construct()
    {
        parent::__construct();
        $this->_setTypeName();
    }

}
