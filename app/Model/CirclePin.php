<?php
App::uses('AppModel', 'Model');

/**
 * CirclePin Model
 */
class CirclePin extends AppModel
{
    function __construct($id = false, $table = null, $ds = null)
    {
        parent::__construct($id, $table, $ds);
        //$this->_setPublicTypeName();
    }

    public $actsAs = [
    ];

    /**
     * Display field
     *
     * @var string
     */
    // public $displayField = 'name';

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
    ];

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [
    ];

    /**
     * hasMany associations
     *
     * @var array
     */
    public $hasMany = [
    ];

    /**
     * Create new circle
     *
     * @param array   $data
     *
     * @return bool
     */
    function add(array $data, $userId): bool
    {
        $data['Circle']['team_id'] = $this->current_team_id;
        $data['Circle']['user_id'] = $userId;

        return (bool)$this->save($data);
    }

    /**
     * サークルの基本情報を変更
     *
     * @param $data
     *
     * @return bool|mixed
     */
    function edit($data)
    {
        if (!isset($data['Circle']) || empty($data['Circle'])) {
            return false;
        }
        $members = $this->CircleMember->getMemberList($data['Circle']['id'], true);
        foreach ($members as $val) {
            Cache::delete($this->getCacheKey(CACHE_KEY_MY_CIRCLE_LIST, true, $val), 'user_data');
        }
        if ($this->isTeamAllCircle($data['Circle']['id'])) {
            Cache::delete($this->getCacheKey(CACHE_KEY_TEAM_ALL_CIRCLE, false, null));
        }
        return $this->save($data);
    }

    /**
     * $keyword にマッチする公開サークル一覧を返す
     *
     * @param       $keyword
     * @param int   $limit
     * @param array $params
     *  'public_flg' 指定した場合は公開状態で絞り込む: default null
     *
     * @return array|null
     */
    public function getCirclesByKeyword($keyword, $limit = 10, array $params = [])
    {
        // オプションデフォルト
        $params = array_merge(['public_flg' => null], $params);

        $keyword = trim($keyword);
        if (strlen($keyword) == 0) {
            return [];
        }
        $my_circle_list = $this->CircleMember->getMyCircleList();
        $options = [
            'conditions' => [
                'id'        => $my_circle_list,
                'name LIKE' => $keyword . "%",
            ],
            'limit'      => $limit,
            'fields'     => ['name', 'id', 'photo_file_name', 'team_all_flg'],
        ];
        if ($params['public_flg'] !== null) {
            $options['conditions']['public_flg'] = $params['public_flg'];
        }
        $res = $this->find('all', $options);
        return $res;
    }

    /**
     * $keyword にマッチする公開サークル一覧を返す
     *
     * @param     $keyword
     * @param int $limit
     *
     * @return array|null
     */
    public function getPublicCirclesByKeyword($keyword, $limit = 10)
    {
        return $this->getCirclesByKeyword($keyword, $limit, ['public_flg' => 1]);
    }

    /**
     * $keyword にマッチする非公開サークル一覧を返す
     *
     * @param     $keyword
     * @param int $limit
     *
     * @return array|null
     */
    public function getSecretCirclesByKeyword($keyword, $limit = 10)
    {
        return $this->getCirclesByKeyword($keyword, $limit, ['public_flg' => 0]);
    }

    function getPublicCircles($type = 'all', $start_date = null, $end_date = null, $order = 'Circle.modified desc')
    {
        $active_user_ids = $this->Team->TeamMember->getActiveTeamMembersList();

        $options = [
            'conditions' => [
                'Circle.team_id'    => $this->current_team_id,
                'Circle.public_flg' => true,
            ],
            'order'      => [$order],
            'contain'    => [
                'CircleMember' => [
                    'conditions' => [
                        'CircleMember.user_id' => $active_user_ids,
                    ],
                    'fields'     => [
                        'CircleMember.id',
                        'CircleMember.user_id'
                    ],
                ],
                'CircleAdmin'  => [
                    'conditions' => [
                        'CircleAdmin.user_id'   => $this->my_uid,
                        'CircleAdmin.admin_flg' => true
                    ],
                    'fields'     => [
                        'CircleAdmin.id'
                    ],
                ]
            ]
        ];
        if ($start_date) {
            $options['conditions']['Circle.created >='] = $start_date;
        }
        if ($end_date) {
            $options['conditions']['Circle.created <'] = $end_date;
        }
        if ($type === 'non-joined') {
            $options['conditions']['Circle.team_all_flg'] = false;
        }
        $res = $this->find('all', $options);
        //typeに応じて絞り込み
        switch ($type) {
            //参加している
            case 'joined':
                $filter = function ($circle) {
                    foreach ($circle['CircleMember'] as $member) {
                        if ($member['user_id'] == $this->my_uid) {
                            return true;
                        }
                    }
                    return false;
                };
                break;
            //参加していない
            case 'non-joined':
                $filter = function ($circle) {
                    foreach ($circle['CircleMember'] as $member) {
                        if ($member['user_id'] == $this->my_uid) {
                            return false;
                        }
                    }
                    return true;
                };
                break;
            default :
                $filter = function () {
                    return true;
                };
        }
        $res = array_filter($res, $filter);
        return $res;
    }

    /**
     * チームに存在する全サークルのリストを返す
     *
     * @return array|null
     */
    public function getList()
    {
        $options = [
            'conditions' => [
                'Circle.team_id' => $this->current_team_id,
            ],
        ];
        return $this->find('list', $options);
    }

    /**
     * 公開サークルのリストを返す
     *
     * @return array|null
     */
    public function getPublicCircleList()
    {
        $options = [
            'conditions' => [
                'Circle.team_id'    => $this->current_team_id,
                'Circle.public_flg' => true,
            ],
        ];
        return $this->find('list', $options);
    }

    function getPinnedCircles()
    {
        // 
        $CircleMember = ClassRegistry::init('CircleMember');
        $my_circle_list = $CircleMember->getMyCircleList(true);

        $options = [
            'conditions' => [
                'circle_id'        => $my_circle_list,
                'NOT' => array('pin_order'  => null),
            ],
            'fields'     => ['id', 'circle_id', 'pin_order'],
            'order'      => ['pin_order' => 'ASC'],
        ];

        $results = $this->find('all', $options);
        return $results;
    }

    function getUnpinnedCircles()
    {
        $CircleMember = ClassRegistry::init('CircleMember');
        $my_circle_list = $CircleMember->getMyCircleList(true);

        $options = [
            'conditions' => [
                'circle_id'        => $my_circle_list,
                'pin_order'        => null,
            ],
            'fields'     => ['id', 'circle_id', 'pin_order'],
        ];

        $results = $this->find('all', $options);
        return $results;
    }
}
