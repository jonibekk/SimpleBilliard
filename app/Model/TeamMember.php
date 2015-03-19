<?php
App::uses('AppModel', 'Model');

/**
 * TeamMember Model
 *
 * @property User              $User
 * @property Team              $Team
 * @property MemberType        $MemberType
 * @property User              $CoachUser
 * @property JobCategory       $JobCategory
 */
class TeamMember extends AppModel
{

    public $myTeams = [];
    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'active_flg'            => ['boolean' => ['rule' => ['boolean'],],],
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
        $options = [
            'conditions' => [
                'TeamMember.user_id'    => $uid,
                'TeamMember.active_flg' => true
            ],
            'fields'     => ['TeamMember.team_id', 'Team.name'],
            'contain'    => ['Team']
        ];
        $res = array_filter($this->find('list', $options));
        $this->myTeams = $res;
    }

    function updateLastLogin($team_id, $uid)
    {
        $team_member = $this->find('first', ['conditions' => ['user_id' => $uid, 'team_id' => $team_id]]);
        $team_member['TeamMember']['last_login'] = REQUEST_TIMESTAMP;
        $res = $this->save($team_member);
        return $res;
    }

    function getWithTeam($team_id = null, $uid = null)
    {
        if (!empty($this->myStatusWithTeam)) {
            return $this->myStatusWithTeam;
        }
        if (!$team_id) {
            $team_id = $this->current_team_id;
        }
        if (!$uid) {
            if (isset($this->my_uid)) {
                $uid = $this->my_uid;
            }
            else {
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
            throw new RuntimeException(__d('gl', "このページにアクセスする場合は、チームに切り換えてください。"));
        }
        if (!$this->myStatusWithTeam) {
            $this->setMyStatusWithTeam($team_id, $uid);
        }
        if (empty($this->myStatusWithTeam['Team'])) {
            throw new RuntimeException(__d('gl', "チームが存在しません。"));
        }
        if (!$this->myStatusWithTeam['TeamMember']['active_flg']) {
            throw new RuntimeException(__d('gl', "現在、あなたはこのチームにアクセスできません。ユーザアカウントが無効化されています。"));
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
        $options = [
            'conditions' => [
                'team_id'    => $team_id,
                'user_id'    => $uid,
                'active_flg' => true,
            ],
            'fields'     => ['id']
        ];
        if ($this->find('first', $options)) {
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
    public function adminCheck($team_id, $uid)
    {
        //まず通常のチームアクセス権限があるかチェック
        $this->permissionCheck($team_id, $uid);
        if (!$this->myStatusWithTeam['TeamMember']['admin_flg']) {
            throw new RuntimeException(__d('gl', "あなたはチーム管理者では無い為、このページにはアクセスできません。"));
        }
        return true;
    }

    public function add($uid, $team_id)
    {
        //if exists update
        $team_member = $this->find('first', ['conditions' => ['user_id' => $uid, 'team_id' => $team_id]]);
        if (viaIsSet($team_member['TeamMember']['id'])) {
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

    public function getAllMemberUserIdList($with_me = true)
    {
        $options = [
            'conditions' => [
                'team_id'    => $this->current_team_id,
                'active_flg' => true,
            ],
            'fields'     => ['user_id'],
        ];
        if (!$with_me) {
            $options['conditions']['NOT']['user_id'] = $this->my_uid;
        }
        $res = $this->find('list', $options);
        return $res;
    }

    public function activateMembers($user_ids, $team_id = null)
    {
        $team_id = !$team_id ? $this->current_team_id : $team_id;
        return $this->updateAll(['TeamMember.active_flg' => true],
                                ['TeamMember.team_id' => $team_id, 'TeamMember.user_id' => $user_ids]);
    }

    function incrementNotifyUnreadCount($user_ids)
    {
        if (empty($user_ids)) {
            return false;
        }

        $conditions = [
            'TeamMember.user_id' => $user_ids,
            'TeamMember.team_id' => $this->current_team_id,
        ];

        $res = $this->updateAll(['TeamMember.notify_unread_count' => 'TeamMember.notify_unread_count + 1'],
                                $conditions);
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
            if (viaIsSet($row_v['MemberType']['name'])) {
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
            if (viaIsSet($user['User']['TeamMember'][0]['id'])) {
                $this->csv_datas[$k]['TeamMember']['id'] = $user['User']['TeamMember'][0]['id'];
            }
            if (viaIsSet($user['User'])) {
                $this->csv_datas[$k]['User'] = $user['User'];
            }
            //save
            $this->create();
            $this->save($this->csv_datas[$k]['TeamMember']);
        }

        /**
         * グループ登録処理
         * グループが既に存在すれば、存在するIdをセット。でなければ、グループを新規登録し、IDをセット
         */
        //一旦グループ紐付けを解除
        $this->User->MemberGroup->deleteAll(['MemberGroup.team_id' => $this->current_team_id]);

        $member_groups = [];
        foreach ($this->csv_datas as $row_k => $row_v) {
            if (viaIsSet($row_v['Group'])) {
                foreach ($row_v['Group'] as $k => $v) {
                    $group = $this->User->MemberGroup->Group->getByNameIfNotExistsSave($v);
                    $member_groups[] = [
                        'group_id' => $group['Group']['id'],
                        'index'    => $k,
                        'team_id'  => $this->current_team_id,
                        'user_id'  => $row_v['User']['id'],
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
            if (!viaIsSet($row_v['Coach'])) {
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
            if (!viaIsSet($row_v['Evaluator'])) {
                continue;
            }
            foreach ($row_v['Evaluator'] as $r_k => $r_v) {
                if ($evaluator_team_member = $this->getByMemberNo($r_v)) {
                    $save_evaluator_data[] = [
                        'index'             => $r_k,
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
            if (viaIsSet($row_v['Group'])) {
                foreach ($row_v['Group'] as $k => $v) {
                    $group = $this->User->MemberGroup->Group->getByNameIfNotExistsSave($v);
                    $this->csv_datas[$row_k]['MemberGroup'][] = [
                        'group_id' => $group['Group']['id'],
                        'index'    => $k
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
            if (viaIsSet($row_v['MemberType']['name'])) {
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
            if (viaIsSet($user['User'])) {
                $this->csv_datas[$row_k]['Email'] = $user['Email'];
                //ユーザが存在した場合は、ユーザ情報を書き換える。User,LocalName
                $user['User'] = array_merge($user['User'], $row_v['User']);
                $user = $this->User->save($user['User']);
            }
            else {
                //なければ、ユーザ情報(User,Email)を登録。
                //create User
                $this->User->create();
                $row_v['User']['no_pass_flg'] = true;
                $row_v['User']['default_team_id'] = $this->current_team_id;
                $row_v['User']['language'] = viaIsSet($row_v['LocalName']['language']) ? $row_v['LocalName']['language'] : 'eng';
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
            if (viaIsSet($row_v['LocalName'])) {
                //save LocalName
                $options = [
                    'conditions' => [
                        'user_id'  => $user['User']['id'],
                        'language' => $row_v['LocalName']['language']
                    ]
                ];
                $local_name = $this->User->LocalName->find('first', $options);
                if (viaIsSet($local_name['LocalName'])) {
                    $local_name['LocalName'] = array_merge($local_name['LocalName'], $row_v['LocalName']);
                    $local_name = $this->User->LocalName->save($local_name);
                }
                else {
                    $row_v['LocalName']['user_id'] = $user['User']['id'];
                    $this->User->LocalName->create();
                    $local_name = $this->User->LocalName->save($row_v['LocalName']);
                }

                $this->csv_datas[$row_k]['LocalName'] = $local_name['LocalName'];
            }

            //MemberGroupの登録
            if (viaIsSet($row_v['MemberGroup'])) {
                foreach ($row_v['MemberGroup'] as $k => $v) {
                    $row_v['MemberGroup'][$k]['index'] = $k;
                    $row_v['MemberGroup'][$k]['user_id'] = $user['User']['id'];
                    $row_v['MemberGroup'][$k]['team_id'] = $this->current_team_id;
                }
                $this->User->MemberGroup->create();
                $this->User->MemberGroup->saveAll($row_v['MemberGroup']);
            }
            /**
             * TeamMemberに登録
             */
            if (viaIsSet($row_v['TeamMember'])) {
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
            if (!viaIsSet($row_v['Coach'])) {
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
            if (!viaIsSet($row_v['Evaluator'])) {
                continue;
            }
            foreach ($row_v['Evaluator'] as $r_k => $r_v) {
                if ($evaluator_team_member = $this->getByMemberNo($r_v)) {
                    $save_evaluator_data[] = [
                        'index'             => $r_k,
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

        $before_csv_data = $this->getAllMembersCsvData();
        //emails
        $before_emails = array_column($before_csv_data, 'email');

        //レコード数が同一である事を確認
        if (count($csv_data) - 1 !== count($before_csv_data)) {
            $res['error_msg'] = __d('validate', "レコード数が一致しません。");
            return $res;
        }

        //row validation
        foreach ($csv_data as $key => $row) {
            //set line no
            $res['error_line_no'] = $key + 1;

            //key name set
            if (!($row = copyKeyName($this->_getCsvHeading(false), $row))) {
                $res['error_msg'] = __d('gl', "項目数が一致しません。");
                return $res;
            }
            if ($key === 0) {
                if (!empty(array_diff($row, $this->_getCsvHeading(false)))) {
                    $res['error_msg'] = __d('gl', "見出しが一致しません。");
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
                $res['error_msg'] = __d('gl', "ファーストネームは変更できません。");
                return $res;
            }
            //last name check
            if ($row['last_name'] != $before_record['last_name']) {
                $res['error_msg'] = __d('gl', "ラストネームは変更できません。");
                return $res;
            }
            $this->csv_member_ids[] = $row['member_no'];
            $this->csv_datas[$key]['TeamMember']['member_no'] = $row['member_no'];
            $this->csv_datas[$key]['TeamMember']['active_flg'] = strtolower($row['active_flg']) == "on" ? true : false;
            $this->csv_datas[$key]['TeamMember']['admin_flg'] = strtolower($row['admin_flg']) == 'on' ? true : false;
            $this->csv_datas[$key]['TeamMember']['evaluation_enable_flg'] = strtolower($row['evaluation_enable_flg']) == 'on' ? true : false;
            if (viaIsSet($row['member_type'])) {
                $this->csv_datas[$key]['MemberType']['name'] = $row['member_type'];
            }
            else {
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
            if (viaIsSet($row['coach_member_no'])) {
                $this->csv_datas[$key]['Coach'] = $row['coach_member_no'];
            }
            else {
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
            $res['error_msg'] = __d('gl', "最低１人は管理者かつアクティブにしてください。");
            return $res;
        }

        //email exists check
        //E-mail address should not be duplicated
        if (count($this->csv_emails) != count(array_unique($this->csv_emails))) {
            $duplicate_emails = array_filter(array_count_values($this->csv_emails), 'isOver2');
            $duplicate_email = key($duplicate_emails);
            //set line no
            $res['error_line_no'] = array_search($duplicate_email, $this->csv_emails) + 2;
            $res['error_msg'] = __d('gl', "重複したメールアドレスが含まれています。");
            return $res;
        }
        //member id duplicate check
        if (count($this->csv_member_ids) != count(array_unique($this->csv_member_ids))) {
            $duplicate_member_ids = array_filter(array_count_values($this->csv_member_ids), 'isOver2');
            $duplicate_member_id = key($duplicate_member_ids);
            //set line no
            $res['error_line_no'] = array_search($duplicate_member_id, $this->csv_member_ids) + 2;
            $res['error_msg'] = __d('gl', "重複したメンバーIDが含まれています。");
            return $res;
        }
        //coach id check
        $this->csv_coach_ids = array_filter($this->csv_coach_ids, "strlen");
        //Error if the unregistered coach is not included in the member ID
        foreach ($this->csv_coach_ids as $k => $v) {
            $key = array_search($v, $this->csv_member_ids);
            if ($key === false) {
                $res['error_line_no'] = $k + 2;
                $res['error_msg'] = __d('gl', "存在しないメンバーIDがコーチIDに含まれています。");
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
                    $res['error_msg'] = __d('gl', "存在しないメンバーIDが評価者IDに含まれています。");
                    return $res;
                }
            }
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
            $res['error_msg'] = __d('gl', "データが１件もありません。");
            return $res;
        }
        //validation each line of csv data.
        foreach ($csv_data as $key => $row) {
            //first record check
            if ($key == 0) {
                if (!empty(array_diff($row, $this->_getCsvHeading()))) {
                    $res['error_msg'] = __d('gl', "見出しが一致しません。");
                    return $res;
                }
                continue;
            }
            //set line no
            $res['error_line_no'] = $key + 1;
            //key name set
            if (!($row = copyKeyName($this->_getCsvHeading(), $row))) {
                $res['error_msg'] = __d('gl', "項目数が一致しません。");
                return $res;
            }

            $row = Hash::expand($row);
            $this->set($row);
            if (!$this->validates()) {
                $res['error_msg'] = current(array_shift($this->validationErrors));
                return $res;
            }

            $this->csv_emails[] = $row['email'];
            $this->csv_datas[$key]['Email'] = ['email' => $row['email']];

            //exists member id check(after check)
            $this->csv_member_ids[] = $row['member_no'];
            $this->csv_datas[$key]['TeamMember']['member_no'] = $row['member_no'];
            $this->csv_datas[$key]['User']['first_name'] = $row['first_name'];
            $this->csv_datas[$key]['User']['last_name'] = $row['last_name'];
            $this->csv_datas[$key]['TeamMember']['admin_flg'] = strtolower($row['admin_flg']) === 'on' ? true : false;
            $this->csv_datas[$key]['TeamMember']['evaluation_enable_flg'] = strtolower($row['evaluation_enable_flg']) === 'on' ? true : false;
            if (viaIsSet($row['member_type'])) {
                $this->csv_datas[$key]['MemberType']['name'] = $row['member_type'];
            }
            if (viaIsSet($row['language']) && viaIsSet($row['local_first_name']) && viaIsSet($row['local_last_name'])) {
                $this->csv_datas[$key]['LocalName']['language'] = $row['language'];
                $this->csv_datas[$key]['LocalName']['first_name'] = $row['local_first_name'];
                $this->csv_datas[$key]['LocalName']['last_name'] = $row['local_last_name'];
            }
            if (viaIsSet($row['phone_no'])) {
                $this->csv_datas[$key]['User']['phone_no'] = str_replace(["-", "(", ")"], '', $row['phone_no']);
            }
            if (viaIsSet($row['gender'])) {
                $this->csv_datas[$key]['User']['gender_type'] = $row['gender'] === 'male' ? User::TYPE_GENDER_MALE : User::TYPE_GENDER_FEMALE;
            }
            //[12]Birth Year
            if (viaIsSet($row['birth_year']) && viaIsSet($row['birth_month']) && viaIsSet($row['birth_day'])) {
                $this->csv_datas[$key]['User']['birth_day'] = $row['birth_year'] . '/' . $row['birth_month'] . '/' . $row['birth_day'];
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
            if (viaIsSet($row['coach_member_no'])) {
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
        }

        //email exists check
        //E-mail address should not be duplicated
        if (count($this->csv_emails) != count(array_unique($this->csv_emails))) {
            $duplicate_emails = array_filter(array_count_values($this->csv_emails), 'isOver2');
            $duplicate_email = key($duplicate_emails);
            //set line no
            $res['error_line_no'] = array_search($duplicate_email, $this->csv_emails) + 2;
            $res['error_msg'] = __d('gl', "重複したメールアドレスが含まれています。");
            return $res;
        }

        //already joined team check
        $joined_emails = $this->User->Email->getEmailsBelongTeamByEmail($this->csv_emails);
        foreach ($joined_emails as $email) {
            //set line no
            $res['error_line_no'] = array_search($email['Email']['email'], $this->csv_emails) + 2;
            $res['error_msg'] = __d('gl', "既にチームに参加しているメールアドレスです。");
            return $res;
        }

        //member id duplicate check
        if (count($this->csv_member_ids) != count(array_unique($this->csv_member_ids))) {
            $duplicate_member_ids = array_filter(array_count_values($this->csv_member_ids), 'isOver2');
            $duplicate_member_id = key($duplicate_member_ids);
            //set line no
            $res['error_line_no'] = array_search($duplicate_member_id, $this->csv_member_ids) + 2;
            $res['error_msg'] = __d('gl', "重複したメンバーIDが含まれています。");
            return $res;
        }

        //exists member id check
        $members = $this->find('all',
                               [
                                   'conditions' => ['team_id' => $this->current_team_id, 'member_no' => $this->csv_member_ids],
                                   'fields'     => ['member_no']
                               ]
        );
        if (viaIsSet($members[0]['TeamMember']['member_no'])) {
            $res['error_line_no'] = array_search($members[0]['TeamMember']['member_no'], $this->csv_member_ids) + 2;
            $res['error_msg'] = __d('gl', "既に存在するメンバーIDです。");
            return $res;
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
                $res['error_msg'] = __d('gl', "存在しないメンバーIDがコーチIDに含まれています。");
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
                    $res['error_msg'] = __d('gl', "存在しないメンバーIDが評価者IDに含まれています。");
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

    function getAllMembersCsvData($team_id = null)
    {
        if (!$team_id) {
            $team_id = $this->current_team_id;
        }
        $options = [
            'conditions' => ['TeamMember.team_id' => $team_id,],
            'fields'     => ['member_no', 'coach_user_id', 'active_flg', 'admin_flg', 'evaluation_enable_flg'],
            'order'      => ['TeamMember.member_no ASC'],
            'contain'    => [
                'User'       => [
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
                ],
                'MemberType' => [
                    'fields' => ['name']
                ],
            ]
        ];
        $all_users = $this->find('all', $options);

        //convert csv data
        $csv_data = [];
        foreach ($all_users as $k => $v) {
            if (!viaIsSet($v['User']['id'])) {
                unset($all_users[$k]);
                continue;
            }
            $csv_data[$k]['email'] = viaIsSet($v['User']['PrimaryEmail']['email']) ? $v['User']['PrimaryEmail']['email'] : null;
            $csv_data[$k]['first_name'] = viaIsSet($v['User']['first_name']) ? $v['User']['first_name'] : null;
            $csv_data[$k]['last_name'] = viaIsSet($v['User']['last_name']) ? $v['User']['last_name'] : null;
            $csv_data[$k]['member_no'] = viaIsSet($v['TeamMember']['member_no']) ? $v['TeamMember']['member_no'] : null;
            $csv_data[$k]['active_flg'] = viaIsSet($v['TeamMember']['active_flg']) && $v['TeamMember']['active_flg'] ? 'ON' : 'OFF';
            $csv_data[$k]['admin_flg'] = viaIsSet($v['TeamMember']['admin_flg']) && $v['TeamMember']['admin_flg'] ? 'ON' : 'OFF';
            $csv_data[$k]['evaluation_enable_flg'] = viaIsSet($v['TeamMember']['evaluation_enable_flg']) && $v['TeamMember']['evaluation_enable_flg'] ? 'ON' : 'OFF';
            $csv_data[$k]['member_type'] = viaIsSet($v['MemberType']['name']) ? $v['MemberType']['name'] : null;
            //group
            if (viaIsSet($v['User']['MemberGroup'])) {
                foreach ($v['User']['MemberGroup'] as $g_k => $g_v) {
                    $key_index = $g_k + 1;
                    $csv_data[$k]['group.' . $key_index] = viaIsSet($g_v['Group']['name']) ? $g_v['Group']['name'] : null;
                }
            }
            //coach after

            //evaluator after
        }

        //set coach member #
        foreach ($all_users as $k => $v) {
            if (!viaIsSet($v['TeamMember']['coach_user_id'])) {
                continue;
            }
            $options = [
                'conditions' => ['team_id' => $team_id, 'user_id' => $v['TeamMember']['coach_user_id']],
                'fields'     => ['member_no']
            ];
            $coach_member = $this->find('first', $options);
            $csv_data[$k]['coach_member_no'] = viaIsSet($coach_member['TeamMember']['member_no']) ? $coach_member['TeamMember']['member_no'] : null;
        }
        //set evaluator member #
        foreach ($all_users as $k => $v) {
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
                if (viaIsSet($r_v['EvaluatorUser']['TeamMember'][0]['member_no'])) {
                    $csv_data[$k]['evaluator_member_no.' . $key_index] = $r_v['EvaluatorUser']['TeamMember'][0]['member_no'];
                }
            }
        }
        //add all colum
        $default_csv = $this->_getCsvHeading(false);
        foreach ($default_csv as $k => $v) {
            $default_csv[$k] = null;
        }
        foreach ($csv_data as $k => $v) {
            $csv_data[$k] = Hash::merge($default_csv, $v);
        }

        return $csv_data;
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
                'email'                 => __d('gl', "メール(*)"),
                'member_no'             => __d('gl', "メンバーID(*)"),
                'first_name'            => __d('gl', "ローマ字名(*)"),
                'last_name'             => __d('gl', "ローマ字姓(*)"),
                'admin_flg'             => __d('gl', "管理者(*)"),
                'evaluation_enable_flg' => __d('gl', "評価対象(*)"),
                'member_type'           => __d('gl', "メンバータイプ"),
                'language'              => __d('gl', "ローカル姓名の言語コード"),
                'local_first_name'      => __d('gl', "ローカル名"),
                'local_last_name'       => __d('gl', "ローカル姓"),
                'phone_no'              => __d('gl', "電話"),
                'gender'                => __d('gl', "性別"),
                'birth_year'            => __d('gl', "誕生年"),
                'birth_month'           => __d('gl', "誕生月"),
                'birth_day'             => __d('gl', "誕生日"),
                'group.1'               => __d('gl', "グループ1"),
                'group.2'               => __d('gl', "グループ2"),
                'group.3'               => __d('gl', "グループ3"),
                'group.4'               => __d('gl', "グループ4"),
                'group.5'               => __d('gl', "グループ5"),
                'group.6'               => __d('gl', "グループ6"),
                'group.7'               => __d('gl', "グループ7"),
                'coach_member_no'       => __d('gl', "コーチID"),
                'evaluator_member_no.1' => __d('gl', "評価者1"),
                'evaluator_member_no.2' => __d('gl', "評価者2"),
                'evaluator_member_no.3' => __d('gl', "評価者3"),
                'evaluator_member_no.4' => __d('gl', "評価者4"),
                'evaluator_member_no.5' => __d('gl', "評価者5"),
                'evaluator_member_no.6' => __d('gl', "評価者6"),
                'evaluator_member_no.7' => __d('gl', "評価者7"),
            ];
        }

        return [
            'email'                 => __d('gl', "メール(*, 変更できません)"),
            'first_name'            => __d('gl', "ローマ字名(*, 変更できません)"),
            'last_name'             => __d('gl', "ローマ字姓(*, 変更できません)"),
            'member_no'             => __d('gl', "メンバーID(*)"),
            'active_flg'            => __d('gl', "メンバーアクティブ状態(*)"),
            'admin_flg'             => __d('gl', "管理者(*)"),
            'evaluation_enable_flg' => __d('gl', "評価対象(*)"),
            'member_type'           => __d('gl', "メンバータイプ"),
            'group.1'               => __d('gl', "グループ1"),
            'group.2'               => __d('gl', "グループ2"),
            'group.3'               => __d('gl', "グループ3"),
            'group.4'               => __d('gl', "グループ4"),
            'group.5'               => __d('gl', "グループ5"),
            'group.6'               => __d('gl', "グループ6"),
            'group.7'               => __d('gl', "グループ7"),
            'coach_member_no'       => __d('gl', "コーチID"),
            'evaluator_member_no.1' => __d('gl', "評価者1"),
            'evaluator_member_no.2' => __d('gl', "評価者2"),
            'evaluator_member_no.3' => __d('gl', "評価者3"),
            'evaluator_member_no.4' => __d('gl', "評価者4"),
            'evaluator_member_no.5' => __d('gl', "評価者5"),
            'evaluator_member_no.6' => __d('gl', "評価者6"),
            'evaluator_member_no.7' => __d('gl', "評価者7"),
        ];

    }

    function _setCsvValidateRule($new = true)
    {
        $common_validate = [
            'email'                 => [
                'notEmpty' => [
                    'rule'    => 'notEmpty',
                    'message' => __d('validate', "%sは必須項目です。", __d('gl', "メールアドレス"))
                ],
                'email'    => [
                    'rule'    => ['email'],
                    'message' => __d('validate', "%sが正しくありません。", __d('gl', "メールアドレス"))
                ],
            ],
            'member_no'             => [
                'notEmpty'        => [
                    'rule'    => 'notEmpty',
                    'message' => __d('validate', "%sは必須項目です。", __d('gl', "メンバーID"))
                ],
                'maxLength'       => [
                    'rule'    => ['maxLength', 64],
                    'message' => __d('validate', "%sは64文字以内で入力してください。", __d('gl', "メンバーID"))
                ],
                'isNotExistArray' => [
                    'rule'       => ['isNotExistArray', 'evaluator_member_no'],
                    'message'    => __d('gl', "%sに本人のIDを指定する事はできません。", __d('gl', "評価者ID")),
                    'allowEmpty' => true,
                ],
            ],
            'first_name'            => [
                'maxLength'      => [
                    'rule'    => ['maxLength', 64],
                    'message' => __d('validate', "%sは64文字以内で入力してください。", __d('gl', "ファーストネーム"))
                ],
                'notEmpty'       => [
                    'rule'    => 'notEmpty',
                    'message' => __d('validate', "%sは必須項目です。", __d('gl', "ファーストネーム"))
                ],
                'isAlphabetOnly' => [
                    'rule'    => 'isAlphabetOnly',
                    'message' => __d('validate', "%sはアルファベットのみで入力してください。", __d('gl', "ファーストネーム"))
                ],
            ],
            'last_name'             => [
                'maxLength'      => [
                    'rule'    => ['maxLength', 64],
                    'message' => __d('validate', "%sは64文字以内で入力してください。", __d('gl', "ラストネーム"))
                ],
                'notEmpty'       => [
                    'rule'    => 'notEmpty',
                    'message' => __d('validate', "%sは必須項目です。", __d('gl', "ラストネーム"))
                ],
                'isAlphabetOnly' => [
                    'rule'    => 'isAlphabetOnly',
                    'message' => __d('validate', "%sはアルファベットのみで入力してください。", __d('gl', "ラストネーム"))
                ],
            ],
            'admin_flg'             => [
                'notEmpty'  => [
                    'rule'    => 'notEmpty',
                    'message' => __d('validate', "%sは必須項目です。", __d('gl', "管理者"))
                ],
                'isOnOrOff' => [
                    'rule'    => 'isOnOrOff',
                    'message' => __d('validate', "%sは'ON'もしくは'OFF'のいずれかである必要があいます。", __d('gl', '管理者'))
                ],
            ],
            'evaluation_enable_flg' => [
                'notEmpty'  => [
                    'rule'    => 'notEmpty',
                    'message' => __d('validate', "%sは必須項目です。", __d('gl', "評価者"))
                ],
                'isOnOrOff' => [
                    'rule'    => 'isOnOrOff',
                    'message' => __d('validate', "%sは'ON'もしくは'OFF'のいずれかである必要があいます。", __d('gl', '評価者'))
                ],
            ],
            'member_type'           => [
                'maxLength' => [
                    'rule'    => ['maxLength', 64],
                    'message' => __d('validate', "%sは64文字以内で入力してください。", __d('gl', "メンバータイプ"))
                ],
            ],
            'group'                 => [
                'isAlignLeft'     => [
                    'rule'       => 'isAlignLeft',
                    'message'    => __d('validate', "%sは左詰めで記入してください。", __d('gl', "グループ名")),
                    'allowEmpty' => true,
                ],
                'isNotDuplicated' => [
                    'rule'       => 'isNotDuplicated',
                    'message'    => __d('validate', "%sが重複しています。", __d('gl', "グループ名")),
                    'allowEmpty' => true,
                ],
                'maxLengthArray'  => [
                    'rule'       => ['maxLengthArray', 64],
                    'message'    => __d('validate', "%sは64文字以内で入力してください。", __d('gl', "グループ名")),
                    'allowEmpty' => true,
                ],
            ],
            'coach_member_no'       => [
                'isNotEqual' => [
                    'rule'       => ['isNotEqual', 'member_no'],
                    'message'    => __d('validate', "%sに本人のIDを指定する事はできません。", __d('gl', "コーチID")),
                    'allowEmpty' => true,
                ],
            ],
            'evaluator_member_no'   => [
                'isAlignLeft'     => [
                    'rule'       => 'isAlignLeft',
                    'message'    => __d('validate', "%sは左詰めで記入してください。", __d('gl', "評価者")),
                    'allowEmpty' => true,
                ],
                'isNotDuplicated' => [
                    'rule'       => 'isNotDuplicated',
                    'message'    => __d('validate', "%sが重複しています。", __d('gl', "評価者")),
                    'allowEmpty' => true,
                ],
                'maxLengthArray'  => [
                    'rule'       => ['maxLengthArray', 64],
                    'message'    => __d('validate', "%sは64文字以内で入力してください。", __d('gl', "評価者")),
                    'allowEmpty' => true,
                ],
            ],
        ];
        $validateOfNew = [
            'phone_no'         => [
                'phoneNo' => [
                    'rule'       => 'phoneNo',
                    'message'    => __d('validate', "電話番号が正しくありません。使用できる文字は半角数字、'-()'です。"),
                    'allowEmpty' => true,
                ],
            ],
            'gender'           => [
                'inList' => [
                    'rule'       => ['inList', ['male', 'female']],
                    'message'    => __d('validate', "サポートされていない性別表記です。'male'もしくは'female'で記入してください。"),
                    'allowEmpty' => true,
                ],
            ],
            'language'         => [
                'inList' => [
                    'rule'       => ['inList', $this->support_lang_codes],
                    'message'    => __d('validate', "サポートされていないローカル姓名の言語コードです。"),
                    'allowEmpty' => true,
                ],
            ],
            'local_first_name' => [
                'maxLength' => [
                    'rule'    => ['maxLength', 64],
                    'message' => __d('validate', "%sは64文字以内で入力してください。", __d('gl', "ローカル名"))
                ],
            ],
            'local_last_name'  => [
                'maxLength' => [
                    'rule'    => ['maxLength', 64],
                    'message' => __d('validate', "%sは64文字以内で入力してください。", __d('gl', "ローカル姓"))
                ],
            ],
            'birth_year'       => [
                'isAllOrNothing' => [
                    'rule'    => ['isAllOrNothing', ['birth_year', 'birth_month', 'birth_day']],
                    'message' => __d('validate', "誕生日を記入する場合は年月日のすべての項目を記入してください。"),
                ],
                'birthYear'      => [
                    'rule'       => 'birthYear',
                    'message'    => __d('validate', "%sが正しくありません。", __d('gl', "誕生年")),
                    'allowEmpty' => true,
                ],
            ],
            'birth_month'      => [
                'birthMonth' => [
                    'rule'       => 'birthMonth',
                    'message'    => __d('validate', "%sが正しくありません。", __d('gl', "誕生月")),
                    'allowEmpty' => true,
                ],
            ],
            'birth_day'        => [
                'birthDay' => [
                    'rule'       => 'birthDay',
                    'message'    => __d('validate', "%sが正しくありません。", __d('gl', "誕生日")),
                    'allowEmpty' => true,
                ],
            ],
        ];
        $validateOfUpdate = [
            'active_flg' => [
                'notEmpty'  => [
                    'rule'    => 'notEmpty',
                    'message' => __d('validate', "%sは必須項目です。", __d('gl', "メンバーアクティブ状態"))
                ],
                'isOnOrOff' => [
                    'rule'    => 'isOnOrOff',
                    'message' => __d('validate', "%sは'ON'もしくは'OFF'のいずれかである必要があいます。", __d('gl', 'メンバーアクティブ状態'))
                ],
            ],
        ];
        if ($new) {
            $validate = $common_validate + $validateOfNew;
        }
        else {
            $validate = $common_validate + $validateOfUpdate;
        }
        $this->validateBackup = $this->validate;
        $this->validate = $validate;
    }

    function _setValidateFromBackUp()
    {
        $this->validate = $this->validateBackup;
    }

    /**
     * ログインしているユーザーのコーチIDを取得する
     *
     * @param $user_id
     * @param $team_id
     *
     * @return array|null
     */
    function selectCoachUserIdFromTeamMembersTB($user_id, $team_id)
    {
        // 検索テーブル: team_members
        // 取得カラム: coach_user_id
        // 条件: user_id, team_id
        $options = [
            'fields'     => ['coach_user_id'],
            'conditions' => [
                'TeamMember.user_id' => $user_id,
                'TeamMember.team_id' => $team_id,
            ],
        ];
        return $this->find('first', $options);
    }

    /**
     * ログインしているユーザーが管理するのメンバーIDを取得する
     *
     * @param $user_id
     * @param $team_id
     *
     * @return array|null
     */
    function selectUserIdFromTeamMembersTB($user_id, $team_id)
    {
        // 検索テーブル: team_members
        // 取得カラム: user_id
        // 条件: coach_user_id = パラメータ1 team_id = パラメータ2
        $options = [
            'fields'     => ['user_id'],
            'conditions' => [
                'TeamMember.coach_user_id' => $user_id,
                'TeamMember.team_id'       => $team_id,
            ],
        ];
        return $this->find('list', $options);
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
                'team_id' => $this->current_team_id,
            ],
            'fields'     => [
                'user_id'
            ],
        ];
        $member_list = $this->find("list",$options);
        $res = $this->User->Collaborator->getCollaboGoalList($member_list, true, $limit, $page);
        return $res;
    }

}