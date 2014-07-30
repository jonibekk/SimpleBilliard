<?php
App::uses('AppModel', 'Model');

/**
 * Circle Model
 *
 * @property Team         $Team
 * @property CircleMember $CircleMember
 */
class Circle extends AppModel
{
    /**
     * 公開タイプ
     */
    const TYPE_PUBLIC_ON = 1;
    const TYPE_PUBLIC_OFF = 0;
    static public $TYPE_PUBLIC = [self::TYPE_PUBLIC_ON => "", self::TYPE_PUBLIC_OFF => "",];

    /**
     * 公開タイプの名前をセット
     */
    private function _setPublicTypeName()
    {
        self::$TYPE_PUBLIC[self::TYPE_PUBLIC_ON] = __d('gl', "公開");
        self::$TYPE_PUBLIC[self::TYPE_PUBLIC_OFF] = __d('gl', "非公開");
    }

    function __construct($id = false, $table = null, $ds = null)
    {
        parent::__construct($id, $table, $ds);
        $this->_setPublicTypeName();
    }

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
                'default_url' => 'no-image.jpg',
                'quality'     => 100,
            ]
        ]
    ];

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
        'name'    => [
            'notEmpty' => [
                'rule' => ['notEmpty'],
            ],
        ],
        'del_flg' => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
        'photo'   => [
            'image_max_size' => [
                'rule' => [
                    'attachmentMaxSize',
                    10485760 //10mb
                ],
            ],
        ],
    ];

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [
        'Team'
    ];

    /**
     * hasMany associations
     *
     * @var array
     */
    public $hasMany = [
        'CircleMember'
    ];

    /**
     * 新規サークル追加(管理者として自分を登録)
     *
     * @param array $data
     *
     * @return mixed
     */
    function add($data)
    {
        if (!isset($data['Circle']) || empty($data['Circle'])) {
            return false;
        }
        $data['Circle']['team_id'] = $this->current_team_id;
        $data['CircleMember'][0]['team_id'] = $this->current_team_id;
        $data['CircleMember'][0]['admin_flg'] = true;
        $data['CircleMember'][0]['user_id'] = $this->me['id'];
        if (!empty($data['Circle']['members'])) {
            $members = explode(",", $data['Circle']['members']);
            foreach ($members as $val) {
                $data['CircleMember'][] = [
                    'team_id' => $this->current_team_id,
                    'user_id' => $val
                ];
            }
        }
        return $this->saveAll($data);
    }

}
