<?php
App::uses('AppModel', 'Model');
App::uses('UploadHelper', 'View/Helper');
App::uses('TimeExHelper', 'View/Helper');
App::uses('View', 'View');

/**
 * TeamVision Model
 *
 * @property User           $CreateUser
 * @property User           $ModifyUser
 * @property Team           $Team
 * @property UploadBehavior $Upload
 */
class TeamVision extends AppModel
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
        'description' => [
            'isString'  => [
                'rule'       => ['isString',],
                'allowEmpty' => true,
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
    ];

    function saveTeamVision($data)
    {
        if (!viaIsSet($data['TeamVision'])) {
            return false;
        }
        $data['TeamVision']['team_id'] = $this->current_team_id;

        if (!viaIsSet($data['TeamVision']['id'])) {
            $data['TeamVision']['create_user_id'] = $this->my_uid;
        }
        $data['TeamVision']['modify_user_id'] = $this->my_uid;
        $res = $this->save($data);
        Cache::delete($this->getCacheKey(CACHE_KEY_TEAM_VISION, false), 'team_info');
        return $res;
    }

    /**
     * @param      $team_id
     * @param      $active_flg
     * @param bool $with_img
     *
     * @return array|mixed|null
     */
    function getTeamVision($team_id, $active_flg, $with_img = false)
    {
        $is_default = false;
        if ($team_id === $this->current_team_id && $active_flg) {
            $is_default = true;
            $res = Cache::read($this->getCacheKey(CACHE_KEY_TEAM_VISION, false), 'team_info');
            if ($res !== false) {
                if ($with_img) {
                    foreach ($res as &$vision) {
                        $vision['TeamVision'] = $this->attachImgUrl($vision['TeamVision'], 'TeamVision');
                    }
                }
                return $res;
            }
        }
        $options = [
            'conditions' => [
                'team_id'    => $team_id,
                'active_flg' => $active_flg
            ],
            'contain'    => ['Team'],
        ];
        $res = $this->find('all', $options);

        if ($is_default) {
            Cache::write($this->getCacheKey(CACHE_KEY_TEAM_VISION, false), $res, 'team_info');
        }

        if ($with_img) {
            foreach ($res as &$v) {
                $v['TeamVision'] = $this->attachImgUrl($v['TeamVision'], 'TeamVision');
            }
        }
        return $res;
    }

    function setTeamVisionActiveFlag($team_vision_id, $active_flg)
    {
        $this->id = $team_vision_id;
        Cache::delete($this->getCacheKey(CACHE_KEY_TEAM_VISION, false), 'team_info');
        return $this->save(['active_flg' => $active_flg]);
    }

    function convertData($data)
    {
        $upload = new UploadHelper(new View());
        $time = new TimeExHelper(new View());

        if (isset($data['TeamVision']) === true) {
            $data['TeamVision']['photo_path'] = $upload->uploadUrl($data['TeamVision'], 'TeamVision.photo',
                ['style' => 'original']);
            $data['TeamVision']['modified'] = $time->elapsedTime(h($data['TeamVision']['modified']));

        } else {
            foreach ($data as $key => $team) {
                $data[$key]['TeamVision']['photo_path'] = $upload->uploadUrl($team['TeamVision'], 'TeamVision.photo',
                    ['style' => 'original']);
                $data[$key]['TeamVision']['modified'] = $time->elapsedTime(h($team['TeamVision']['modified']));
            }
        }

        return $data;
    }

    function deleteTeamVision($team_vision_id)
    {
        $this->id = $team_vision_id;
        Cache::delete($this->getCacheKey(CACHE_KEY_TEAM_VISION, false), 'team_info');
        return $this->delete();
    }

    function getTeamVisionDetail($team_vision_id, $active_flg)
    {
        $options = [
            'conditions' => [
                'id'         => $team_vision_id,
                'active_flg' => $active_flg
            ]
        ];
        return $this->find('first', $options);
    }

    function getDisplayVisionRandom()
    {
        if (!$this->current_team_id) {
            return null;
        }
        $team_name = $this->Team->getCurrentTeam()['Team']['name'];
        $team_visions = Hash::extract($this->getTeamVision($this->current_team_id, true), '{n}.TeamVision');
        $team_visions = Hash::insert($team_visions, '{n}.target_name', $team_name);
        $team_visions = Hash::insert($team_visions, '{n}.model', 'TeamVision');
        $group_visions = $this->Team->GroupVision->getMyGroupVision();
        $visions = array_merge($team_visions, $group_visions);
        if (empty($visions)) {
            return null;
        }
        $key = array_rand($visions, 1);
        $res = $visions[$key];
        return $res;
    }
}
