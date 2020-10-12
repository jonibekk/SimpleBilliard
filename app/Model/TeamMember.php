<?php
App::uses('AppModel', 'Model');
App::uses('UploadHelper', 'View/Helper');
App::uses('View', 'View');
App::uses('TransactionManager', 'Model');
App::uses('TranslationLanguage', 'Model');
App::import('Service', 'UserService');
App::import('Model/Entity', 'TeamMemberEntity');
App::import('Lib/Cache/Redis/PaymentFlag', 'PaymentTiming');

use Goalous\Enum as Enum;

/**
 * TeamMember Model
 *
 * @property User        $User
 * @property Team        $Team
 * @property MemberType  $MemberType
 * @property User        $CoachUser
 * @property JobCategory $JobCategory
 */

use Goalous\Enum\Model\TeamMember as TeamMemberEnum;
use Goalous\Enum\DataType\DataType as DataType;
use Goalous\Exception as GlException;

class TeamMember extends AppModel

{
    const ADMIN_USER_FLAG = 1;
    const MAX_NUMBER_OF_EVALUATORS = 7;

    /**
     * User status valid codes
     * TODO.Payment: delete and move to enum
     */
    const USER_STATUS_INVITED = 0;
    const USER_STATUS_ACTIVE = 1;
    const USER_STATUS_INACTIVE = 2;

