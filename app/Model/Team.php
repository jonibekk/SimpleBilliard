<?php
App::uses('AppModel', 'Model');

/**
 * Team Model
 *
 * @property Badge          $Badge
 * @property CommentLike    $CommentLike
 * @property CommentMention $CommentMention
 * @property CommentRead    $CommentRead
 * @property Comment        $Comment
 * @property GivenBadge     $GivenBadge
 * @property Group          $Group
 * @property Invite         $Invite
 * @property JobCategory    $JobCategory
 * @property Notification   $Notification
 * @property PostLike       $PostLike
 * @property PostMention    $PostMention
 * @property PostRead       $PostRead
 * @property Post           $Post
 * @property TeamMember     $TeamMember
 * @property Thread         $Thread
 */
class Team extends AppModel
{
    /**
     * タイプ
     */
    const TYPE_FREE = 1;
    const TYPE_PRO = 2;
    static public $TYPE = [self::TYPE_FREE => "", self::TYPE_PRO => ""];

    /**
     * タイプの名前をセット
     */
    private function _setTypeName()
    {
        self::$TYPE[self::TYPE_FREE] = __d('gl', "フリー");
        self::$TYPE[self::TYPE_PRO] = __d('gl', "プロ");
    }

    /**
     * Display field
     *
     * @var string
     */
    public $displayField = 'name';

    public $actsAs = [
        'Upload' => [
            'photo' => [
                'styles'      => [
                    'small'        => '32x32',
                    'medium'       => '48x48',
                    'medium_large' => '96x96',
                    'large'        => '128x128',
                    'x_large'      => '256x256',
                ],
                'path'        => ":webroot/upload/:model/:id/:hash_:style.:extension",
                'default_url' => 'no-image-team.jpg',
                'quality'     => 100,
            ]
        ]
    ];
    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'name'               => ['notEmpty' => ['rule' => ['notEmpty'],],],
        'type'               => ['numeric' => ['rule' => ['numeric'],],],
        'domain_limited_flg' => ['boolean' => ['rule' => ['boolean'],],],
        'start_term_month'   => ['numeric' => ['rule' => ['numeric'],],],
        'border_months'      => ['numeric' => ['rule' => ['numeric'],],],
        'del_flg'            => ['boolean' => ['rule' => ['boolean'],],],
        'photo'              => [
            'image_max_size' => ['rule' => ['attachmentMaxSize', 10485760],], //10mb
            'image_type'     => ['rule' => ['attachmentContentType', ['image/jpeg', 'image/gif', 'image/png']],]
        ],
        'emails'             => [
            'notEmpty'    => ['rule' => ['notEmpty'],],
            'emailsCheck' => [
                'rule' => ['emailsCheck']
            ],
        ],
    ];

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [];

    /**
     * hasMany associations
     *
     * @var array
     */
    public $hasMany = [
        'Badge',
        'CommentLike',
        'CommentMention',
        'CommentRead',
        'Comment',
        'GivenBadge',
        'Group',
        'Invite',
        'JobCategory',
        'Notification',
        'PostLike',
        'PostMention',
        'PostRead',
        'Post',
        'TeamMember',
        'Thread',
    ];

    function __construct($id = false, $table = null, $ds = null)
    {
        parent::__construct($id, $table, $ds);
        $this->_setTypeName();
    }

    /**
     * @param array  $postData
     * @param string $uid
     *
     * @return array|bool
     */
    function add($postData, $uid)
    {
        $this->set($postData);
        if (!$this->validates()) {
            return false;
        }
        $team_member = [
            'TeamMember' => [
                [
                    'user_id'   => $uid,
                    'admin_flg' => true,
                ]
            ]
        ];
        $postData = array_merge($postData, $team_member);
        $this->saveAll($postData);
        //デフォルトチームを更新
        $user = $this->TeamMember->User->findById($uid);
        if (isset($user['User']) && !$user['User']['default_team_id']) {
            $this->TeamMember->User->id = $uid;
            $this->TeamMember->User->saveField('default_team_id', $this->id);
        }
        return true;
    }

    /**
     * @param $data
     *
     * @return null
     */
    function getEmailListFromPost($data)
    {
        if (!isset($data['Team']['emails'])) {
            return null;
        }
        $this->set($data);
        if (!$this->validates()) {
            return null;
        }
        $res = $this->extractEmail($data['Team']['emails']);
        return $res;
    }

    /**
     * @param $emails
     *
     * @return array
     */
    function extractEmail($emails)
    {
        $res = [];
        //一行ずつ処理
        $cr = array("\r\n", "\r"); // 改行コード置換用配列を作成しておく

        $emails = trim($emails); // 文頭文末の空白を削除

        // 改行コードを統一
        //str_replace ("検索文字列", "置換え文字列", "対象文字列");
        $emails = str_replace($cr, "\n", $emails);

        //改行コードで分割（結果は配列に入る）
        $lines_array = explode("\n", $emails);
        //一行ずつ処理
        foreach ($lines_array as $line) {
            //カンマで分割
            $emails = explode(",", $line);
            //メールアドレス毎に処理
            foreach ($emails as $email) {
                //全角スペースを除去
                $email = preg_replace('/　/', ' ', $email);
                //前後スペースを除去
                $email = trim($email);
                //空行はスキップ
                if (empty($email)) {
                    continue;
                }
                if (!in_array($email, $res)) {
                    $res[] = $email;
                }
            }
        }
        return $res;
    }
}
