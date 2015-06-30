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
        'name'       => [
            'notEmpty' => [
                'rule' => ['notEmpty'],
            ],
        ],
        'active_flg' => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
        'del_flg'    => [
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
        return $res;
    }

    /**
     * チームに所属するすべてのグループビジョンを取得
     * @param $team_id
     * @param $active_flg
     * @return array|null
     */
    function getGroupVision($team_id, $active_flg)
    {
        $options = [
            'conditions' => [
                'team_id' => $team_id,
                'active_flg' => $active_flg,
            ]
        ];
        return $this->find('all', $options);
    }

    /**
     * AngularJSのテンプレート側から処理しやすく加工
     * @param $team_id
     * @param $data
     * @return mixed
     */
    function convertData($team_id, $data)
    {
        $group_list = $this->Group->getByAllName($team_id);
        $upload = new UploadHelper(new View());
        $time = new TimeExHelper(new View());

        foreach ($data as $key => $group) {
            $data[$key]['GroupVision']['photo_path'] = $upload->uploadUrl($group['GroupVision'], 'GroupVision.photo', ['style' => 'large']);
            $data[$key]['GroupVision']['modified'] = $time->elapsedTime(h($group['GroupVision']['modified']));
            if (isset($group_list[$group['GroupVision']['group_id']]) === true) {
                $data[$key]['GroupVision']['group_name'] = $group_list[$group['GroupVision']['group_id']];
            }
        }
        return $data;
    }
}
