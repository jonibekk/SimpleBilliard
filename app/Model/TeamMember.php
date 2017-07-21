<?php
App::uses('AppModel', 'Model');
App::uses('UploadHelper', 'View/Helper');
App::uses('View', 'View');

/**
 * TeamMember Model
 *
 * @property User        $User
 * @property Team        $Team
 * @property MemberType  $MemberType
 * @property User        $CoachUser
 * @property JobCategory $JobCategory
 */
class TeamMember extends AppModel
{
    const ADMIN_USER_FLAG = 1;
    const ACTIVE_USER_FLAG = 1;

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
        'active_flg'            => [
            'isVerifiedEmail' => [
                'rule' => ['isVerifiedEmail']
            ],
            'boolean'         => [
                'rule' => ['boolean'],
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

    /**
     * 現在有効なチーム一覧を取得
     *
     * @param $uid
     *
     * @return array
     */
    function getActiveTeamList($uid)
    {
        if (empty($this->myTeams)) {

            $this->setActiveTeamList($uid);
        }
        return $this->myTeams;
    }

    function setActiveTeamList($uid)
    {
        $model = $this;
        $res = Cache::remember($this->getCacheKey(CACHE_KEY_TEAM_LIST, true, null, false),
            function () use ($model, $uid) {
                $options = [
                    'conditions' => [
                        'TeamMember.user_id'    => $uid,
                        'TeamMember.active_flg' => true
                    ],
                    'fields'     => ['TeamMember.team_id', 'Team.name'],
                    'contain'    => ['Team']
                ];
                $res = array_filter($model->findWithoutTeamId('list', $options));
                return $res;
            }, 'team_info');
        $this->myTeams = $res;
    }

    public function getActiveTeamMembersList($use_cache = true)
    {
        if ($use_cache && !empty($this->active_member_list)) {
            return $this->active_member_list;
        }
        //if team is not exist
        if (!$this->Team->findById($this->current_team_id)) {
            return [];
        }
        $options = [
            'conditions' => [
                'active_flg' => true,
                'team_id'    => $this->current_team_id
            ],
            'fields'     => ['user_id', 'user_id']
        ];
        $this->active_member_list = $this->find('list', $options);
        $this->active_member_list = $this->User->filterActiveUserList($this->active_member_list);

        return $this->active_member_list;
    }

    function updateLastLogin($team_id, $uid)
    {
        $team_member = $this->find('first', ['conditions' => ['user_id' => $uid, 'team_id' => $team_id]]);
        $team_member['TeamMember']['last_login'] = REQUEST_TIMESTAMP;

        $enable_with_team_id = false;
        if ($this->Behaviors->loaded('WithTeamId')) {
            $enable_with_team_id = true;
        }
        if ($enable_with_team_id) {
            $this->Behaviors->disable('WithTeamId');
        }

        $res = $this->save($team_member);

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
     * @param $team_id
     * @param $uid
     *
     * @return bool
     * @throws RuntimeException
     */
    public function permissionCheck($team_id, $uid)
    {
        //チームに入っていない場合
        if (!$team_id) {
            throw new RuntimeException(__("If you want to access this page, please switch to the team."));
        }
        if (!$this->myStatusWithTeam) {
            $this->setMyStatusWithTeam($team_id, $uid);
        }
        if (empty($this->myStatusWithTeam['Team'])) {
            throw new RuntimeException(__("There is no team."));
        }
        if (!$this->myStatusWithTeam['TeamMember']['active_flg']) {
            throw new RuntimeException(__("You can't access to this team. Your account has been disabled."));
        }
        return true;
    }

    /**
     * @param $uid
     * @param $team_id
     *
     * @return bool
     */
    public function isActive($uid, $team_id = null)
    {
        if (!$team_id) {
            if (!$this->current_team_id) {
                return false;
            }
            $team_id = $this->current_team_id;
        }
        $is_default = false;
        if ($uid == $this->my_uid && $team_id == $this->current_team_id) {
            $is_default = true;
            $res = Cache::read($this->getCacheKey(CACHE_KEY_MEMBER_IS_ACTIVE, true), 'team_info');
            if ($res !== false) {
                if (!empty($res) && Hash::get($res, 'User.id') && Hash::get($res, 'Team.id')) {
                    return true;
                }
                return false;
            }
        }
        $options = [
            'conditions' => [
                'TeamMember.team_id'    => $team_id,
                'TeamMember.user_id'    => $uid,
                'TeamMember.active_flg' => true,
            ],
            'fields'     => ['TeamMember.id', 'TeamMember.user_id', 'TeamMember.team_id'],
            'contain'    => [
                'User' => [
                    'conditions' => [
                        'User.active_flg' => true,
                    ],
                    'fields'     => ['User.id']
                ],
                'Team' => [
                    'fields' => ['Team.id']
                ]
            ]
        ];
        $res = $this->find('first', $options);
        if ($is_default) {
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

    public function isAdmin($uid = null)
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
        return $res;
    }

    public function add($uid, $team_id)
    {
        //if exists update
        $team_member = $this->find('first', ['conditions' => ['user_id' => $uid, 'team_id' => $team_id]]);
        if (Hash::get($team_member, 'TeamMember.id')) {
            $team_member['TeamMember']['active_flg'] = true;
            return $this->save($team_member);
        }
        $data = [
            'user_id'    => $uid,
            'team_id'    => $team_id,
            'active_flg' => true,
        ];
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
                'team_id'    => $teamId,
                'active_flg' => true,
            ],
            'fields'     => ['user_id'],
        ];
        if ($required_active) {
            $options['conditions']['active_flg'] = true;
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

    public function setAdminUserFlag($member_id, $flag)
    {
        $this->deleteCacheMember($member_id);
        $this->id = $member_id;
        $flag = $flag == 'ON' ? 1 : 0;
        return $this->saveField('admin_flg', $flag);
    }

    public function setActiveFlag($member_id, $flag)
    {
        $this->deleteCacheMember($member_id);
        $this->id = $member_id;
        $flag = $flag == 'ON' ? 1 : 0;
        return $this->saveField('active_flg', $flag, true);
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
            'fields'     => ['id', 'active_flg', 'admin_flg', 'coach_user_id', 'evaluation_enable_flg', 'created'],
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
            $user_emails = Hash::combine($user_emails, '{n}.Email.user_id', '{n}.Email.email_verified');
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
        return $this->updateAll(['TeamMember.active_flg' => true],
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

    /**
     * save new members from csv
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
    function saveNewMembersFromCsv($csv)
    {
        $res = [
            'error'         => false,
            'success_count' => 0,
            'error_line_no' => 0,
            'error_msg'     => null,
        ];
        $validate = $this->validateNewMemberCsvData($csv);
        if ($validate['error']) {
            return array_merge($res, $validate);
        }

        //save process

        /**
         * グループ登録処理
         * グループが既に存在すれば、存在するIdをセット。でなければ、グループを新規登録し、IDをセット
         */
        foreach ($this->csv_datas as $row_k => $row_v) {
            // delete old existing joined group members
            if (!empty($row_v['User']['id'])) {
                $member_group_ids = $this->User->MemberGroup->getAllGroupMemberIds($this->current_team_id,
                    $row_v['User']['id']);
                if (!empty($member_group_ids)) {
                    foreach ($member_group_ids as $member_group_id) {
                        $this->User->MemberGroup->delete($member_group_id);
                    }
                }
            }

            // add member groups
            if (Hash::get($row_v, 'Group')) {
                foreach ($row_v['Group'] as $k => $v) {
                    // if user with same team and group exists then don't need to insert again
                    $group = $this->User->MemberGroup->Group->getByNameIfNotExistsSave($v);

                    // making member group array to save
                    $this->csv_datas[$row_k]['MemberGroup'][] = [
                        'group_id'  => $group['Group']['id'],
                        'index_num' => $k
                    ];
                }
                unset($this->csv_datas[$row_k]['Group']);
            }
        }

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

        /**
         * ユーザ登録
         * 1.メアドが存在する場合は、既存ユーザの情報を書き換える。
         * なければ、まずEmail、LocalNameを登録し、ユーザ情報を登録した上でTeamMemberを登録する。
         */
        foreach ($this->csv_datas as $row_k => $row_v) {
            //メアド存在確認
            $user = $this->User->getUserByEmail($row_v['Email']['email']);

            if (Hash::get($user, 'User')) {
                $this->csv_datas[$row_k]['Email'] = $user['Email'];
                //ユーザが存在した場合は、ユーザ情報を書き換える。User,LocalName
                $user['User'] = array_merge($user['User'], $row_v['User']);
                // if no_pass_flg true, don't update password
                if ($user['User']['no_pass_flg'] == 1) {
                    unset($user['User']['password']);
                }

                // 意図しないカラムの更新を防ぐために、明示的に更新するカラムを指定する
                $user_update_fields = array_keys($user['User']);
                $user = $this->User->save($user['User'], true, $user_update_fields);
            } else {
                //なければ、ユーザ情報(User,Email)を登録。
                //create User
                $this->User->create();
                $row_v['User']['no_pass_flg'] = true;
                $row_v['User']['default_team_id'] = $this->current_team_id;
                $row_v['User']['language'] = Hash::get($row_v,
                    'LocalName.language') ? $row_v['LocalName']['language'] : 'eng';

                $user = $this->User->save($row_v['User']);
                $row_v['Email']['user_id'] = $user['User']['id'];
                //create Email
                $this->User->Email->create();
                $email = $this->User->Email->save($row_v['Email']);

                $this->csv_datas[$row_k]['Email'] = $email['Email'];
                $user['User']['primary_email_id'] = $email['Email']['id'];
                $this->User->id = $user['User']['id'];

                $this->User->saveField('primary_email_id', $email['Email']['id']);
            }
            $this->csv_datas[$row_k]['User'] = $user['User'];

            //LocalName
            $options = [
                'conditions' => [
                    'user_id' => $user['User']['id']
                ]
            ];
            if (Hash::get($row_v, 'LocalName')) {
                //save LocalName (if only lang update)
                $existing_local_name = $this->User->LocalName->find('first', $options);

                if (Hash::get($existing_local_name, 'LocalName')) {
                    $existing_local_name['LocalName'] = array_merge($existing_local_name['LocalName'],
                        $row_v['LocalName']);
                    $existing_local_name = $this->User->LocalName->save($existing_local_name);
                } else {
                    $row_v['LocalName']['user_id'] = $user['User']['id'];
                    $this->User->LocalName->create();
                    $existing_local_name = $this->User->LocalName->save($row_v['LocalName']);
                }

                $this->csv_datas[$row_k]['LocalName'] = $existing_local_name['LocalName'];
            } else {
                $exists_local_name = $this->User->LocalName->find('first', $options);
                if (!empty($exists_local_name)) {
                    $this->User->LocalName->delete($exists_local_name['LocalName']['id']);
                }
            }

            //MemberGroupの登録
            if (Hash::get($row_v, 'MemberGroup')) {
                foreach ($row_v['MemberGroup'] as $k => $v) {
                    $row_v['MemberGroup'][$k]['index_num'] = $k;
                    $row_v['MemberGroup'][$k]['user_id'] = $user['User']['id'];
                    $row_v['MemberGroup'][$k]['team_id'] = $this->current_team_id;
                }
                $this->User->MemberGroup->saveAll($row_v['MemberGroup']);
            }
            /**
             * TeamMemberに登録
             */
            if (Hash::get($row_v, 'TeamMember')) {
                $row_v['TeamMember']['user_id'] = $user['User']['id'];
                $row_v['TeamMember']['team_id'] = $this->current_team_id;
                $row_v['TeamMember']['invitation_flg'] = true;
                $row_v['TeamMember']['active_flg'] = false;
                $this->create();
                $team_member = $this->save($row_v['TeamMember']);
                $this->csv_datas[$row_k]['TeamMember'] = $team_member['TeamMember'];
            }
        }

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
        $save_evaluator_data = [];
        foreach ($this->csv_datas as $row_k => $row_v) {
            // delete all existing evaluator records for the same user
            if (!empty($row_v['User']['id'])) {
                $existing_evaluator_ids = $this->Team->Evaluator->getExistingEvaluatorsIds($this->current_team_id,
                    $row_v['User']['id']);
                if (!empty($existing_evaluator_ids)) {
                    foreach ($existing_evaluator_ids as $existing_evaluator_id) {
                        $this->Team->Evaluator->delete($existing_evaluator_id);
                    }
                }
            }

            if (!Hash::get($row_v, 'Evaluator')) {
                continue;
            }
            foreach ($row_v['Evaluator'] as $r_k => $r_v) {
                if ($evaluator_team_member = $this->getByMemberNo($r_v)) {
                    // making evaluator save array
                    $save_evaluator_data[] = [
                        'index_num'         => $r_k,
                        'team_id'           => $this->current_team_id,
                        'evaluatee_user_id' => $row_v['User']['id'],
                        'evaluator_user_id' => $evaluator_team_member['TeamMember']['user_id'],
                    ];
                }
            }
        }
        // saving evaluator data
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

        $before_csv_data = $this->getAllMembersCsvData();
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
            if (!($row = copyKeyName($this->_getCsvHeading(false), $row))) {
                $res['error_msg'] = __("Numbers are not consistent.");
                return $res;
            }
            if ($key === 0) {
                if (!empty(array_diff($row, $this->_getCsvHeading(false)))) {
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
            $this->csv_datas[$key]['TeamMember']['active_flg'] = strtolower($row['active_flg']) == "on" ? true : false;
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
            if ($v['TeamMember']['admin_flg'] && $v['TeamMember']['active_flg']) {
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

    /**
     * validate new member csv data
     *
     * @param array $csv_data
     *
     * @return array
     */
    function validateNewMemberCsvData($csv_data)
    {
        $this->_setCsvValidateRule();

        $res = [
            'error'         => true,
            'error_line_no' => 0,
            'error_msg'     => null,
        ];

        if (count($csv_data) <= 1) {
            $res['error_msg'] = __("At least one data is required.");
            return $res;
        }
        //validation each line of csv data.
        foreach ($csv_data as $key => $row) {
            //first record check
            if ($key == 0) {
                if (!empty(array_diff($row, $this->_getCsvHeading()))) {
                    $res['error_msg'] = __("Headding are not consistent.");
                    return $res;
                }
                continue;
            }
            //set line no
            $res['error_line_no'] = $key + 1;
            //key name set
            if (!($row = copyKeyName($this->_getCsvHeading(), $row))) {
                $res['error_msg'] = __("Numbers are not consistent.");
                return $res;
            }

            $row = Hash::expand($row);
            if (Hash::get($row, 'gender')) {
                $row['gender'] = strtolower($row['gender']);
            }
            $this->set($row);
            if (!$this->validates()) {
                $res['error_msg'] = current(array_shift($this->validationErrors));
                return $res;
            }

            $this->csv_emails[] = $row['email'];
            $this->csv_datas[$key]['Email'] = ['email' => $row['email']];

            // add flg which records to be update
            $options = [
                'fields'     => ['email', 'id', 'to_user_id', 'email_token_expires'],
                'conditions' => [
                    'email'          => $row['email'],
                    'team_id'        => $this->current_team_id,
                    'email_verified' => 0
                ]
            ];
            // checking first if any previous record in invite table, if not then get user_id from email table
            // for checking the member id
            $checkInvite = $this->Team->Invite->find('first', $options);
            if (!empty($checkInvite['Invite'])) {
                $this->csv_datas[$key]['User']['id'] = $checkInvite['Invite']['to_user_id'];
            } else {
                $checkInvite = $this->User->Email->findByEmail($row['email']);
                if (!empty($checkInvite['Email'])) {
                    $this->csv_datas[$key]['User']['id'] = $checkInvite['Email']['user_id'];
                }
            }

            //exists member id check(after check)
            $this->csv_member_ids[] = $row['member_no'];
            $this->csv_datas[$key]['TeamMember']['member_no'] = $row['member_no'];
            $this->csv_datas[$key]['User']['first_name'] = $row['first_name'];
            $this->csv_datas[$key]['User']['last_name'] = $row['last_name'];
            $this->csv_datas[$key]['TeamMember']['admin_flg'] = strtolower($row['admin_flg']) === 'on' ? true : false;
            $this->csv_datas[$key]['TeamMember']['evaluation_enable_flg'] = strtolower($row['evaluation_enable_flg']) === 'on' ? true : false;

            // for coach id set null if not set
            if (!Hash::get($row, 'coach_member_no')) {
                $this->csv_datas[$key]['TeamMember']['coach_user_id'] = null;
            }

            if (Hash::get($row, 'member_type')) {
                $this->csv_datas[$key]['MemberType']['name'] = $row['member_type'];
            } else {
                $this->csv_datas[$key]['TeamMember']['member_type_id'] = null;
            }

            // for local name
            if (Hash::get($row, 'language') && (Hash::get($row, 'local_first_name') || Hash::get($row,
                        'local_last_name'))
            ) {
                $this->csv_datas[$key]['LocalName']['language'] = $row['language'];
                $this->csv_datas[$key]['LocalName']['first_name'] = Hash::get($row,
                    'local_first_name') ? $row['local_first_name'] : '';
                $this->csv_datas[$key]['LocalName']['last_name'] = Hash::get($row,
                    'local_last_name') ? $row['local_last_name'] : '';
            } else {
                if (Hash::get($row, 'language')) {
                    $this->local_lang_list[$key] = 'Local first name or local last name required with language of local name.';
                } else {
                    if (Hash::get($row, 'local_first_name') || Hash::get($row, 'local_last_name')) {
                        $this->local_lang_list[$key] = 'Local language required with local first name or local last name.';
                    }
                }
            }

            if (Hash::get($row, 'phone_no')) {
                $this->csv_datas[$key]['User']['phone_no'] = str_replace(["-", "(", ")"], '', $row['phone_no']);
            } else {
                $this->csv_datas[$key]['User']['phone_no'] = null;
            }

            if (Hash::get($row, 'gender')) {
                $this->csv_datas[$key]['User']['gender_type'] = $row['gender'] === 'male' ? User::TYPE_GENDER_MALE : User::TYPE_GENDER_FEMALE;
            } else {
                $this->csv_datas[$key]['User']['gender_type'] = null;
            }

            //[12]Birth Year
            if (Hash::get($row, 'birth_year') && Hash::get($row, 'birth_month') && Hash::get($row, 'birth_day')) {
                $this->csv_datas[$key]['User']['birth_day'] = $row['birth_year'] . '/' . $row['birth_month'] . '/' . $row['birth_day'];
            } else {
                $this->csv_datas[$key]['User']['birth_day'] = null;
            }

            //[15]-[21]Group
            foreach ($row['group'] as $v) {
                if (viaIsSet($v)) {
                    $this->csv_datas[$key]['Group'][] = $v;
                }
            }

            //[22]Coach ID
            //exists check (after check)
            $this->csv_coach_ids[] = $row['coach_member_no'];
            if (Hash::get($row, 'coach_member_no')) {
                $this->csv_datas[$key]['Coach'] = $row['coach_member_no'];
            }

            //[23]-[29]Evaluator ID
            foreach ($row['evaluator_member_no'] as $v) {
                if (viaIsSet($v)) {
                    $this->csv_datas[$key]['Evaluator'][] = $v;
                }
            }
            //evaluator id check(after check)
            $this->csv_evaluator_ids[] = array_filter($row['evaluator_member_no'], "strlen");
        } // end of foreach csv_data

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

        // if only language of local name (without Local first name or local last name)  then show error
        if (!empty($this->local_lang_list)) {
            foreach ($this->local_lang_list as $local_k => $local_v) {
                //set line no
                $res['error_line_no'] = $local_k + 1;
                $res['error_msg'] = __($local_v);
                return $res;
            }
        }

        //already joined team check
        $joined_emails = $this->User->Email->getEmailsBelongTeamByEmail($this->csv_emails);
        foreach ($joined_emails as $email) {
            //set line no
            $res['error_line_no'] = array_search($email['Email']['email'], $this->csv_emails) + 2;
            $res['error_msg'] = __("This email address is found in this team.");
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

        //exists member id check
        $members = $this->find('all',
            [
                'conditions' => ['team_id' => $this->current_team_id, 'member_no' => $this->csv_member_ids],
                'fields'     => ['member_no', 'user_id', 'id']
            ]
        );

        if (!empty($members)) {
            foreach ($members as $member) {
                foreach ($this->csv_datas as $csv_data_key => $this_csv_data) {
                    if ($member['TeamMember']['member_no'] == $this_csv_data['TeamMember']['member_no']) {
                        if (!empty($this_csv_data['User']['id']) && $member['TeamMember']['user_id'] ==
                            $this_csv_data['User']['id']
                        ) {
                            $this->csv_datas[$csv_data_key]['TeamMember']['id'] = $member['TeamMember']['id'];
                            $this->csv_datas[$csv_data_key]['TeamMember']['user_id'] = $member['TeamMember']['user_id'];
                        } else {
                            $res['error_line_no'] = array_search($member['TeamMember']['member_no'],
                                    $this->csv_member_ids) + 2;
                            $res['error_msg'] = __("This Member ID is found in this team.");
                            return $res;
                        }
                    }
                }
            }
        } else {
            foreach ($this->csv_datas as $csv_data_key => $this_csv_data) {
                if (!empty($this_csv_data['User']['id'])) {
                    $member = $this->findByUserId($this_csv_data['User']['id']);
                    if (!empty($member)) {
                        $this->csv_datas[$csv_data_key]['TeamMember']['id'] = $member['TeamMember']['id'];
                        $this->csv_datas[$csv_data_key]['TeamMember']['user_id'] = $member['TeamMember']['user_id'];
                    }
                }
            }
        }

        //coach id check
        $this->csv_coach_ids = array_filter($this->csv_coach_ids, "strlen");

        //Coach ID must be already been registered or must be included in the member ID
        //First check coach ID whether registered
        $exists_coach_ids = $this->find('all',
            [
                'conditions' => ['team_id' => $this->current_team_id, 'member_no' => $this->csv_coach_ids],
                'fields'     => ['member_no']
            ]
        );
        //remove the registered coach
        foreach ($exists_coach_ids as $k => $v) {
            $member_no = $v['TeamMember']['member_no'];
            $key = array_search($member_no, $this->csv_coach_ids);
            if ($key !== false) {
                unset($this->csv_coach_ids[$key]);
            }
        }
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
        //Check for evaluator ID registered
        $exists_evaluator_ids = $this->find('all',
            [
                'conditions' => ['team_id' => $this->current_team_id, 'member_no' => $merged_evaluator_ids],
                'fields'     => ['member_no']
            ]
        );
        //remove the evaluator ID of the registered
        foreach ($exists_evaluator_ids as $er_k => $er_v) {
            $member_no = $er_v['TeamMember']['member_no'];
            foreach ($this->csv_evaluator_ids as $r_k => $r_v) {
                $key = array_search($member_no, $r_v);
                if ($key !== false) {
                    unset($this->csv_evaluator_ids[$r_k][$key]);
                }
            }
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

        $this->_setValidateFromBackUp();
        $res['error'] = false;
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
     * $user_id をキーにしてチームメンバー情報を取得
     *
     * @param      $user_id
     * @param null $team_id
     *
     * @return array|null
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
            $this->csv_datas[$k]['email'] = Hash::get($v,
                'User.PrimaryEmail.email') ? $v['User']['PrimaryEmail']['email'] : null;
            $this->csv_datas[$k]['first_name'] = Hash::get($v, 'User.first_name') ? $v['User']['first_name'] : null;
            $this->csv_datas[$k]['last_name'] = Hash::get($v, 'User.last_name') ? $v['User']['last_name'] : null;
            $this->csv_datas[$k]['member_no'] = Hash::get($v,
                'TeamMember.member_no') ? $v['TeamMember']['member_no'] : null;
            $this->csv_datas[$k]['active_flg'] = Hash::get($v,
                'TeamMember.active_flg') && $v['TeamMember']['active_flg'] ? 'ON' : 'OFF';
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
                ]
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
            'fields'     => ['member_no', 'coach_user_id', 'active_flg', 'admin_flg', 'evaluation_enable_flg'],
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
                $default_csv = $this->_getCsvHeading(false);
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
    function _getCsvHeading($new = true)
    {
        if ($new) {
            return [
                'email'                 => __("Email(*)"),
                'member_no'             => __("Member ID(*)"),
                'first_name'            => __("First Name(*)"),
                'last_name'             => __("Last Name(*)"),
                'admin_flg'             => __("Administrator(*)"),
                'evaluation_enable_flg' => __("Evaluated(*)"),
                'member_type'           => __("Member Type"),
                'language'              => __("Language of Local Name"),
                'local_first_name'      => __("Local First Name"),
                'local_last_name'       => __("Local Last Name"),
                'phone_no'              => __("Telephone Number"),
                'gender'                => __("Gender"),
                'birth_year'            => __("Birth Year"),
                'birth_month'           => __("Birth Month"),
                'birth_day'             => __("Birthday"),
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

        return [
            'email'                 => __("Email(*, Not changed)"),
            'first_name'            => __("First Name(*, Not changed)"),
            'last_name'             => __("Last Name(*, Not changed)"),
            'member_no'             => __("Member ID(*)"),
            'active_flg'            => __("Member active status(*)"),
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
        for ($ek = 1; $ek <= 7; $ek++) {
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
        $validateOfNew = [
            'phone_no'         => [
                'maxLength' => [
                    'rule'    => ['maxLength', 20],
                    'message' => __("%s should be entered in less than 20 characters.", __("Phone number"))
                ],
                'phoneNo'   => [
                    'rule'       => 'phoneNo',
                    'message'    => __("Telephone number is incorrect. Single-byte number and '-()' are allowed."),
                    'allowEmpty' => true,
                ],
            ],
            'gender'           => [
                'inList' => [
                    'rule'       => ['inList', ['male', 'female']],
                    'message'    => __("Choose 'male' or 'female'."),
                    'allowEmpty' => true,
                ],
            ],
            'language'         => [
                'inList' => [
                    'rule'       => ['inList', $this->support_lang_codes],
                    'message'    => __("It is unsupported language code of the local first name and last name."),
                    'allowEmpty' => true,
                ],
            ],
            'local_first_name' => [
                'maxLength' => [
                    'rule'    => ['maxLength', 64],
                    'message' => __("%s should be entered in less than 64 characters.", __("Local First Name"))
                ],
            ],
            'local_last_name'  => [
                'maxLength' => [
                    'rule'    => ['maxLength', 64],
                    'message' => __("%s should be entered in less than 64 characters.", __("Local Last Name"))
                ],
            ],
            'birth_year'       => [
                'isAllOrNothing' => [
                    'rule'    => ['isAllOrNothing', ['birth_year', 'birth_month', 'birth_day']],
                    'message' => __("If you want to fill in the date of birth, please fill out all of the items of date."),
                ],
                'birthYear'      => [
                    'rule'       => 'birthYear',
                    'message'    => __("%s is not correct.", __("Birth Year")),
                    'allowEmpty' => true,
                ],
            ],
            'birth_month'      => [
                'birthMonth' => [
                    'rule'       => 'birthMonth',
                    'message'    => __("%s is not correct.", __("Birth Month")),
                    'allowEmpty' => true,
                ],
            ],
            'birth_day'        => [
                'birthDay' => [
                    'rule'       => 'birthDay',
                    'message'    => __("%s is not correct.", __("Birthday")),
                    'allowEmpty' => true,
                ],
            ],
        ];
        $validateOfUpdate = [
            'active_flg' => [
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
        if ($new) {
            $validate = $common_validate + $validateOfNew;
        } else {
            $validate = $common_validate + $validateOfUpdate;
        }
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
     * @return array|null
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
                'active_flg'               => 1,
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
            'fields'     => ['active_flg', 'evaluation_enable_flg'],
            'conditions' => [
                'TeamMember.user_id' => $user_id,
            ],
        ];
        $res = $this->find('first', $options);
        $evaluation_flg = false;
        if (isset($res['TeamMember']['active_flg']) === true
            && $res['TeamMember']['active_flg'] === true
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
                'TeamMember.team_id'    => $team_id,
                'TeamMember.active_flg' => true,
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
                'TeamMember.active_flg',
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
                ['{n}.Team.name', '{n}.Team.id', '{n}.TeamMember.active_flg', '{n}.TeamMember.admin_flg'],
                'TeamName:%s, TeamId:%s, TeamMemberActive:%s, TeamAdmin:%s'
            );
        }
        return $teams;
    }

    /**
     * active admin as team member and user
     *
     * @param  int $userId
     * @param  int $teamId
     *
     * @return bool
     */
    public function isActiveAdmin(int $userId, int $teamId): bool
    {
        $options = [
            'conditions' => [
                'TeamMember.user_id'    => $userId,
                'TeamMember.admin_flg'  => true,
                'TeamMember.active_flg' => true
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
                'TeamMember.team_id'    => $teamId,
                'TeamMember.admin_flg'  => true,
                'TeamMember.active_flg' => true
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
}
