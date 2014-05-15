<?php
App::uses('AppModel', 'Model');

/**
 * Image Model
 *
 * @property User  $User
 * @property Badge $Badge
 * @property Team  $Team
 * @property Post  $Post
 */
class Image extends AppModel
{
    /**
     * 画像タイプ
     */
    const TYPE_USER = 1;
    const TYPE_GOAL = 2;
    const TYPE_BADGE = 3;
    const TYPE_POST = 4;
    static public $TYPE = [null => "", self::TYPE_USER => "", self::TYPE_GOAL => "", self::TYPE_BADGE => "", self::TYPE_POST => ""];

    /**
     * 画像タイプの名前をセット
     */
    private function _setTypeName()
    {
        self::$TYPE[null] = __d('gl', "選択してください");
        self::$TYPE[self::TYPE_USER] = __d('gl', "ユーザロゴ画像");
        self::$TYPE[self::TYPE_GOAL] = __d('gl', "ゴール画像");
        self::$TYPE[self::TYPE_BADGE] = __d('gl', "バッジ画像");
        self::$TYPE[self::TYPE_POST] = __d('gl', "投稿画像");
    }

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'user_id'        => ['uuid' => ['rule' => ['uuid']]],
        'type'           => ['numeric' => ['rule' => ['numeric']]],
        'item_file_name' => ['notEmpty' => ['rule' => ['notEmpty']]],
        'del_flg'        => ['boolean' => ['rule' => ['boolean']]],
    ];

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [
        'User',
    ];

    /**
     * hasMany associations
     *
     * @var array
     */
    public $hasMany = [
        'Badge',
        'Team',
    ];

    /**
     * hasAndBelongsToMany associations
     *
     * @var array
     */
    public $hasAndBelongsToMany = [
        'Post' => ['unique' => 'keepExisting',],
    ];

    function __construct()
    {
        parent::__construct();
        $this->_setTypeName();
    }

}