    public $myTeams = [];
    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'member_no'             => ['maxLength' => ['rule' => ['maxLength', 64]]],
        'comment'               => [
            'isString'  => [
                'rule'       => ['isString',],
                'allowEmpty' => true,
            ],
            'maxLength' => ['rule' => ['maxLength', 2000]],
        ],
        'status'                => [
            'inEnumList'      => [
                'rule' => [
                    'inEnumList',
                    "Goalous\Enum\Model\TeamMember\Status"
                ],
            ],
            'isVerifiedEmail' => [
                'rule' => ['isVerifiedEmail']
            ]
        ],
        'evaluation_enable_flg' => ['boolean' => ['rule' => ['boolean'],],],
        'invitation_flg'        => ['boolean' => ['rule' => ['boolean'],],],
        'admin_flg'             => ['boolean' => ['rule' => ['boolean'],],],
        'del_flg'               => ['boolean' => ['rule' => ['boolean'],],],
    ];

    public $validateBackup = [];

    //The Associations below have been created with all possible keys, those that are not needed can be removed

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [
        'User',
        'Team',
        'CoachUser' => ['className' => 'User', 'foreignKey' => 'coach_user_id',],
        'JobCategory',
        'MemberType',
    ];

    public $myStatusWithTeam = [];

    public $csv_datas = [];
    private $csv_emails = [];
    private $csv_member_ids = [];
    private $csv_coach_ids = [];
    private $csv_evaluator_ids = [];
    private $all_users = [];
    private $evaluations = [];
    private $active_member_list = [];
    private $local_lang_list = [];

    public $modelConversionTable = [
        'user_id'               => DataType::INT,
        'team_id'               => DataType::INT,
        'coach_user_id'         => DataType::INT,
        'member_type_id'        => DataType::INT,
        'job_category_id'       => DataType::INT,
        'active_flg'            => DataType::BOOL,
        'invitation_flg'        => DataType::BOOL,
        'evaluation_enable_flg' => DataType::BOOL,
        'admin_flg'             => DataType::BOOL,
        'evaluable_count'       => DataType::INT,
        'last_login'            => DataType::INT,
        'status'                => DataType::INT,
    ];

    /**
     * 現在有効なチーム一覧を取得
     *
     * @param      $uid
     *
     * @return array
     */
    function getActiveTeamList($uid)
    {
        $teamListCache = Cache::read($this->getCacheKey(CACHE_KEY_TEAM_LIST, true, $uid, false), 'team_info');
        if (empty($this->myTeams) || empty($teamListCache)) {
            $this->setActiveTeamList($uid);
        }
        return $this->myTeams;
    }

    function setActiveTeamList($uid)
    {
        $model = $this;
        $res = Cache::remember($this->getCacheKey(CACHE_KEY_TEAM_LIST, true, $uid, false),
            function () use ($model, $uid) {
                $options = [
                    'conditions' => [
                        'TeamMember.user_id' => $uid,
                        'TeamMember.status'  => self::USER_STATUS_ACTIVE,
                        'TeamMember.del_flg' => false
                    ],
                    'fields'     => ['TeamMember.team_id', 'Team.name'],
                    'joins'      => [
                        [
                            'type'       => 'INNER',
                            'table'      => 'teams',
                            'alias'      => 'Team',
                            'conditions' => [
                                'Team.id = TeamMember.team_id',
                                'Team.del_flg' => false,
                            ]
                        ]
                    ]
                ];
                $res = array_filter($model->findWithoutTeamId('list', $options));
                return $res;
            }, 'team_info');
        $this->myTeams = $res;
    }

    public function getActiveTeamMembersList($use_cache = true, $teamId = null)
    {
        $teamId = $teamId ?: $this->current_team_id;
        if ($use_cache && !empty($this->active_member_list)) {
            return $this->active_member_list;
        }
        //if team is not exist
        if (!$this->Team->findById($teamId)) {
            return [];
        }
        $options = [
            'conditions' => [
                'status'  => self::USER_STATUS_ACTIVE,
                'team_id' => $teamId
            ],
            'fields'     => ['user_id', 'user_id']
        ];
        $this->active_member_list = $this->find('list', $options);
        $this->active_member_list = $this->User->filterActiveUserList($this->active_member_list);

        return $this->active_member_list;
    }

    /**
     * Update last login time of a user in a team
     *
     * @param int|null $teamId
     * @param int      $userId
     * @param int      $loginTimestamp
     *
     * @return array
     *
     * @throws Exception
     */
    public function updateLastLogin(?int $teamId, int $userId, int $loginTimestamp = REQUEST_TIMESTAMP): array
    {
        if (is_null($teamId)){
            return[];
        }

        $teamMember = $this->find('first', ['conditions' => ['user_id' => $userId, 'team_id' => $teamId]]);

        if (empty($teamMember)) {
            throw new GlException\GoalousNotFoundException("Team Member doesn't exist");
        }

        $teamMember['TeamMember']['last_login'] = $loginTimestamp;

        $enable_with_team_id = false;
        if ($this->Behaviors->loaded('WithTeamId')) {
            $enable_with_team_id = true;
        }
        if ($enable_with_team_id) {
            $this->Behaviors->disable('WithTeamId');
        }

        $res = $this->save($teamMember);

        if ($enable_with_team_id) {
            $this->Behaviors->enable('WithTeamId');
        }

        return $res;
    }

    function getWithTeam($team_id = null, $uid = null)
    {
        if (!empty($this->myStatusWithTeam)) {
            return $this->myStatusWithTeam;
        }
        $is_default = false;
        if ($team_id === null && $uid === null) {
            $is_default = true;
            $res = Cache::read($this->getCacheKey(CACHE_KEY_MY_MEMBER_STATUS, true), 'team_info');
            if ($res !== false) {
                return $this->myStatusWithTeam = $res;
            }
        }
        if (!$team_id) {
            $team_id = $this->current_team_id;
        }
        if (!$uid) {
            if (isset($this->my_uid)) {
                $uid = $this->my_uid;
            } else {
                return [];
            }
        }
        $options = [
            'conditions' => [
                'TeamMember.user_id' => $uid,
                'TeamMember.team_id' => $team_id,
            ],
            'contain'    => ['Team']
        ];
        $res = $this->find('first', $options);
        $this->myStatusWithTeam = $res;
        if ($is_default && !empty($res)) {
            Cache::write($this->getCacheKey(CACHE_KEY_MY_MEMBER_STATUS, true), $res, 'team_info');
        }
        return $res;
    }

    public function setMyStatusWithTeam($team_id, $uid)
    {
        $this->myStatusWithTeam = $this->getWithTeam($team_id, $uid);
    }

    /**
     * 通常のアクセス権限チェック（自分が所属しているチームかどうか？）
     *
     * @param      $team_id
     * @param      $uid
     * @param bool $skipCheckUserStatus
     *
     * @return bool
     * @throws RuntimeException
     */
    public function permissionCheck($team_id, $uid, bool $skipCheckUserStatus = false): bool
    {
        //チームに入っていない場合
        if (!$team_id) {
            throw new RuntimeException(__("If you want to access this page, please switch to the team."));
        }
        if (empty($this->Team->findById($team_id))) {
            throw new RuntimeException(__('Your team no longer has access to Goalous.'));
        }
        if (!$this->myStatusWithTeam) {
            $this->setMyStatusWithTeam($team_id, $uid);
        }
        if (empty($this->myStatusWithTeam['Team'])) {
            throw new RuntimeException(__("There is no team."));
        }
        if (!$skipCheckUserStatus && $this->myStatusWithTeam['TeamMember']['status'] != self::USER_STATUS_ACTIVE) {
            throw new RuntimeException(__("You can't access to this team. Your account has been disabled."));
        }
        return true;
    }

    /**
     * @param      $uid
     * @param null $teamId
     * @param bool $withCache
     *
     * @return bool
     * @deprecated
     * We should consider whether keep to use CACHE_KEY_MEMBER_IS_ACTIVE cache
     */
    public function isActive($uid, $teamId = null, bool $withCache = true)
    {
        if (!$teamId) {
            if (!$this->current_team_id) {
                return false;
            }
            $teamId = $this->current_team_id;
        }
        $isDefault = false;
        if ($uid == $this->my_uid && $teamId == $this->current_team_id) {
            $isDefault = true;
            if ($withCache) {
                $res = Cache::read($this->getCacheKey(CACHE_KEY_MEMBER_IS_ACTIVE, true), 'team_info');
                if ($res !== false) {
                    if (!empty($res) && Hash::get($res, 'User.id') && Hash::get($res, 'Team.id')) {
                        return true;
                    }
                    return false;
                }
            }
        }
        $options = [
            'conditions' => [
                'TeamMember.team_id' => $teamId,
                'TeamMember.user_id' => $uid,
                'TeamMember.status'  => self::USER_STATUS_ACTIVE,
            ],
            'fields'     => ['TeamMember.id', 'TeamMember.user_id', 'TeamMember.team_id'],
            'contain'    => [
                'User' => [
                    'conditions' => [
                        'User.active_flg' => true,
                        'User.del_flg'    => false
                    ],
                    'fields'     => ['User.id']
                ],
                'Team' => [
                    'conditions' => [
                        'Team.del_flg' => false
                    ],
                    'fields'     => ['Team.id']
                ]
            ]
        ];
        $res = $this->find('first', $options);
        if ($withCache && $isDefault) {
            Cache::write($this->getCacheKey(CACHE_KEY_MEMBER_IS_ACTIVE, true), $res, 'team_info');
        }
        if (!empty($res) && Hash::get($res, 'User.id') && Hash::get($res, 'Team.id')) {
            return true;
        }
        return false;
    }

    /**
     * アクセス権限の確認
     *
     * @param $team_id
     * @param $uid
     *
     * @return boolean
     * @throws RuntimeException
     */
    public function adminCheck($team_id = null, $uid = null)
    {
        if (!$team_id) {
            $team_id = $this->current_team_id;
        }
        if (!$uid) {
            $uid = $this->my_uid;
        }
        //まず通常のチームアクセス権限があるかチェック
        $this->permissionCheck($team_id, $uid);
        if (!$this->myStatusWithTeam['TeamMember']['admin_flg']) {
            throw new RuntimeException(__("Only team administrators are allowed to access to this page."));
        }
        return true;
    }

    public function isAdmin($uid = null): bool
    {
        if (!$uid) {
            $uid = $this->my_uid;
        }

        $options = [
            'conditions' => [
                'user_id'   => $uid,
                'admin_flg' => true
            ]
        ];
        $res = $this->find('first', $options);
        return (bool)$res;
    }

    /**
     * Create or update status of user member information in a team.
     *
     * @param int $userId
     * @param int $teamId ID of the team that the user is joining
     *
     * @return array|BaseEntity|mixed
     * @throws Exception
     */
    public function add(int $userId, int $teamId)
    {
        //if exists update
        $team_member = $this->find('first', ['conditions' => ['user_id' => $userId, 'team_id' => $teamId]]);
        if (Hash::get($team_member, 'TeamMember.id')) {
            $team_member['TeamMember']['status'] = self::USER_STATUS_ACTIVE;
            return $this->save($team_member);
        }
        $data = [
            'user_id' => $userId,
            'team_id' => $teamId,
            'status'  => self::USER_STATUS_ACTIVE,
        ];

        Cache::delete($this->getCacheKey(CACHE_KEY_MEMBER_IS_ACTIVE, true, $userId), 'team_info');
        Cache::delete($this->getCacheKey(CACHE_KEY_TEAM_LIST, true, $userId, false), 'team_info');
        $this->create();
        return $this->save($data);
    }

    public function getAllMemberUserIdList(
        $with_me = true,
        $required_active = true,
        $required_evaluate = false,
        $teamId = null
    ) {
        $teamId = $teamId ?? $this->current_team_id;
        $options = [
            'conditions' => [
                'team_id' => $teamId
            ],
            'fields'     => ['user_id'],
        ];
        if ($required_active) {
            $options['conditions']['status'] = self::USER_STATUS_ACTIVE;
        }
        if ($required_evaluate) {
            $options['conditions']['evaluation_enable_flg'] = true;
        }
        if (!$with_me) {
            $options['conditions']['NOT']['user_id'] = $this->my_uid;
        }
        $res = $this->find('list', $options);
        return $res;
    }

    /**
     * Count charge target users
     *
     * @param int $teamId
     *
     * @return int
     */
    public function countChargeTargetUsers(int $teamId): int
    {
        /* get payment flag */
        $paymentTiming = new PaymentTiming();
        if ($paymentTiming->checkIfPaymentTiming($teamId)){
            $options = [
                'conditions' => [
                    'team_id' => $teamId,
                    'status'  => [
                        // self::USER_STATUS_INVITED,
                        self::USER_STATUS_ACTIVE,
                    ],
                ],
            ];
        } else {
            $options = [
                'conditions' => [
                    'team_id' => $teamId,
                    'status'  => [
                        self::USER_STATUS_INVITED,
                        self::USER_STATUS_ACTIVE,
                    ],
                ],
            ];

        }
        $cnt = (int)$this->find('count', $options);
        return $cnt;
    }

    /**
     * Count active and invited users
     *
     * @param int $teamId
     *
     * @return int
     */
    public function countHeadCount(int $teamId): int
    {
        $options = [
            'conditions' => [
                'team_id' => $teamId,
                'status'  => [
                    self::USER_STATUS_INVITED,
                    self::USER_STATUS_ACTIVE,
                ],
            ],
        ];
        $cnt = (int)$this->find('count', $options);
        return $cnt;
    }

    /**
     * Count active users
     *
     * @param int $teamId
     *
     * @return int
     */
    public function countActiveUsers(int $teamId): int
    {
        $options = [
            'conditions' => [
                'team_id' => $teamId,
                'status'  => [
                    // self::USER_STATUS_INVITED,
                    self::USER_STATUS_ACTIVE,
                ],
            ],
        ];
        $cnt = (int)$this->find('count', $options);
        return $cnt;
    }

    /**
     * Count invited users
     *
     * @param int $teamId
     *
     * @return int
     */
    public function countInvitedUsers(int $teamId): int
    {
        $options = [
            'conditions' => [
                'team_id' => $teamId,
                'status'  => [
                    self::USER_STATUS_INVITED,
                ],
            ],
        ];
        $cnt = (int)$this->find('count', $options);
        return $cnt;
    }

    /**
     * Count charge target users each team
     *
     * @param array $teamIds
     *
     * @return array
     * e.g.
     * TeamA(id:10)]: 5 charge target users.
     * TeamA(id:13)]: 6 charge target users.
     * return [10 => 5, 13 => 6];
     */
    public function countChargeTargetUsersEachTeam(array $teamIds): array
    {
        if (empty($teamIds)) {
            return [];
        }

        $options = [
            'fields'     => [
                'team_id',
                'COUNT(team_id) as cnt'
            ],
            'conditions' => [
                'team_id' => $teamIds,
                'status'  => [
                    // self::USER_STATUS_INVITED,
                    self::USER_STATUS_ACTIVE,
                ],
            ],
            'group'      => ['team_id']
        ];
        $res = $this->find('all', $options);
        if (empty($res)) {
            return [];
        }

        return Hash::combine($res, '{n}.TeamMember.team_id', '{n}.0.cnt');
    }

    public function setAdminUserFlag($member_id, $flag)
    {
        $this->deleteCacheMember($member_id);
        $this->id = $member_id;
        $flag = $flag == 'ON' ? 1 : 0;
        return $this->saveField('admin_flg', $flag);
    }

    /**
     * Activate taem member
     *
     * @param int $teamMemberId
     *
     * @return bool
     */
    public function activate(int $teamMemberId): bool
    {
        /** @var TransactionManager $TransactionManager */
        $TransactionManager = ClassRegistry::init("TransactionManager");
        try {
            $TransactionManager->begin();

            /** @var UserService $UserService */
            $UserService = ClassRegistry::init('UserService');

            $this->deleteCacheMember($teamMemberId);
            $this->id = $teamMemberId;

            // If user's default_team_id is set to null
            // user do not have teams to show on login.
            // Setting default_team_id to activated team.
            $user = $this->getUserById($teamMemberId);
            $activateUserId = $user['id'];
            $user = $this->User->getById($activateUserId);
            if (is_null($user['default_team_id'])) {
                $UserService->updateDefaultTeam($activateUserId, $this->current_team_id);
            }

            $result = (bool)$this->saveField('status', self::USER_STATUS_ACTIVE);
            if (!$result) {
                throw new RuntimeException('save failed on team_members.status');
            }
            $TransactionManager->commit();
            return $result;
        } catch (Exception $e) {
            GoalousLog::error("TeamMember activating failed", [
                'message' => $e->getMessage(),
            ]);
            GoalousLog::error($e->getTraceAsString());
            $TransactionManager->rollback();
            return false;
        }
    }

    /**
     * Inactivate taem member
     *
     * @param int $teamMemberId
     *
     * @return bool
     */
    public function inactivate(int $teamMemberId): bool
    {
        $this->deleteCacheMember($teamMemberId);
        $this->id = $teamMemberId;
        return (bool)$this->saveField('status', self::USER_STATUS_INACTIVE);
    }

    public function setEvaluationFlag($member_id, $flag)
    {
        $this->deleteCacheMember($member_id);
        $this->id = $member_id;
        $flag = $flag == 'ON' ? 1 : 0;
        return $this->saveField('evaluation_enable_flg', $flag);
    }

    /**
     * 対象ユーザのCache削除
     *
     * @param $member_id
     *
     * @return bool
     */
    function deleteCacheMember($member_id)
    {
        $this->id = $member_id;
        $member = $this->read();
        if (isset($member['TeamMember']['user_id'])) {
            Cache::delete($this->getCacheKey(CACHE_KEY_TEAM_LIST, true, $member['TeamMember']['user_id'], false),
                'team_info');
            Cache::delete($this->getCacheKey(CACHE_KEY_MY_MEMBER_STATUS, true, $member['TeamMember']['user_id']),
                'team_info');
            Cache::delete($this->getCacheKey(CACHE_KEY_MEMBER_IS_ACTIVE, true, $member['TeamMember']['user_id']),
                'team_info');

            return true;
        }
        return false;
    }

    /*
     * グループ別のメンバー取得
     */
    public function selectGroupMemberInfo($team_id, $group_id)
    {
        $options = $this->defineTeamMemberOption($team_id);
        if (empty($group_id) === false) {
            $user_id = $this->User->MemberGroup->getGroupMemberUserId($team_id, $group_id);
            $options['conditions']['user_id'] = $user_id;
        }
        return $this->convertMemberData($this->getAllMemberDetail($options));
    }

    /*
     * 2段階認証OFFのメンバーを取得
     */
    public function select2faStepMemberInfo($team_id)
    {
        $options = $this->defineTeamMemberOption($team_id);
        $options['contain']['User']['conditions']['User.2fa_secret'] = null;
        return $this->convertMemberData($this->getAllMemberDetail($options));
    }

    /*
     * チーム管理者取得
     */
    public function selectAdminMemberInfo($team_id)
    {
        $options = $this->defineTeamMemberOption($team_id);
        $options['conditions']['TeamMember.admin_flg'] = 1;
        return $this->convertMemberData($this->getAllMemberDetail($options));
    }

    /*
     * すべてのチームメンバー取得
     */
    public function selectMemberInfo($team_id)
    {
        $options = $this->defineTeamMemberOption($team_id);
        return $this->convertMemberData($this->getAllMemberDetail($options));
    }

    /*
     * チームページDefaultのオプション取得
     */
    public function defineTeamMemberOption($team_id)
    {
        $options = [
            'fields'     => ['id', 'status', 'admin_flg', 'coach_user_id', 'evaluation_enable_flg', 'created'],
            'conditions' => [
                'team_id' => $team_id,
            ],
            'order'      => ['TeamMember.created' => 'DESC'],
            'contain'    => [
                'User'      => [
                    'fields'      => ['id', 'first_name', 'last_name', '2fa_secret', 'photo_file_name'],
                    'MemberGroup' => [
                        'fields' => ['group_id'],
                        'Group'  => [
                            'fields' => ['name']
                        ]
                    ]
                ],
                'CoachUser' => [
                    'fields' => $this->User->profileFields
                ],
                'Email'     => [
                    'fields' => ['Email.id', 'Email.user_id', 'Email.email_verified']
                ]
            ]
        ];
        return $options;
    }

    public function convertMemberData($res)
    {
        $upload = new UploadHelper(new View());
        foreach ($res as $key => $tm_obj) {
            // グループ名の取得
            $group_name = [];
            foreach ($tm_obj['User']['MemberGroup'] as $g_obj) {
                if (isset($g_obj['Group']['name']) === true && empty($g_obj['Group']['name']) === false) {
                    $group_name[] = $g_obj['Group']['name'];
                }
            }

            $res[$key]['TeamMember']['group_name'] = '';
            if (count($group_name) > 0) {
                $res[$key]['TeamMember']['group_name'] = implode(',', $group_name);
            }

            // コーチ名を取得
            if (isset($res[$key]['CoachUser']['roman_username']) === true) {
                $res[$key]['search_coach_name_keyword'] = $res[$key]['CoachUser']['roman_username'];
                if (isset($tm_obj['CoachUser']['display_username']) === true) {
                    $res[$key]['TeamMember']['coach_name'] = $res[$key]['CoachUser']['display_username'];
                    $res[$key]['search_coach_name_keyword'] .= $res[$key]['CoachUser']['display_username'];
                }
            }

            // 2fa_secret: AngularJSで整数から始まるキーを読み取れないので別項目にて２段階認証設定表示を行う
            $res[$key]['User']['two_step_flg'] = is_null($tm_obj['User']['2fa_secret']) === true ? false : true;

            // メイン画像
            $res[$key]['User']['img_url'] = $upload->uploadUrl($tm_obj['User'], 'User.photo', ['style' => 'medium']);

            // ユーザー検索用キーワード作成
            $res[$key]['search_user_keyword'] = $tm_obj['User']['roman_username'];
            if (isset($tm_obj['User']['display_username']) === true) {
                $res[$key]['search_user_keyword'] .= $tm_obj['User']['display_username'];
            }
            //ユーザのリンク
            $url = Router::url([
                'controller' => 'users',
                'action'     => 'view_goals',
                'user_id'    => $tm_obj['User']['id'],
            ]);
            $res[$key]['User']['user_page_url'] = $url;
        }
        return $res;
    }

    /**
     * メンバー一覧の詳細なデータ取得
     * パフォーマンス向上の為にcontainを切り崩してデータをそれぞれ取ってマージしている
     * TODO ロジックが煩雑なため、後ほど、containの処理を見直す
     *
     * @param $options
     *
     * @return array|null
     */
    function getAllMemberDetail($options)
    {
        $contain = null;
        if (isset($options['contain'])) {
            $contain = $options['contain'];
            unset($options['contain']);
        }
        $options['fields'][] = 'user_id';
        $team_members = $this->find('all', $options);
        if (!$team_members) {
            return $team_members;
        }
        $user_ids = Hash::extract($team_members, '{n}.TeamMember.user_id');
        $coach_user_ids = Hash::extract($team_members, '{n}.TeamMember.coach_user_id');
        $team_members = Hash::combine($team_members, '{n}.TeamMember.user_id', '{n}');
        //コーチ情報をまとめて取得
        if (Hash::get($contain, 'CoachUser')) {
            $contain['CoachUser']['conditions']['id'] = $coach_user_ids;
            $coach_users = $this->User->find('all', $contain['CoachUser']);
            $coach_users = Hash::combine($coach_users, '{n}.User.id', '{n}');
        }
        //Email情報をまとめて取得
        if (Hash::get($contain, 'Email')) {
            $contain['Email']['conditions']['user_id'] = $user_ids;
            $user_emails = $this->User->Email->find('all', $contain['Email']);
            // if user changes email and does not verify it via the email,
            // his email_verified should be the anyone he got verified.
            $tmp = [];
            foreach ($user_emails as $email){
                $idKey = $email['Email']['user_id'];
                $verifiedValue = $email['Email']['email_verified'];
                if (isset($tmp[$idKey]) and $tmp[$idKey] == 1){
                    continue;
                }
                $tmp[$idKey] = $verifiedValue;
            }
            $user_emails = $tmp;
        }
        //ユーザ情報とグループ情報を取得して、ユーザ情報にマージ
        if (Hash::get($contain, 'User')) {
            //ユーザ情報を取得
            $group_options = Hash::get($contain, 'User.MemberGroup');
            unset($contain['User']['MemberGroup']);
            $contain['User']['conditions']['id'] = $user_ids;
            $users = $this->User->find('all', $contain['User']);
            $users = Hash::combine($users, '{n}.User.id', '{n}');
            if ($group_options) {
                //グループ情報をまとめて取得
                $group_options['conditions']['user_id'] = $user_ids;
                if (Hash::get($group_options, 'Group')) {
                    $group_options['contain']['Group'] = $group_options['Group'];
                    unset($group_options['Group']);
                }
                $group_options['fields'][] = 'user_id';
                $group_options['fields'][] = 'id';
                $member_groups = $this->Team->Group->MemberGroup->find('all', $group_options);
                $member_groups = Hash::combine($member_groups, '{n}.MemberGroup.id', '{n}', '{n}.MemberGroup.user_id');
            }
            //ユーザ情報にグループ情報をマージ
            foreach ($users as $user_id => $val) {
                $mg_res = [];
                if (isset($member_groups[$user_id])) {
                    foreach ($member_groups[$user_id] as $groups) {
                        $groups['MemberGroup']['Group'] = $groups['Group'];
                        $mg_res[] = $groups['MemberGroup'];
                    }
                }
                $users[$user_id]['User']['MemberGroup'] = $mg_res;
            }
        }
        //チームメンバー情報にユーザ情報をマージ
        foreach ($team_members as $user_id => $val) {
            if (isset($users[$user_id])) {
                $team_members[$user_id] = array_merge($team_members[$user_id], $users[$user_id]);
            } else {
                unset($team_members[$user_id]);
            }
        }
        //チームメンバー情報にコーチ情報をマージ
        foreach ($team_members as $user_id => $val) {
            $coach = [];
            if (viaIsSet($coach_users[$val['TeamMember']['coach_user_id']]['User'])) {
                $coach = $coach_users[$val['TeamMember']['coach_user_id']]['User'];
            }
            $team_members[$user_id]['CoachUser'] = $coach;
        }
        // チームメンバー情報からEmail未確認ユーザーを除外
        foreach ($team_members as $user_id => $val) {
            if (!Hash::get($user_emails, $user_id)) {
                unset($team_members[$user_id]);
            }
        }

        $res = array_values($team_members);

        return $res;
    }

    public function activateMembers($user_ids, $team_id = null)
    {
        $team_id = !$team_id ? $this->current_team_id : $team_id;
        return $this->updateAll(['TeamMember.status' => self::USER_STATUS_ACTIVE],
            ['TeamMember.team_id' => $team_id, 'TeamMember.user_id' => $user_ids]);
    }

    /**
     * @param array $member_numbers
     *
     * @return array|null [member_no => user_id,...]
     */
    function getUserIdsByMemberNos($member_numbers = [])
    {
        $options = [
            'conditions' => [
                'member_no' => $member_numbers
            ],
            'fields'     => [
                'member_no',
                'user_id',
            ]
        ];
        $res = $this->find('list', $options);
        return $res;
    }

    /**
     * update members from csv
     * return data as:
     * $res = [
     * 'error'         => false,
     * 'success_count' => 0,
     * 'error_line_no' => 0,
     * 'error_msg'     => null,
     * ];
     *
     * @param array $csv
     * @param       $term_id
     *
     * @return array
     */
    function updateFinalEvaluationFromCsv($csv, $term_id)
    {
        $res = [
            'error'         => false,
            'success_count' => 0,
            'error_line_no' => 0,
            'error_msg'     => null,
        ];
        $validate = $this->validateUpdateFinalEvaluationCsvData($csv, $term_id);
        if ($validate['error']) {
            return array_merge($res, $validate);
        }
        //get user ids
        $uids = $this->getUserIdsByMemberNos(Hash::extract($this->csv_datas, '{n}.member_no'));
        //get Evaluations
        $evaluations = $this->Team->Evaluation->getFinalEvaluations($term_id, $uids);
        $score_list = $this->Team->Evaluation->EvaluateScore->getScoreList($this->current_team_id);
        //prepare save data
        foreach ($this->csv_datas as $key => $row) {
            $row = Hash::expand($row);
            $save_data = [];
            $save_data['evaluate_score_id'] = array_keys($score_list, $row['total']['final']['score'])[0];
            $save_data['comment'] = $row['total']['final']['comment'];
            $save_data['evaluator_user_id'] = $this->my_uid;
            $save_data['status'] = Evaluation::TYPE_STATUS_DONE;
            $user_id = $uids[$row['member_no']];
            $evaluations[$user_id] = array_merge($evaluations[$user_id], $save_data);
        }
        //save evaluations
        $this->Team->Evaluation->saveAll($evaluations);
        $res['success_count'] = count($this->csv_datas);
        return $res;
    }

    /**
     * update members from csv
     * return data as:
     * $res = [
     * 'error'         => false,
     * 'success_count' => 0,
     * 'error_line_no' => 0,
     * 'error_msg'     => null,
     * ];
     *
     * @param array $csv
     *
     * @return array
     */
    function updateMembersFromCsv($csv)
    {
        $res = [
            'error'         => false,
            'success_count' => 0,
            'error_line_no' => 0,
            'error_msg'     => null,
        ];
        $validate = $this->validateUpdateMemberCsvData($csv);
        if ($validate['error']) {
            return array_merge($res, $validate);
        }
        //update process
        /**
         * メンバータイプ
         * メンバータイプを検索し、存在すればIDをセット。でなければメンバータイプを新規登録し、IDをセット
         */
        foreach ($this->csv_datas as $row_k => $row_v) {
            if (Hash::get($row_v, 'MemberType.name')) {
                $member_type = $this->MemberType->getByNameIfNotExistsSave($row_v['MemberType']['name']);
                $this->csv_datas[$row_k]['TeamMember']['member_type_id'] = $member_type['MemberType']['id'];
                unset($this->csv_datas[$row_k]['MemberType']);
            }
        }

        //update TeamMember
        foreach ($this->csv_datas as $k => $v) {
            //set TeamMember id
            $options = [
                'conditions' => ['email' => $v['Email']['email']],
                'fields'     => ['email'],
                'contain'    => [
                    'User' => [
                        'fields'     => ['id'],
                        'TeamMember' => [
                            'conditions' => ['team_id' => $this->current_team_id],
                            'fields'     => ['id']
                        ]
                    ]
                ]
            ];
            $user = $this->User->Email->find('first', $options);

            if (Hash::get($user, 'User')) {
                $this->csv_datas[$k]['User'] = $user['User'];
            }
            if (Hash::get($user, 'User.TeamMember.0.id')) {
                $this->csv_datas[$k]['TeamMember']['id'] = $user['User']['TeamMember'][0]['id'];
            } else {
                $this->create();
            }

            // 意図しないカラムの更新を防ぐために、明示的に更新するカラムを指定する
            $team_member_update_fields = array_keys($this->csv_datas[$k]['TeamMember']);
            $this->save($this->csv_datas[$k]['TeamMember'], true, $team_member_update_fields);
        }

        /**
         * グループ登録処理
         * グループが既に存在すれば、存在するIdをセット。でなければ、グループを新規登録し、IDをセット
         */
        //一旦グループ紐付けを解除
        $this->User->MemberGroup->deleteAll(['MemberGroup.team_id' => $this->current_team_id]);

        $member_groups = [];
        foreach ($this->csv_datas as $row_k => $row_v) {
            if (Hash::get($row_v, 'Group')) {
                foreach ($row_v['Group'] as $k => $v) {
                    $group = $this->User->MemberGroup->Group->getByNameIfNotExistsSave($v);
                    $member_groups[] = [
                        'group_id'  => $group['Group']['id'],
                        'index_num' => $k,
                        'team_id'   => $this->current_team_id,
                        'user_id'   => $row_v['User']['id'],
                    ];
                }
                unset($this->csv_datas[$row_k]['Group']);
            }
        }
        $this->User->MemberGroup->create();
        $this->User->MemberGroup->saveAll($member_groups);

        /**
         * コーチは最後に登録
         * コーチIDはメンバーIDを検索し、セット
         */
        foreach ($this->csv_datas as $row_k => $row_v) {
            if (!Hash::get($row_v, 'Coach')) {
                continue;
            }
            if ($coach_team_member = $this->getByMemberNo($row_v['Coach'])) {
                $this->id = $row_v['TeamMember']['id'];
                $team_member = $this->saveField('coach_user_id', $coach_team_member['TeamMember']['user_id']);
                $this->csv_datas[$row_k]['TeamMember']['coach_user_id'] = $team_member['TeamMember']['coach_user_id'];
            }
        }

        /**
         * 評価者は最後に登録
         * 評価者IDはメンバーIDを検索し、セット
         */
        //評価者紐付けを解除
        $this->Team->Evaluator->deleteAll(['Evaluator.team_id' => $this->current_team_id]);

        $save_evaluator_data = [];
        foreach ($this->csv_datas as $row_k => $row_v) {
            if (!Hash::get($row_v, 'Evaluator')) {
                continue;
            }
            foreach ($row_v['Evaluator'] as $r_k => $r_v) {
                if ($evaluator_team_member = $this->getByMemberNo($r_v)) {
                    $save_evaluator_data[] = [
                        'index_num'         => $r_k,
                        'team_id'           => $this->current_team_id,
                        'evaluatee_user_id' => $row_v['User']['id'],
                        'evaluator_user_id' => $evaluator_team_member['TeamMember']['user_id'],
                    ];
                }
            }
        }
        if (viaIsSet($save_evaluator_data)) {
            $this->Team->Evaluator->create();
            $this->Team->Evaluator->saveAll($save_evaluator_data);
        }

        $res['success_count'] = count($this->csv_datas);
        return $res;
    }

    function validateUpdateMemberCsvData($csv_data)
    {
        $this->_setCsvValidateRule(false);

        $res = [
            'error'         => true,
            'error_line_no' => 0,
            'error_msg'     => null,
        ];

        $before_csv_data = array_values($this->getAllMembersCsvData());
        $this->csv_datas = [];
        //emails
        $before_emails = array_column($before_csv_data, 'email');

        //レコード数が同一である事を確認
        if (count($csv_data) - 1 !== count($before_csv_data)) {
            $res['error_msg'] = __("Number of records do not match.");
            return $res;
        }
        //row validation
        foreach ($csv_data as $key => $row) {
            //set line no
            $res['error_line_no'] = $key + 1;

            //key name set
            if (!($row = copyKeyName($this->_getCsvHeading(), $row))) {
                $res['error_msg'] = __("Numbers are not consistent.");
                return $res;
            }
            if ($key === 0) {
                if (!empty(array_diff($row, $this->_getCsvHeading()))) {
                    $res['error_msg'] = __("Headding are not consistent.");
                    return $res;
                }
                continue;
            }
            $row = Hash::expand($row);
            $this->set($row);
            if (!$this->validates()) {
                $res['error_msg'] = current(array_shift($this->validationErrors));
                return $res;
            }

            $this->csv_emails[] = $row['email'];
            $this->csv_datas[$key]['Email'] = ['email' => $row['email']];

            $before_record = $before_csv_data[array_search($row['email'], $before_emails)];

            //first name check
            if ($row['first_name'] != $before_record['first_name']) {
                $res['error_msg'] = __("You can't change first name.");
                return $res;
            }
            //last name check
            if ($row['last_name'] != $before_record['last_name']) {
                $res['error_msg'] = __("You can't change last name.");
                return $res;
            }
            $this->csv_member_ids[] = $row['member_no'];
            $this->csv_datas[$key]['TeamMember']['member_no'] = $row['member_no'];
            $this->csv_datas[$key]['TeamMember']['status'] = strtolower($row['status']) == "on" ? self::USER_STATUS_ACTIVE : self::USER_STATUS_INACTIVE;
            $this->csv_datas[$key]['TeamMember']['admin_flg'] = strtolower($row['admin_flg']) == 'on' ? true : false;
            $this->csv_datas[$key]['TeamMember']['evaluation_enable_flg'] = strtolower($row['evaluation_enable_flg']) == 'on' ? true : false;
            if (Hash::get($row, 'member_type')) {
                $this->csv_datas[$key]['MemberType']['name'] = $row['member_type'];
            } else {
                $this->csv_datas[$key]['TeamMember']['member_type_id'] = null;
            }
            //Group
            foreach ($row['group'] as $v) {
                if (viaIsSet($v)) {
                    $this->csv_datas[$key]['Group'][] = $v;
                }
            }
            //exists check (after check)
            $this->csv_coach_ids[] = $row['coach_member_no'];
            if (Hash::get($row, 'coach_member_no')) {
                $this->csv_datas[$key]['Coach'] = $row['coach_member_no'];
            } else {
                $this->csv_datas[$key]['TeamMember']['coach_user_id'] = null;
            }

            //Evaluator ID
            //duplicate evaluator check.
            $filtered_evaluators = array_filter($row['evaluator_member_no'], "strlen");
            foreach ($row['evaluator_member_no'] as $v) {
                if (viaIsSet($v)) {
                    $this->csv_datas[$key]['Evaluator'][] = $v;
                }
            }
            //evaluator id check(after check)
            $this->csv_evaluator_ids[] = $filtered_evaluators;
        }
        //require least 1 or more admin and active check
        $exists_admin_active = false;
        foreach ($this->csv_datas as $k => $v) {
            if ($v['TeamMember']['admin_flg'] && $v['TeamMember']['status'] == self::USER_STATUS_ACTIVE) {
                $exists_admin_active = true;
            }
        }
        if (!$exists_admin_active) {
            $res['error_line_no'] = 0;
            $res['error_msg'] = __("At least one active admin is required.");
            return $res;
        }

        //email exists check
        //E-mail address should not be duplicated
        if (count($this->csv_emails) != count(array_unique($this->csv_emails))) {
            $duplicate_emails = array_filter(array_count_values($this->csv_emails), 'isOver2');
            $duplicate_email = key($duplicate_emails);
            //set line no
            $res['error_line_no'] = array_search($duplicate_email, $this->csv_emails) + 2;
            $res['error_msg'] = __("Duplicated email address.");
            return $res;
        }
        //member id duplicate check
        if (count($this->csv_member_ids) != count(array_unique($this->csv_member_ids))) {
            $duplicate_member_ids = array_filter(array_count_values($this->csv_member_ids), 'isOver2');
            $duplicate_member_id = key($duplicate_member_ids);
            //set line no
            $res['error_line_no'] = array_search($duplicate_member_id, $this->csv_member_ids) + 2;
            $res['error_msg'] = __("Duplicated member ID.");
            return $res;
        }
        //coach id check
        $this->csv_coach_ids = array_filter($this->csv_coach_ids, "strlen");
        //Error if the unregistered coach is not included in the member ID
        foreach ($this->csv_coach_ids as $k => $v) {
            $key = array_search($v, $this->csv_member_ids);
            if ($key === false) {
                $res['error_line_no'] = $k + 2;
                $res['error_msg'] = __("Invalid member ID set in coach ID.");
                return $res;
            }
        }
        //evaluator id check
        //Evaluator ID must be already been registered or must be included in the member ID
        //remove empty elements
        foreach ($this->csv_evaluator_ids as $k => $v) {
            $this->csv_evaluator_ids[$k] = array_filter($v, "strlen");
        }

        //Merge all evaluator ID
        $merged_evaluator_ids = [];
        foreach ($this->csv_evaluator_ids as $v) {
            $merged_evaluator_ids = array_merge($merged_evaluator_ids, $v);
        }
        //Error if the unregistered evaluator ID is not included in the member ID
        foreach ($this->csv_evaluator_ids as $r_k => $r_v) {
            foreach ($r_v as $k => $v) {
                $key = array_search($v, $this->csv_member_ids);
                if ($key === false) {
                    $res['error_line_no'] = $r_k + 2;
                    $res['error_msg'] = __("Invalid member ID set in evaluator ID.");
                    return $res;
                }
            }
        }
        $res['error'] = false;
        $this->_setValidateFromBackUp();
        return $res;
    }

    function validateUpdateFinalEvaluationCsvData($csv_data, $term_id)
    {
        $this->_setCsvValidateRuleFinalEval();

        $res = [
            'error'         => true,
            'error_line_no' => 0,
            'error_msg'     => null,
        ];

        $before_csv_data = $this->getAllEvaluationsCsvData($term_id);
        $this->csv_datas = [];
        //member_no
        $before_member_numbers = array_column($before_csv_data, 'member_no');
        //レコード数が同一である事を確認
        if (count($csv_data) - 1 !== count($before_csv_data)) {
            $res['error_msg'] = __("Number of records do not match.");
            return $res;
        }
        $score_list = $this->Team->Evaluation->EvaluateScore->getScoreList($this->current_team_id);
        //row validation
        foreach ($csv_data as $key => $row) {
            //set line no
            $res['error_line_no'] = $key + 1;

            //key name set
            if (!($row = copyKeyName($this->_getCsvHeadingEvaluation(), $row))) {
                $res['error_msg'] = __("Numbers are not consistent.");
                return $res;
            }
            if ($key === 0) {
                if (!empty(array_diff($row, $this->_getCsvHeadingEvaluation()))) {
                    $res['error_msg'] = __("Headding are not consistent.");
                    return $res;
                }
                continue;
            }
            $this->set($row);
            if (!$this->validates()) {
                $res['error_msg'] = current(array_shift($this->validationErrors));
                return $res;
            }

            //member_no exists check
            if (!in_array($row['member_no'], $before_member_numbers)) {
                $res['error_msg'] = __("Invalid member ID.");
                return $res;
            }

            //score check
            if (!in_array($row['total.final.score'], $score_list)) {
                $res['error_msg'] = __("Invalid score.");
                return $res;
            }

            $this->csv_datas[] = $row;
            $this->csv_member_ids[] = $row['member_no'];
        }
        //member id duplicate check
        if (count($this->csv_member_ids) != count(array_unique($this->csv_member_ids))) {
            $duplicate_member_ids = array_filter(array_count_values($this->csv_member_ids), 'isOver2');
            $duplicate_member_id = key($duplicate_member_ids);
            //set line no
            $res['error_line_no'] = array_search($duplicate_member_id, $this->csv_member_ids) + 2;
            $res['error_msg'] = __("Duplicated member ID.");
            return $res;
        }
        $res['error'] = false;
        $this->_setValidateFromBackUp();
        return $res;
    }

    function getByMemberNo($member_no, $team_id = null)
    {
        if (!$team_id) {
            $team_id = $this->current_team_id;
        }
        $options = [
            'conditions' => [
                'team_id'   => $team_id,
                'member_no' => $member_no
            ],
        ];
        $res = $this->find('first', $options);
        return $res;
    }

    /**
     * @param      $user_id
     * @param null $team_id
     *
     * @return array|null
     * @deprecated
     * Don't use AppMode.current_team_id/my_uid
     * $user_id をキーにしてチームメンバー情報を取得
     */
    function getByUserId($user_id, $team_id = null)
    {
        if (!$team_id) {
            $team_id = $this->current_team_id;
        }
        $options = [
            'conditions' => [
                'team_id' => $team_id,
                'user_id' => $user_id,
            ],
            'contain'    => [
                'User',
            ]
        ];
        $res = $this->find('first', $options);
        return $res;
    }

    /**
     * Instead of getByUserId method
     *
     * @param int $userId
     * @param int $teamId
     *
     * @return array|null
     */
    function getUnique(int $userId, int $teamId): array
    {
        $options = [
            'conditions' => [
                'team_id' => $teamId,
                'user_id' => $userId,
            ],
        ];
        $res = $this->useType()->find('first', $options);
        return Hash::get($res, 'TeamMember') ?? [];
    }

    function getAllMembersCsvData($team_id = null)
    {
        if (!$team_id) {
            $team_id = $this->current_team_id;
        }

        $this->setAllMembers($team_id);
        //convert csv data
        foreach ($this->all_users as $k => $v) {
            if (!Hash::get($v, 'User.id')) {
                unset($this->all_users[$k]);
                continue;
            }
            if (Hash::get($v, 'TeamMember.status') == Enum\Model\TeamMember\Status::INVITED) {
                continue;
            }
            $this->csv_datas[$k]['email'] = Hash::get($v,
                'User.PrimaryEmail.email') ? $v['User']['PrimaryEmail']['email'] : null;
            $this->csv_datas[$k]['first_name'] = Hash::get($v, 'User.first_name') ? $v['User']['first_name'] : null;
            $this->csv_datas[$k]['last_name'] = Hash::get($v, 'User.last_name') ? $v['User']['last_name'] : null;
            $this->csv_datas[$k]['member_no'] = Hash::get($v,
                'TeamMember.member_no') ? $v['TeamMember']['member_no'] : null;
            $this->csv_datas[$k]['status'] = Hash::get($v,
                'TeamMember.status') == self::USER_STATUS_ACTIVE ? 'ON' : 'OFF';
            $this->csv_datas[$k]['admin_flg'] = Hash::get($v,
                'TeamMember.admin_flg') && $v['TeamMember']['admin_flg'] ? 'ON' : 'OFF';
            $this->csv_datas[$k]['evaluation_enable_flg'] = Hash::get($v,
                'TeamMember.evaluation_enable_flg') && $v['TeamMember']['evaluation_enable_flg'] ? 'ON' : 'OFF';
            $this->csv_datas[$k]['member_type'] = Hash::get($v, 'MemberType.name') ? $v['MemberType']['name'] : null;
            //group
            if (Hash::get($v, 'User.MemberGroup')) {
                foreach ($v['User']['MemberGroup'] as $g_k => $g_v) {
                    $key_index = $g_k + 1;
                    $this->csv_datas[$k]['group.' . $key_index] = Hash::get($g_v,
                        'Group.name') ? $g_v['Group']['name'] : null;
                }
            }
        }

        $this->setCoachNumberForCsvData($team_id);
        $this->setEvaluatorNumberForCsvData($team_id);
        $this->addDefaultSellForCsvData('before_update');

        return $this->csv_datas;
    }

    function setCoachNumberForCsvData($team_id)
    {
        foreach ($this->all_users as $k => $v) {
            if (!Hash::get($v, 'TeamMember.coach_user_id')) {
                continue;
            }
            $options = [
                'conditions' => ['team_id' => $team_id, 'user_id' => $v['TeamMember']['coach_user_id']],
                'fields'     => ['member_no']
            ];
            $coach_member = $this->find('first', $options);
            $this->csv_datas[$k]['coach_member_no'] = Hash::get($coach_member,
                'TeamMember.member_no') ? $coach_member['TeamMember']['member_no'] : null;
        }
        return;
    }

    function setEvaluatorNumberForCsvData($team_id)
    {
        foreach ($this->all_users as $k => $v) {
            $options = [
                'conditions' => ['team_id' => $team_id, 'evaluatee_user_id' => $v['User']['id']],
                'fields'     => ['evaluator_user_id'],
                'contain'    => [
                    'EvaluatorUser' => [
                        'fields'     => ['id'],
                        'TeamMember' => [
                            'conditions' => ['team_id' => $team_id],
                            'fields'     => ['member_no']
                        ],
                    ]
                ],
                'order'      => 'index_num ASC'
            ];
            $evaluators = $this->Team->Evaluator->find('all', $options);
            foreach ($evaluators as $r_k => $r_v) {
                $key_index = $r_k + 1;
                if (Hash::get($r_v, 'EvaluatorUser.TeamMember.0.member_no')) {
                    $this->csv_datas[$k]['evaluator_member_no.' . $key_index] = $r_v['EvaluatorUser']['TeamMember'][0]['member_no'];
                }
            }
        }
        return;
    }

    function setAllMembers($team_id = null, $type = 'before_update', $term_id = null)
    {
        if (!$team_id) {
            $team_id = $this->current_team_id;
        }

        $options = [
            'conditions' => [
                'TeamMember.team_id' => $team_id,
            ],
            'fields'     => ['member_no', 'coach_user_id', 'status', 'admin_flg', 'evaluation_enable_flg'],
            'order'      => ['TeamMember.member_no ASC'],
            'contain'    => [
                'User'       => [
                    'fields' => $this->User->profileFields,
                ],
                'MemberType' => [
                    'fields' => ['name']
                ],
                'CoachUser'  => [
                    'fields' => $this->User->profileFields,
                ]
            ]
        ];
        switch ($type) {
            case 'before_update':
                $options['contain']['User'] = [
                    'fields'       => ['first_name', 'last_name'],
                    'MemberGroup'  => [
                        'conditions' => ['MemberGroup.team_id' => $team_id],
                        'fields'     => ['group_id'],
                        'Group'      => [
                            'fields' => ['name']
                        ]
                    ],
                    'PrimaryEmail' => [
                        'fields' => ['email'],
                    ],
                    'Email'        => [
                        'fields' => ['email_verified']
                    ]
                ];
                break;
            case 'final_evaluation':
                $uids = $this->Team->Evaluation->getEvaluateeIdsByTermId($term_id);
                $options['conditions'] += [
                    'TeamMember.user_id' => $uids,
                ];
                $options['contain']['User']['Email'] = [
                    'fields' => ['email_verified']
                ];
                break;
        }

        // exclude email unverified user
        $all_users_include_unverified_user = $this->find('all', $options);
        $all_users = [];
        foreach ($all_users_include_unverified_user as $key => $user) {
            if (Hash::get($user, 'User.Email.0.email_verified')) {
                unset($user['User']['Email']);
                $all_users[] = $user;
            }
        }
        $this->all_users = $all_users;
        return;
    }

    function setUserInfoForCsvData()
    {
        foreach ($this->all_users as $k => $v) {
            if (!Hash::get($v, 'User.id')) {
                unset($this->all_users[$k]);
                continue;
            }

            $this->csv_datas[$k]['member_no'] = Hash::get($v,
                'TeamMember.member_no') ? $v['TeamMember']['member_no'] : null;
            $this->csv_datas[$k]['member_type'] = Hash::get($v, 'MemberType.name') ? $v['MemberType']['name'] : null;
            $this->csv_datas[$k]['user_name'] = Hash::get($v,
                'User.display_username') ? $v['User']['display_username'] : null;
            $this->csv_datas[$k]['coach_user_name'] = Hash::get($v,
                'CoachUser.display_username') ? $v['CoachUser']['display_username'] : null;
        }
        return;
    }

    function setEvaluations($term_id)
    {
        $this->evaluations = $this->Team->Evaluation->getAllEvaluations($term_id);
        return;
    }

    function setGoalEvaluationForCsvData()
    {
        /**
         * @var Goal $Goal
         */
        $Goal = ClassRegistry::init('Goal');
        $goal_ids = [];
        foreach ($this->all_users as $k => $v) {
            if (isset($this->evaluations[$v['User']['id']])) {
                $goals = Hash::combine($this->evaluations[$v['User']['id']], '{n}.Evaluation.id',
                    '{n}.Evaluation.goal_id',
                    '{n}.Evaluation.goal_id');
                unset($goals[0]);
                //set goal_count
                $this->csv_datas[$k]['goal_count'] = count($goals);
                $goal_ids[$v['User']['id']] = array_keys($goals);
            }
        }
        //set kr_count and action count
        foreach ($this->all_users as $k => $v) {
            if (!isset($goal_ids[$v['User']['id']]) || empty($goal_ids[$v['User']['id']])) {
                $this->csv_datas[$k]['kr_count'] = 0;
                $this->csv_datas[$k]['action_count'] = 0;
                $this->csv_datas[$k]['goal_progress'] = 0;
                continue;
            }
            $kr_count = $Goal->KeyResult->getKrCount($goal_ids[$v['User']['id']]);
            $this->csv_datas[$k]['kr_count'] = $kr_count;
            $action_count = $Goal->ActionResult->getActionCount($goal_ids[$v['User']['id']], $v['User']['id']);
            $this->csv_datas[$k]['action_count'] = $action_count;
            //goal_progress()
            $all_goal_progress = $Goal->getAllUserGoalProgress($goal_ids[$v['User']['id']], $v['User']['id']);
            $this->csv_datas[$k]['goal_progress'] = $all_goal_progress;
        }
        return;
    }

    function setTotalSelfEvaluationForCsvData()
    {
        foreach ($this->all_users as $k => $v) {
            if (!Hash::get($this->evaluations, Hash::get($v, 'User.id'))) {
                continue;
            }
            foreach ($this->evaluations[$v['User']['id']] as $eval) {
                if ($eval['Evaluation']['evaluate_type'] == Evaluation::TYPE_ONESELF && empty($eval['Evaluation']['goal_id'])) {
                    $this->csv_datas[$k]['total.self.score'] = $eval['EvaluateScore']['name'];
                    $this->csv_datas[$k]['total.self.comment'] = $eval['Evaluation']['comment'];
                }
            }
        }
        return;
    }

    function setTotalEvaluatorEvaluationForCsvData()
    {
        foreach ($this->all_users as $k => $v) {
            if (!Hash::get($this->evaluations, Hash::get($v, 'User.id'))) {
                continue;
            }
            $ek = 1;
            foreach ($this->evaluations[$v['User']['id']] as $eval) {
                if ($eval['Evaluation']['evaluate_type'] == Evaluation::TYPE_EVALUATOR && empty($eval['Evaluation']['goal_id'])) {
                    $this->csv_datas[$k]["total.evaluator.$ek.name"] = $eval['EvaluatorUser']['display_username'];
                    $this->csv_datas[$k]["total.evaluator.$ek.score"] = $eval['EvaluateScore']['name'];
                    $this->csv_datas[$k]["total.evaluator.$ek.comment"] = $eval['Evaluation']['comment'];
                    $ek++;
                }
            }
        }
        return;
    }

    function setTotalFinalEvaluationForCsvData()
    {
        foreach ($this->all_users as $k => $v) {
            if (!Hash::get($this->evaluations, Hash::get($v, 'User.id'))) {
                continue;
            }
            foreach ($this->evaluations[$v['User']['id']] as $eval) {
                if ($eval['Evaluation']['evaluate_type'] == Evaluation::TYPE_FINAL_EVALUATOR && empty($eval['Evaluation']['goal_id'])) {
                    $this->csv_datas[$k]["total.final.score"] = $eval['EvaluateScore']['name'];
                    $this->csv_datas[$k]["total.final.comment"] = $eval['Evaluation']['comment'];
                }
            }
        }
        return;
    }

    function addDefaultSellForCsvData($type = 'before_update')
    {
        switch ($type) {
            case 'before_update':
                $default_csv = $this->_getCsvHeading();
                break;
            case 'evaluation':
                $default_csv = $this->_getCsvHeadingEvaluation();
                break;
            default:
                return;
        }
        foreach ($default_csv as $k => $v) {
            $default_csv[$k] = null;
        }
        foreach ($this->csv_datas as $k => $v) {
            $this->csv_datas[$k] = Hash::merge($default_csv, $v);
        }
        return;
    }

    /**
     * @param      $term_id
     * @param null $team_id
     *
     * @return array
     */
    function getAllEvaluationsCsvData($term_id, $team_id = null)
    {
        $this->setAllMembers($team_id, 'final_evaluation', $term_id);
        $this->setEvaluations($term_id);
        $this->setUserInfoForCsvData();
        $this->setGoalEvaluationForCsvData();
        $this->setTotalSelfEvaluationForCsvData();
        $this->setTotalEvaluatorEvaluationForCsvData();
        $this->setTotalFinalEvaluationForCsvData();
        $this->addDefaultSellForCsvData('evaluation');

        return $this->csv_datas;
    }

    /**
     * get CSV heading
     *
     * @param bool $new
     *
     * @return array
     */
    function _getCsvHeading()
    {
        return [
            'email'                 => __("Email(*, Not changed)"),
            'first_name'            => __("First Name(*, Not changed)"),
            'last_name'             => __("Last Name(*, Not changed)"),
            'member_no'             => __("Member ID(*)"),
            'status'                => __("Member active status(*)"),
            'admin_flg'             => __("Administrator(*)"),
            'evaluation_enable_flg' => __("Evaluated(*)"),
            'member_type'           => __("Member Type"),
            'group.1'               => __("Group 1"),
            'group.2'               => __("Group 2"),
            'group.3'               => __("Group 3"),
            'group.4'               => __("Group 4"),
            'group.5'               => __("Group 5"),
            'group.6'               => __("Group 6"),
            'group.7'               => __("Group 7"),
            'coach_member_no'       => __("Coach ID"),
            'evaluator_member_no.1' => __("Evaluator 1"),
            'evaluator_member_no.2' => __("Evaluator 2"),
            'evaluator_member_no.3' => __("Evaluator 3"),
            'evaluator_member_no.4' => __("Evaluator 4"),
            'evaluator_member_no.5' => __("Evaluator 5"),
            'evaluator_member_no.6' => __("Evaluator 6"),
            'evaluator_member_no.7' => __("Evaluator 7"),
        ];

    }

    function _getCsvHeadingEvaluation()
    {

        $record = [
            'member_no'          => __("Member ID(*)"),
            'member_type'        => __("Member Type"),
            'user_name'          => __("Member name"),
            'coach_user_name'    => __("Coach name"),
            'goal_count'         => __("Number of goals"),
            'kr_count'           => __("Number of key results"),
            'action_count'       => __("Number of actions"),
            'goal_progress'      => __("Progress(%)"),
            'total.self.score'   => __('Score by him/herself'),
            'total.self.comment' => __('Comment by him/herself'),
        ];
        //evaluator
        for ($ek = 1; $ek <= self:: MAX_NUMBER_OF_EVALUATORS; $ek++) {
            $record["total.evaluator.$ek.name"] = __('Name of evaluator %s', $ek);
            $record["total.evaluator.$ek.score"] = __('Score of evaluator %s', $ek);
            $record["total.evaluator.$ek.comment"] = __('Comment by evaluator %s', $ek);
        }

//final
        $record["total.final.score"] = __('Score by final evaluator');
        $record["total.final.comment"] = __('Comment by final evaluator');

        return $record;
    }

    function _setCsvValidateRule($new = true)
    {
        $common_validate = [
            'email'                 => [
                'notBlank' => [
                    'rule'    => 'notBlank',
                    'message' => __("%s is required.", __("Email Address"))
                ],
                'email'    => [
                    'rule'    => ['email'],
                    'message' => __("%s is not correct.", __("Email Address"))
                ],
            ],
            'member_no'             => [
                'notBlank'        => [
                    'rule'    => 'notBlank',
                    'message' => __("%s is required.", __("Member ID"))
                ],
                'maxLength'       => [
                    'rule'    => ['maxLength', 64],
                    'message' => __("%s should be entered in less than 64 characters.", __("Member ID"))
                ],
                'isNotExistArray' => [
                    'rule'       => ['isNotExistArray', 'evaluator_member_no'],
                    'message'    => __("%s doesn't allow you to specify the ID of you.", __("Evaluator ID")),
                    'allowEmpty' => true,
                ],
            ],
            'first_name'            => [
                'maxLength'    => [
                    'rule'    => ['maxLength', 64],
                    'message' => __("%s should be entered in less than 64 characters.", __("First Name"))
                ],
                'notBlank'     => [
                    'rule'    => 'notBlank',
                    'message' => __("%s is required.", __("First Name"))
                ],
                'userNameChar' => ['rule' => ['userNameChar']],
            ],
            'last_name'             => [
                'maxLength'    => [
                    'rule'    => ['maxLength', 64],
                    'message' => __("%s should be entered in less than 64 characters.", __("Last Name"))
                ],
                'notBlank'     => [
                    'rule'    => 'notBlank',
                    'message' => __("%s is required.", __("Last Name"))
                ],
                'userNameChar' => ['rule' => ['userNameChar']],
            ],
            'admin_flg'             => [
                'notBlank'  => [
                    'rule'    => 'notBlank',
                    'message' => __("%s is required.", __("Administrators"))
                ],
                'isOnOrOff' => [
                    'rule'    => 'isOnOrOff',
                    'message' => __("%s must be either 'ON' or 'OFF'.", __('Administrators'))
                ],
            ],
            'evaluation_enable_flg' => [
                'notBlank'  => [
                    'rule'    => 'notBlank',
                    'message' => __("%s is required.", __("Evaluator"))
                ],
                'isOnOrOff' => [
                    'rule'    => 'isOnOrOff',
                    'message' => __("%s must be either 'ON' or 'OFF'.", __('Evaluator'))
                ],
            ],
            'member_type'           => [
                'maxLength' => [
                    'rule'    => ['maxLength', 64],
                    'message' => __("%s should be entered in less than 64 characters.", __("Member Type"))
                ],
            ],
            'group'                 => [
                'isAlignLeft'     => [
                    'rule'       => 'isAlignLeft',
                    'message'    => __("Please %s fill with align left.", __("Group name")),
                    'allowEmpty' => true,
                ],
                'isNotDuplicated' => [
                    'rule'       => 'isNotDuplicated',
                    'message'    => __("%s is duplicated.", __("Group name")),
                    'allowEmpty' => true,
                ],
                'maxLengthArray'  => [
                    'rule'       => ['maxLengthArray', 64],
                    'message'    => __("%s should be entered in less than 64 characters.", __("Group name")),
                    'allowEmpty' => true,
                ],
            ],
            'coach_member_no'       => [
                'isNotEqual' => [
                    'rule'       => ['isNotEqual', 'member_no'],
                    'message'    => __("%s doesn't allow you to specify the ID of you.", __("Coach ID")),
                    'allowEmpty' => true,
                ],
            ],
            'evaluator_member_no'   => [
                'isAlignLeft'     => [
                    'rule'       => 'isAlignLeft',
                    'message'    => __("Please %s fill with align left.", __("Evaluator")),
                    'allowEmpty' => true,
                ],
                'isNotDuplicated' => [
                    'rule'       => 'isNotDuplicated',
                    'message'    => __("%s is duplicated.", __("Evaluator")),
                    'allowEmpty' => true,
                ],
                'maxLengthArray'  => [
                    'rule'       => ['maxLengthArray', 64],
                    'message'    => __("%s should be entered in less than 64 characters.", __("Evaluator")),
                    'allowEmpty' => true,
                ],
            ],
        ];
        $validateOfUpdate = [
            'status' => [
                'notBlank'  => [
                    'rule'    => 'notBlank',
                    'message' => __("%s is required.", __("Active status"))
                ],
                'isOnOrOff' => [
                    'rule'    => 'isOnOrOff',
                    'message' => __("%s must be either 'ON' or 'OFF'.", __('Active status'))
                ],
            ],
        ];
        $validate = $common_validate + $validateOfUpdate;
        $this->validateBackup = $this->validate;
        $this->validate = $validate;
    }

    function _setCsvValidateRuleFinalEval()
    {
        //TODO ルール設定まだしてない
        $validate_rules = [
            'total.final.score' => [
                'notBlank' => [
                    'rule'    => 'notBlank',
                    'message' => __("%s is required.", __("Score by final evaluator"))
                ],
            ],
        ];
        $this->validateBackup = $this->validate;
        $this->validate = $validate_rules;
    }

    function _setValidateFromBackUp()
    {
        $this->validate = $this->validateBackup;
    }

    /**
     * ログインしているユーザーのコーチIDを取得する
     *
     * @param $user_id
     *
     * @return int|null
     */
    function getCoachUserIdByMemberUserId($user_id = null)
    {
        if (!$user_id) {
            $user_id = $this->my_uid;
        }
        // 検索テーブル: team_members
        // 取得カラム: coach_user_id
        // 条件: user_id, team_id
        $options = [
            'fields'     => ['coach_user_id', 'coach_user_id'],
            'conditions' => [
                'user_id' => $user_id,
            ],
        ];
        $res = $this->find('list', $options);
        return array_pop($res);
    }

    /**
     * ログインしているユーザーが管理するのメンバーIDを取得する
     *
     * @param $user_id
     *
     * @return array|null
     */
    function getMyMembersList($user_id)
    {
        if (!$user_id) {
            $user_id = $this->my_uid;
        }
        // 検索テーブル: team_members
        // 取得カラム: user_id
        // 条件: coach_user_id = パラメータ1 team_id = パラメータ2
        $options = [
            'fields'     => ['user_id'],
            'conditions' => [
                'TeamMember.coach_user_id' => $user_id,
                'status'                   => self::USER_STATUS_ACTIVE,
                'evaluation_enable_flg'    => 1,
            ],
        ];
        return $this->find('list', $options);
    }

    function getTeamAdminUid()
    {
        $options = [
            'conditions' => [
                'team_id'   => $this->current_team_id,
                'admin_flg' => true
            ]
        ];
        $res = $this->find('first', $options);
        if (Hash::get($res, 'TeamMember.id')) {
            return $res['TeamMember']['id'];
        }
        return null;
    }

    function getLoginUserAdminFlag($team_id, $user_id)
    {
        $options = [
            'conditions' => [
                'team_id' => $team_id,
                'user_id' => $user_id,
            ]
        ];
        $res = $this->find('first', $options);
        if (isset($res['TeamMember']['admin_flg']) === true) {
            return $res['TeamMember']['admin_flg'];
        }
        return false;
    }

    function getAdminUserCount($team_id)
    {
        $options = [
            'conditions' => [
                'team_id'   => $team_id,
                'admin_flg' => 1
            ]
        ];
        return $this->find('count', $options);
    }

    /**
     * マイメンバーのゴールを取得する
     *
     * @param      $user_id
     * @param null $limit
     * @param int  $page
     *
     * @return array|null
     */
    function getCoachingGoalList($user_id, $limit = null, $page = 1)
    {
        $options = [
            'conditions' => [
                'coach_user_id' => $user_id,
                'team_id'       => $this->current_team_id,
            ],
            'fields'     => [
                'user_id'
            ],
        ];
        $member_list = $this->find("list", $options);
        $res = $this->User->GoalMember->getCollaboGoalList($member_list, true, $limit, $page);
        return $res;
    }

    /**
     * Param1のユーザーは評価対象の人なのか
     *
     * @param $user_id
     *
     * @return boolean
     */
    function getEvaluationEnableFlg($user_id)
    {
        $options = [
            'fields'     => ['status', 'evaluation_enable_flg'],
            'conditions' => [
                'TeamMember.user_id' => $user_id,
            ],
        ];
        $res = $this->find('first', $options);
        $evaluation_flg = false;
        if (isset($res['TeamMember']['status'])
            && $res['TeamMember']['status'] == self::USER_STATUS_ACTIVE
            && isset($res['TeamMember']['evaluation_enable_flg']) === true
            && $res['TeamMember']['evaluation_enable_flg'] === true
        ) {
            $evaluation_flg = true;
        }

        return $evaluation_flg;
    }

    function getCoachId($user_id, $team_id = null)
    {
        $team_id = empty($team_id) ? $this->current_team_id : $team_id;
        $options = [
            'conditions' => [
                'TeamMember.user_id' => $user_id,
                'TeamMember.team_id' => $team_id,
            ],
            'fields'     => ['coach_user_id'],
        ];
        $res = $this->find('first', $options);
        return Hash::get($res, 'TeamMember.coach_user_id') ? $res['TeamMember']['coach_user_id'] : null;
    }

    /**
     * $team_id のチームに所属するアクティブメンバー数を返す
     *
     * @param $team_id
     *
     * @return array
     */
    function countActiveMembersByTeamId($team_id)
    {
        return $this->find('count', [
            'conditions' => [
                'TeamMember.team_id' => $team_id,
                'TeamMember.status'  => self::USER_STATUS_ACTIVE,
            ],
        ]);
    }

    function getIdByTeamAndUserId($team_id, $user_id)
    {
        $team_member = $this->find('first', [
            'conditions' => [
                'TeamMember.team_id' => $team_id,
                'TeamMember.user_id' => $user_id,
            ],
            'fields'     => ['id']
        ]);
        if ($team_member_id = Hash::get($team_member, 'TeamMember.id')) {
            return $team_member_id;
        }
        return null;
    }

    function getAllTeam($user_id, $reformat_for_shell = false)
    {
        $options = [
            'conditions' => [
                'TeamMember.user_id' => $user_id,
            ],
            'fields'     => [
                'TeamMember.team_id',
                'TeamMember.user_id',
                'Team.name',
                'TeamMember.status',
                'TeamMember.admin_flg',
            ],
            'contain'    => ['Team']
        ];
        $teams = $this->findWithoutTeamId('all', $options);
        foreach ($teams as $k => $v) {
            if (!$v['Team']['name']) {
                unset($teams[$k]);
            }
        }
        if ($reformat_for_shell) {
            $teams = Hash::format($teams,
                ['{n}.Team.name', '{n}.Team.id', '{n}.TeamMember.status', '{n}.TeamMember.admin_flg'],
                'TeamName:%s, TeamId:%s, TeamMemberActive:%s, TeamAdmin:%s'
            );
        }
        return $teams;
    }

    /**
     * active admin as team member and user
     *
     * @param int $userId
     * @param int $teamId
     *
     * @return bool
     */
    public
    function isActiveAdmin(
        int $userId,
        int $teamId
    ): bool {
        $options = [
            'conditions' => [
                'TeamMember.user_id'   => $userId,
                'TeamMember.admin_flg' => true,
                'TeamMember.status'    => self::USER_STATUS_ACTIVE
            ],
            'fields'     => ['TeamMember.id'],
            'joins'      => [
                [
                    'type'       => 'INNER',
                    'table'      => 'users',
                    'alias'      => 'User',
                    'conditions' => [
                        'User.id = TeamMember.user_id',
                        'User.active_flg' => true
                    ],
                ],
            ],
        ];

        $res = $this->find('first', $options);
        return (bool)$res;
    }

    /**
     * find admin Ids
     *
     * @param int $teamId
     *
     * @return array
     */
    function findAdminList(int $teamId): array
    {
        $options = [
            'conditions' => [
                'TeamMember.team_id'   => $teamId,
                'TeamMember.admin_flg' => true,
                'TeamMember.status'    => self::USER_STATUS_ACTIVE
            ],
            'fields'     => ['TeamMember.user_id'],
            'joins'      => [
                [
                    'type'       => 'INNER',
                    'table'      => 'users',
                    'alias'      => 'User',
                    'conditions' => [
                        'User.id = TeamMember.user_id',
                        'User.active_flg' => true
                    ],
                ],
            ],
        ];

        $res = $this->find('list', $options);
        return $res;
    }

    /**
     * data migration for shell
     * - active user -> status active
     *
     * @return void
     */
    function updateActiveFlgToStatus()
    {
        $res = $this->updateAll(
            [
                'TeamMember.status' => self::USER_STATUS_ACTIVE
            ],
            [
                'TeamMember.active_flg' => true
            ]
        );
        return $res;
    }

    /**
     * data migration for shell
     * - inactive user -> status inactive
     *
     * @return void
     */
    function updateInactiveFlgToStatus()
    {
        $res = $this->updateAll(
            [
                'TeamMember.status' => self::USER_STATUS_INACTIVE
            ],
            [
                'TeamMember.active_flg' => false
            ]
        );
        return $res;
    }

    /**
     * Get list of team members by its status.
     *      USER_STATUS_INVITED = 0;
     *      USER_STATUS_ACTIVE = 1;
     *      USER_STATUS_INACTIVE = 2;
     *
     * @param      $status
     * @param null $teamId
     *
     * @return array|null
     */
    public
    function getTeamMemberListByStatus(
        $status,
        $teamId = null
    ) {
        if (!$teamId) {
            $teamId = $this->current_team_id;
        }

        $options = [
            'conditions' => [
                'TeamMember.team_id' => $teamId,
                'TeamMember.status'  => $status,
            ],
        ];
        $res = $this->find('list', $options);
        return $res;
    }

    /**
     * Is team member or not
     *
     * @param int $teamId
     * @param int $teamMemberId
     *
     * @return bool
     */
    public
    function isTeamMember(
        int $teamId,
        int $teamMemberId
    ): bool {
        $options = [
            'conditions' => [
                'id'      => $teamMemberId,
                'team_id' => $teamId
            ]
        ];
        return (bool)$this->find('first', $options);
    }

    /**
     * Is inactive team member
     *
     * @param int $teamMemberId
     *
     * @return bool
     */
    public
    function isInactive(
        int $teamMemberId
    ): bool {
        $options = [
            'conditions' => [
                'id'     => $teamMemberId,
                'status' => self::USER_STATUS_INACTIVE
            ]
        ];
        return (bool)$this->find('first', $options);
    }

    /**
     * Get user data by team member id
     *
     * @param int $teamMemberId
     *
     * @return array
     */
    public
    function getUserById(
        int $teamMemberId
    ): array {
        $options = [
            'conditions' => [
                'TeamMember.id' => $teamMemberId
            ],
            'contain'    => [
                'User' => [
                    'fields' => $this->User->profileFields
                ]
            ]
        ];
        $res = $this->find('first', $options);
        if (!empty($res['User'])) {
            return $res['User'];
        }
        return [];
    }

    /**
     * Find Belonged teams by user
     *
     * @param int $userId
     *
     * @return array
     * @internal param int $teamMemberId
     */
    public
    function findBelongsByUser(
        int $userId
    ): array {
        $options = [
            'conditions' => [
                'TeamMember.user_id'   => $userId,
                'TeamMember.status !=' => Enum\Model\TeamMember\Status::INACTIVE
            ],
        ];
        $res = $this->find('all', $options);
        if (empty($res)) {
            return [];
        }
        return Hash::extract($res, '{n}.TeamMember');
    }

    /**
     * Get list of team members
     *
     * @param int                        $teamId
     * @param TeamMemberEnum\Status|null $status Status of member. If not given, will take all
     *
     * @return TeamMemberEntity[]
     */
    public function getMemberList(int $teamId, TeamMemberEnum\Status $status = null)
    {
        $condition = [
            'conditions' => [
                'TeamMember.team_id' => $teamId,
                'TeamMember.del_flg' => false
            ]
        ];

        if (!empty($status)) {
            $condition['conditions']['TeamMember.status'] = $status->getValue();
        }

        return $this->useType()->useEntity()->find('all', $condition);
    }

    /**
     * Get last logged in active team ID
     *
     * @param int $userId
     *
     * @return int | null Team ID, null for no available joined active team
     */
    public function getLatestLoggedInActiveTeamId(int $userId)
    {
        $condition = [
            'conditions' => [
                'TeamMember.user_id' => $userId,
                'TeamMember.del_flg' => false,
                'TeamMember.status'  => Enum\Model\TeamMember\Status::ACTIVE
            ],
            'fields'     => [
                'TeamMember.team_id',
            ],
            'order'      => [
                'TeamMember.last_login' => 'DESC'
            ],
            'joins'      => [
                [
                    'type'       => 'INNER',
                    'table'      => 'teams',
                    'alias'      => 'Team',
                    'conditions' => [
                        'Team.id = TeamMember.team_id',
                        'Team.del_flg' => false
                    ]
                ]
            ]
        ];
        $res = $this->find('first', $condition);

        return Hash::get($res, 'TeamMember.team_id', null);
    }

    /**
     * Filter active member's user id
     *
     * @param array $userIds
     * @param int   $teamId
     *
     * @return array user ids array
     */
    public function filterActiveMembers(array $userIds, int $teamId): array
    {
        $condition = [
            'conditions' => [
                'TeamMember.team_id' => $teamId,
                'TeamMember.user_id' => $userIds,
                'TeamMember.del_flg' => false,
                'TeamMember.status'  => Enum\Model\TeamMember\Status::ACTIVE
            ],
            'fields'     => [
                'TeamMember.user_id',
            ],
            'joins'      => [
                [
                    'type'       => 'INNER',
                    'table'      => 'users',
                    'alias'      => 'User',
                    'conditions' => [
                        'User.id = TeamMember.user_id',
                        'User.active_flg' => true,
                        'User.del_flg'    => false
                    ]
                ]
            ]
        ];
        $res = $this->find('all', $condition);

        return Hash::extract($res, '{n}.TeamMember.user_id');
    }

    /**
     * Check if user is being invited to given team
     *
     * @param int $userId
     * @param int $teamId
     *
     * @return bool
     */
    public function isBeingInvited(int $userId, int $teamId): bool
    {
        $condition = [
            'conditions' => [
                'TeamMember.user_id' => $userId,
                'TeamMember.team_id' => $teamId,
                'TeamMember.status'  => Enum\Model\TeamMember\Status::INVITED,
                'TeamMember.del_flg' => false
            ],
            'fields'     => [
                'TeamMember.id'
            ]
        ];

        return !empty($this->find('count', $condition));
    }

    /**
     * Get user's default translation language in a team
     *
     * @param int $teamId
     * @param int $userId
     *
     * @return string | null
     */
    public function getDefaultTranslationLanguage(int $teamId, int $userId)
    {
        $option = [
            'conditions' => [
                'team_id' => $teamId,
                'user_id' => $userId
            ],
            'fields'     => [
                'team_id',
                'user_id',
                'default_translation_language'
            ]
        ];

        $queryResult = $this->find('first', $option);

        if (empty($queryResult)) {
            throw new GlException\GoalousNotFoundException("Team member not found");
        }

        return Hash::get($queryResult, 'TeamMember.default_translation_language');
    }

    /**
     * Check whether team member has default translation language in the team
     *
     * @param int $teamId
     * @param int $userId
     *
     * @return bool
     */
    public function hasDefaultTranslationLanguage(int $teamId, int $userId): bool
    {
        return !empty($this->getDefaultTranslationLanguage($teamId, $userId));
    }

    /**
     * Set user's default translation language in a team
     *
     * @param int    $teamId
     * @param int    $userId
     * @param string $langCode ISO 639-1 Language Code
     *
     * @throws  Exception
     */
    public function setDefaultTranslationLanguage(int $teamId, int $userId, string $langCode)
    {
        /** @var TranslationLanguage $TranslationLanguage */
        $TranslationLanguage = ClassRegistry::init('TranslationLanguage');

        if (!$TranslationLanguage->isValidLanguage($langCode)) {
            throw new InvalidArgumentException("Unknown translation language: " . $langCode);
        }

        $teamMemberId = $this->getIdByTeamAndUserId($teamId, $userId);

        if (empty($teamMemberId)) {
            throw new GlException\GoalousNotFoundException("Team member not found");
        }

        $this->id = $teamMemberId;

        $newData = [
            'default_translation_language' => $langCode
        ];

        $this->save($newData, false);
    }

    /**
     * Delete team_member.
     * Update del_flg and deleted.
     *
     * @param int    $teamId
     * @param int    $userId
     *
     */
    public function deleteTeamMember(int $teamId, int $userId)
    {
        $teamMemberId = $this->getIdByTeamAndUserId($teamId, $userId);

        if (empty($teamMemberId)) {
            throw new GlException\GoalousNotFoundException("Team member not found");
        }

        $this->id = $teamMemberId;

        $newData = [
            'del_flg' => true,
            'deleted' => GoalousDateTime::now()->getTimestamp()
        ];

        $this->save($newData, false);
    }

    /**
     * If user's status is USER_STATUS_ACTIVE, return true.
     *
     * @param int    $teamId
     * @param int    $userId
     *
     * @return boolean
     */
    public function isStatusActive(int $teamId, int $userId)
    {
        $option = [
            'conditions' => [
                'team_id' => $teamId,
                'user_id' => $userId,
                'status'  => self::USER_STATUS_ACTIVE
            ],
            'fields'     => [
                'status'
            ]
        ];

        $teamMemberStatus = $this->find('first', $option);

        if(empty($teamMemberStatus)) {
            return false;
        }

        return true;
    }
}
