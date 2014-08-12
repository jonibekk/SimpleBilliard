<?php
App::uses('AppModel', 'Model');

/**
 * Circle Model
 *
 * @property Team            $Team
 * @property CircleMember    $CircleMember
 * @property PostShareCircle $PostShareCircle
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
        self::$TYPE_PUBLIC[self::TYPE_PUBLIC_OFF] = __d('gl', "秘密");
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
            'image_max_size' => ['rule' => ['attachmentMaxSize', 10485760],], //10mb
            'image_type'     => ['rule' => ['attachmentContentType', ['image/jpeg', 'image/gif', 'image/png']],]
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
        'CircleMember'    => [
            'dependent' => true,
        ],
        'PostShareCircle' => [
            'dependent' => true,
        ],
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
                $val = str_replace('user_', '', $val);;
                $data['CircleMember'][] = [
                    'team_id' => $this->current_team_id,
                    'user_id' => $val
                ];
            }
        }
        if ($res = $this->saveAll($data)) {
            $this->CircleMember->updateCounterCache(['circle_id' => $this->getLastInsertID()]);
        }
        return $res;
    }

    function edit($data)
    {
        if (!isset($data['Circle']) || empty($data['Circle'])) {
            return false;
        }
        //既存のメンバーを取得
        $exists_member_list = $this->CircleMember->getMemberList($data['Circle']['id']);
        if (isset($data['Circle']['members']) && !empty($data['Circle']['members'])) {
            $members = explode(",", $data['Circle']['members']);
            foreach ($members as $val) {
                $val = str_replace('user_', '', $val);;
                if ($key = array_search($val, $exists_member_list)) {
                    unset($exists_member_list[$key]);
                    continue;
                }
                $data['CircleMember'][] = [
                    'team_id' => $this->current_team_id,
                    'user_id' => $val,
                ];
            }
        }
        //既存メンバーで指定されないメンバーがいた場合、削除
        if (!empty($exists_member_list)) {
            $this->CircleMember->deleteAll(['CircleMember.circle_id' => $data['Circle']['id'], 'CircleMember.user_id' => $exists_member_list]);
        }
        if ($res = $this->saveAll($data)) {
            $this->CircleMember->updateCounterCache(['circle_id' => $data['Circle']['id']]);
        }
        return $res;
    }

    public function getCirclesByKeyword($keyword, $limit = 10)
    {
        $my_circle_list = $this->CircleMember->getMyCircleList();
        $options = [
            'conditions' => [
                'id'          => $my_circle_list,
                'name Like ?' => "%" . $keyword . "%",
            ],
            'limit'      => $limit,
            'fields'     => ['name', 'id', 'photo_file_name'],
        ];
        $res = $this->find('all', $options);
        return $res;
    }

    public function getEditData($id)
    {
        $options = [
            'conditions' => ['Circle.id' => $id],
            'contain'    => [
                'CircleMember' => [
                    'conditions' => [
                        'NOT' => ['CircleMember.user_id' => $this->me['id']]
                    ]
                ]
            ]
        ];
        $circle = $this->find('first', $options);
        $circle['Circle']['members'] = null;
        if (!empty($circle['CircleMember'])) {
            foreach ($circle['CircleMember'] as $val) {
                $circle['Circle']['members'][] = 'user_' . $val['user_id'];
            }
            $circle['Circle']['members'] = implode(',', $circle['Circle']['members']);
        }
        unset($circle['CircleMember']);
        return $circle;
    }

    function getPublicCircles()
    {
        $options = [
            'conditions' => [
                'Circle.team_id'    => $this->current_team_id,
                'Circle.public_flg' => true,
            ],
            'order'      => ['Circle.modified desc'],
            'contain'    => [
                'CircleMember' => [
                    'conditions' => [
                        'CircleMember.user_id' => $this->me['id']
                    ]
                ]
            ]
        ];
        $res = $this->find('all', $options);
        return $res;
    }
}
