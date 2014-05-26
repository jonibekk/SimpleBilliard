<?php
App::uses('AppModel', 'Model');

/**
 * SendMail Model
 *
 * @property User $FromUser
 * @property User $ToUser
 * @property Team $Team
 */
class SendMail extends AppModel
{
    /**
     * メールテンプレタイプ
     */
    const TYPE_TMPL_ACCOUNT_VERIFY = 1;
    static public $TYPE_TMPL = [
        self::TYPE_TMPL_ACCOUNT_VERIFY => [
            'subject'  => null,
            'template' => 'account_verification',
            'layout'   => 'default',
        ],
    ];

    private function _setTemplateSubject()
    {
        self::$TYPE_TMPL[self::TYPE_TMPL_ACCOUNT_VERIFY]['subject'] = __d('mail', "アカウントの仮登録が完了しました。");
        //subjectにサービス名のプレフィックスを追加
        foreach (self::$TYPE_TMPL as $key => $val) {
            self::$TYPE_TMPL[$key]['subject'] = "[" . SERVICE_NAME . "]" . $val['subject'];
        }
    }

    function __construct($id = false, $table = null, $ds = null)
    {
        parent::__construct($id, $table, $ds);
        $this->_setTemplateSubject();
    }

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'to_user_id'    => [
            'uuid' => ['rule' => ['uuid'],],
        ],
        'template_type' => [
            'numeric' => ['rule' => ['numeric'],],
        ],
        'del_flg'       => [
            'boolean' => ['rule' => ['boolean'],],
        ],
    ];

    //The Associations below have been created with all possible keys, those that are not needed can be removed

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [
        'FromUser' => ['className' => 'User', 'foreignKey' => 'from_user_id',],
        'ToUser'   => ['className' => 'User', 'foreignKey' => 'to_user_id',],
        'Team',
    ];

    /**
     * 送信メールデータ作成
     *
     * @param       $to_uid
     * @param       $tmpl_type
     * @param array $item
     * @param null  $from_uid
     * @param null  $team_id
     *
     * @return bool
     */
    public function saveMailData($to_uid, $tmpl_type, $item = [], $from_uid = null, $team_id = null)
    {
        if (!$to_uid || !$tmpl_type) {
            return false;
        }
        $data = [
            'to_user_id'    => $to_uid,
            'template_type' => $tmpl_type,
            'item'          => (!empty($item)) ? json_encode($item) : null,
            'from_user_id'  => ($from_uid) ? $from_uid : null,
            'team_id'       => ($team_id) ? $team_id : null,
        ];
        return $this->save($data);
    }

    /**
     * 詳細なユーザ名等を含んだデータを返す
     *
     * @param $id
     *
     * @return array|null
     */
    public function getDetail($id)
    {
        if (!$id) {
            return null;
        }
        $options = [
            'conditions' => ['SendMail.id' => $id],
            'contain'    => [
                'ToUser'   => ['PrimaryEmail'],
                'FromUser' => ['PrimaryEmail'],
                'Team'
            ]
        ];
        return $this->find('first', $options);
    }
}
