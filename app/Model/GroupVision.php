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
            'notEmpty'  => [
                'rule' => ['notEmpty'],
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
        if (!viaIsSet($data['GroupVision'])) {
            return false;
        }
        $data['GroupVision']['team_id'] = $this->current_team_id;

        if (!viaIsSet($data['GroupVision']['id'])) {
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
     * @param $team_id
     * @param $active_flg
     *
     * @return array|null
     */
    function getGroupVision($team_id, $active_flg)
    {
        $options = [
            'conditions' => [
                'team_id'    => $team_id,
                'active_flg' => $active_flg,
            ]
        ];
        return $this->find('all', $options);
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
        if ($with_img) {
            foreach ($group_visions as &$group_vision) {
                $group_vision['GroupVision'] = $this->attachImgUrl($group_vision['GroupVision'], 'GroupVision');
            }

        }
        $group_visions = Hash::extract($group_visions, '{n}.GroupVision');
        return $group_visions;
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
                'group_id'   => $group_ids,
                'active_flg' => $active_flg
            ]
        ];
        $res = $this->find('all', $options);
        return $res;
    }

    /**
     * AngularJSのテンプレート側から処理しやすく加工
     *
     * @param $team_id
     * @param $data
     *
     * @return mixed
     */
    function convertData($team_id, $data)
    {
        $group_list = $this->Group->getByAllName($team_id);
        $upload = new UploadHelper(new View());
        $time = new TimeExHelper(new View());

        if (isset($data['GroupVision']) === true) {
            $data['GroupVision']['photo_path'] = $upload->uploadUrl($data['GroupVision'], 'GroupVision.photo',
                ['style' => 'original']);
            $data['GroupVision']['modified'] = $time->elapsedTime(h($data['GroupVision']['modified']));
            if (isset($group_list[$data['GroupVision']['group_id']]) === true) {
                $data['GroupVision']['group_name'] = $group_list[$data['GroupVision']['group_id']];
            }

        } else {
            foreach ($data as $key => $group) {
                $data[$key]['GroupVision']['photo_path'] = $upload->uploadUrl($group['GroupVision'],
                    'GroupVision.photo',
                    ['style' => 'large']);
                $data[$key]['GroupVision']['modified'] = $time->elapsedTime(h($group['GroupVision']['modified']));
                if (isset($group_list[$group['GroupVision']['group_id']]) === true) {
                    $data[$key]['GroupVision']['group_name'] = $group_list[$group['GroupVision']['group_id']];
                }
            }
        }

        return $data;
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
