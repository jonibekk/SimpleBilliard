<?php
App::uses('AppModel', 'Model');
App::uses('UploadHelper', 'View/Helper');
App::uses('TimeExHelper', 'View/Helper');
App::uses('View', 'View');

/**
 * GroupVision Model
 *
 * @property User  $CreateUser
 * @property User  $ModifyUser
 * @property Team  $Team
 * @property Group $Group
 */
class GroupVision extends AppModel
{

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
        'name'        => [
            'isString'  => [
                'rule' => ['isString',],
            ],
            'maxLength' => ['rule' => ['maxLength', 200]],
            'notBlank'  => [
                'rule' => ['notBlank'],
            ],
        ],
        'group_id'    => [
            'numeric' => [
                'rule'       => ['numeric'],
                'allowEmpty' => true,
            ],
        ],
        'description' => [
            'isString'  => [
                'rule' => ['isString',],
            ],
            'maxLength' => ['rule' => ['maxLength', 2000]],
        ],
        'photo'       => [
            'image_max_size'  => ['rule' => ['attachmentMaxSize', 10485760],], //10mb
            'image_type'      => ['rule' => ['attachmentImageType',],],
            'canProcessImage' => ['rule' => 'canProcessImage',],
        ],
        'active_flg'  => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
        'del_flg'     => [
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
        'CreateUser' => [
            'className'  => 'User',
            'foreignKey' => 'create_user_id',
        ],
        'ModifyUser' => [
            'className'  => 'User',
            'foreignKey' => 'modify_user_id',
        ],
        'Team',
        'Group',
    ];

    function saveGroupVision($data)
    {
        if (!Hash::get($data, 'GroupVision')) {
            return false;
        }
        $data['GroupVision']['team_id'] = $this->current_team_id;

        if (!Hash::get($data, 'GroupVision.id')) {
            $data['GroupVision']['create_user_id'] = $this->my_uid;
        }
        $data['GroupVision']['modify_user_id'] = $this->my_uid;
        $res = $this->save($data);
        Cache::clear(false, 'team_info');
        return $res;
    }

    /**
     * チームに所属するすべてのグループビジョンを取得
     *
     * @param int  $teamId
     * @param bool $activeFlg
     *
     * @return array
     */
    function findGroupVisions(int $teamId, bool $activeFlg): array
    {
        $options = [
            'conditions' => [
                'team_id'    => $teamId,
                'active_flg' => $activeFlg,
            ]
        ];
        return (array)$this->find('all', $options);
    }

    /**
     * @param bool $with_img
     *
     * @return array|null
     */
    function getMyGroupVision($with_img = false)
    {
        $model = $this;
        $group_visions = Cache::remember($this->getCacheKey(CACHE_KEY_GROUP_VISION, true),
            function () use ($model) {
                $my_group_list = $model->Group->MemberGroup->getMyGroupList();
                $group_visions = $model->getGroupVisionsByGroupIds(array_keys($my_group_list));
                foreach ($group_visions as $k => $v) {
                    if (isset($my_group_list[$v['GroupVision']['group_id']])) {
                        $group_visions[$k]['GroupVision']['target_name'] = $my_group_list[$v['GroupVision']['group_id']];
                    } else {
                        $group_visions[$k]['GroupVision']['target_name'] = null;
                    }
                }
                return $group_visions;
            }, 'team_info');
        $group_visions = Hash::insert($group_visions, '{n}.GroupVision.model', 'GroupVision');
        $res = [];
        foreach ($group_visions as $group_vision) {
            if ($with_img) {
                $group_vision['GroupVision'] = $this->attachImgUrl($group_vision['GroupVision'], 'GroupVision');
            }
            $v = $group_vision['GroupVision'];
            $v['group'] = $group_vision['Group'];
            $res[] = $v;
        }
        return $res;
    }

    /**
     * グループIDからアクティブなグループビジョンを取得
     *
     * @param      $group_ids
     * @param bool $active_flg
     *
     * @return array|null
     */
    function getGroupVisionsByGroupIds($group_ids, $active_flg = true)
    {
        $options = [
            'conditions' => [
                'GroupVision.group_id'   => $group_ids,
                'GroupVision.active_flg' => $active_flg
            ],
            'contain'    => ['Group']
        ];
        $res = $this->find('all', $options);
        return $res;
    }

    /**
     * グループ名も含めグループビジョンを取得する
     *
     * @param $id
     *
     * @return array|null
     */
    function findWithGroupById($id)
    {
        $options = [
            'conditions' => [
                'GroupVision.id' => $id,
            ],
            'contain'    => ['Group']
        ];
        $res = $this->find('first', $options);
        return $res;
    }

    /**
     * アーカイブ設定
     *
     * @param $group_vision_id
     * @param $active_flg
     *
     * @return mixed
     * @throws Exception
     */
    function setGroupVisionActiveFlag($group_vision_id, $active_flg)
    {
        $this->id = $group_vision_id;
        Cache::clear(false, 'team_info');
        return $this->save(['active_flg' => $active_flg]);
    }

    /**
     * 削除
     *
     * @param $group_vision_id
     *
     * @return bool
     */
    function deleteGroupVision($group_vision_id)
    {
        $this->id = $group_vision_id;
        Cache::clear(false, 'team_info');
        return $this->delete();
    }

    /**
     * １件取得
     *
     * @param $group_vision_id
     * @param $active_flg
     *
     * @return array|null
     */
    function getGroupVisionDetail($group_vision_id, $active_flg)
    {
        $options = [
            'conditions' => [
                'id'         => $group_vision_id,
                'active_flg' => $active_flg
            ]
        ];
        return $this->find('first', $options);
    }
}
