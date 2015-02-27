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
    private $csv_rater_ids = [];

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
        $this->Team->Rater->deleteAll(['Rater.team_id' => $this->current_team_id]);

        $save_rater_data = [];
        foreach ($this->csv_datas as $row_k => $row_v) {
            if (!viaIsSet($row_v['Rater'])) {
                continue;
            }
            foreach ($row_v['Rater'] as $r_k => $r_v) {
                if ($rater_team_member = $this->getByMemberNo($r_v)) {
                    $save_rater_data[] = [
                        'index'         => $r_k,
                        'team_id'       => $this->current_team_id,
                        'ratee_user_id' => $row_v['User']['id'],
                        'rater_user_id' => $rater_team_member['TeamMember']['user_id'],
                    ];
                }
            }
        }
        if (viaIsSet($save_rater_data)) {
            $this->Team->Rater->create();
            $this->Team->Rater->saveAll($save_rater_data);
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
        $save_rater_data = [];
        foreach ($this->csv_datas as $row_k => $row_v) {
            if (!viaIsSet($row_v['Rater'])) {
                continue;
            }
            foreach ($row_v['Rater'] as $r_k => $r_v) {
                if ($rater_team_member = $this->getByMemberNo($r_v)) {
                    $save_rater_data[] = [
                        'index'         => $r_k,
                        'team_id'       => $this->current_team_id,
                        'ratee_user_id' => $row_v['User']['id'],
                        'rater_user_id' => $rater_team_member['TeamMember']['user_id'],
                    ];
                }
            }
        }
        if (viaIsSet($save_rater_data)) {
            $this->Team->Rater->create();
            $this->Team->Rater->saveAll($save_rater_data);
        }

        $res['success_count'] = count($this->csv_datas);
        return $res;
    }

    function validateUpdateMemberCsvData($csv_data)
    {
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

            //email exists
            if (!in_array($row['email'], $before_emails)) {
                $res['error_msg'] = __d('validate', "メールアドレスは変更できません。");
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

            //Member ID(*)
            if (!viaIsSet($row['member_no'])) {
                $res['error_msg'] = __d('gl', "メンバーIDは必須項目です。");
                return $res;
            }
            $this->csv_member_ids[] = $row['member_no'];
            $this->csv_datas[$key]['TeamMember']['member_no'] = $row['member_no'];
            //Active(*)
            if (!viaIsSet($row['active_flg'])) {
                $res['error_msg'] = __d('gl', "メンバーアクティブ状態は必須項目です。");
                return $res;
            }
            // ON or OFF check
            if (!isOnOrOff($row['active_flg'])) {
                $res['error_msg'] = __d('gl', "%sは'ON'もしくは'OFF'のいずれかである必要があいます。", __d('gl', 'メンバーアクティブ状態'));
                return $res;
            }
            $this->csv_datas[$key]['TeamMember']['active_flg'] = strtolower($row['active_flg']) == "on" ? true : false;
            //Administrator(*)
            if (!viaIsSet($row['admin_flg'])) {
                $res['error_msg'] = __d('gl', "管理者は必須項目です。");
                return $res;
            }
            // ON or OFF check
            if (!isOnOrOff($row['admin_flg'])) {
                $res['error_msg'] = __d('gl', "%sは'ON'もしくは'OFF'のいずれかである必要があいます。", __d('gl', '管理者'));
                return $res;
            }
            $this->csv_datas[$key]['TeamMember']['admin_flg'] = strtolower($row['admin_flg']) == 'on' ? true : false;
            //Evaluated(*)
            if (!viaIsSet($row['evaluation_enable_flg'])) {
                $res['error_msg'] = __d('gl', "評価対象は必須項目です。");
                return $res;
            }

            // ON or OFF check
            if (!isOnOrOff($row['evaluation_enable_flg'])) {
                $res['error_msg'] = __d('gl', "%sは'ON'もしくは'OFF'のいずれかである必要があいます。", __d('gl', '評価対象'));
                return $res;
            }
            $this->csv_datas[$key]['TeamMember']['evaluation_enable_flg'] = strtolower($row['evaluation_enable_flg']) == 'on' ? true : false;
            if (viaIsSet($row['member_type'])) {
                $this->csv_datas[$key]['MemberType']['name'] = $row['member_type'];
            }
            else {
                $this->csv_datas[$key]['TeamMember']['member_type_id'] = null;
            }
            //Group
            $groups = [];
            for ($i = 1; $i <= 7; $i++) {
                $groups[] = $row["group_{$i}"];
            }
            if (!isAlignLeft($groups)) {
                $res['error_msg'] = __d('gl', "グループ名は左詰めで記入してください。");
                return $res;
            }
            //duplicate group check.
            $filtered_groups = array_filter($groups, "strlen");
            if (count(array_unique($filtered_groups)) != count($filtered_groups)
            ) {
                $res['error_msg'] = __d('gl', "グループ名が重複しています。");
                return $res;
            }
            foreach ($groups as $v) {
                if (viaIsSet($v)) {
                    $this->csv_datas[$key]['Group'][] = $v;
                }
            }
            //Coach ID
            //not allow include own member ID
            if (!empty($row['member_no']) && $row['member_no'] == $row['coach_member_no']) {
                $res['error_msg'] = __d('gl', "コーチIDに本人のIDを指定する事はできません。");
                return $res;
            }
            //exists check (after check)
            $this->csv_coach_ids[] = $row['coach_member_no'];
            if (viaIsSet($row['coach_member_no'])) {
                $this->csv_datas[$key]['Coach'] = $row['coach_member_no'];
            }
            else {
                $this->csv_datas[$key]['TeamMember']['coach_user_id'] = null;
            }

            //Rater ID
            $raters = [];
            for ($i = 1; $i <= 7; $i++) {
                $raters[] = $row["rater_member_no_{$i}"];
            }
            if (!isAlignLeft($raters)) {
                $res['error_msg'] = __d('gl', "評価者IDは左詰めで記入してください。");
                return $res;
            }
            //not allow include own member ID
            if (!empty($row['member_no']) && in_array($row['member_no'], $raters)
            ) {
                $res['error_msg'] = __d('gl', "評価者IDに本人のIDを指定する事はできません。");
                return $res;
            }
            //duplicate rater check.
            $filtered_raters = array_filter($raters, "strlen");
            if (count(array_unique($filtered_raters)) != count($filtered_raters)
            ) {
                $res['error_msg'] = __d('gl', "評価者IDが重複しています。");
                return $res;
            }
            foreach ($raters as $v) {
                if (viaIsSet($v)) {
                    $this->csv_datas[$key]['Rater'][] = $v;
                }
            }
            //rater id check(after check)
            $this->csv_rater_ids[] = $filtered_raters;
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
        //rater id check
        //Rater ID must be already been registered or must be included in the member ID
        //remove empty elements
        foreach ($this->csv_rater_ids as $k => $v) {
            $this->csv_rater_ids[$k] = array_filter($v, "strlen");
        }

        //Merge all rater ID
        $merged_rater_ids = [];
        foreach ($this->csv_rater_ids as $v) {
            $merged_rater_ids = array_merge($merged_rater_ids, $v);
        }
        //Error if the unregistered rater ID is not included in the member ID
        foreach ($this->csv_rater_ids as $r_k => $r_v) {
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

            //[0]Mail(*)
            if (!viaIsSet($row['email'])) {
                $res['error_msg'] = __d('gl', "メールアドレスは必須項目です。");
                return $res;
            }
            if (!Validation::email($row['email'])) {
                $res['error_msg'] = __d('gl', "メールアドレスが正しくありません。");
                return $res;
            }
            //already joined team check(after check)

            $this->csv_emails[] = $row['email'];
            $this->csv_datas[$key]['Email'] = ['email' => $row['email']];

            //[1]Member ID(*)
            if (!viaIsSet($row['member_no'])) {
                $res['error_msg'] = __d('gl', "メンバーIDは必須項目です。");
                return $res;
            }
            //exists member id check(after check)
            $this->csv_member_ids[] = $row['member_no'];
            $this->csv_datas[$key]['TeamMember']['member_no'] = $row['member_no'];

            //[2]First Name(*)
            if (!viaIsSet($row['first_name'])) {
                $res['error_msg'] = __d('gl', "ローマ字名は必須項目です。");
                return $res;
            }
            //user validation
            $this->User->set(['first_name' => $row['first_name']]);
            if (!$this->User->validates()) {
                $res['error_msg'] = __d('gl', "ローマ字名はローマ字のみで入力してください。");
                return $res;
            }
            $this->csv_datas[$key]['User']['first_name'] = $row['first_name'];

            //[3]Last Name(*)
            if (!viaIsSet($row['last_name'])) {
                $res['error_msg'] = __d('gl', "ローマ字姓は必須項目です。");
                return $res;
            }
            //user validation
            $this->User->set(['last_name' => $row['last_name']]);
            if (!$this->User->validates()) {
                $res['error_msg'] = __d('gl', "ローマ字姓はローマ字のみで入力してください。");
                return $res;
            }
            $this->csv_datas[$key]['User']['last_name'] = $row['last_name'];

            //[4]Administrator(*)
            if (!viaIsSet($row['admin_flg'])) {
                $res['error_msg'] = __d('gl', "管理者は必須項目です。");
                return $res;
            }
            // ON or OFF check
            if (!isOnOrOff($row['admin_flg'])) {
                $res['error_msg'] = __d('gl', "%sは'ON'もしくは'OFF'のいずれかである必要があいます。", __d('gl', '管理者'));
                return $res;
            }
            $this->csv_datas[$key]['TeamMember']['admin_flg'] = strtolower($row['admin_flg']) === 'on' ? true : false;

            //[5]Evaluated(*)
            if (!viaIsSet($row['evaluation_enable_flg'])) {
                $res['error_msg'] = __d('gl', "評価対象は必須項目です。");
                return $res;
            }

            // ON or OFF check
            if (!isOnOrOff($row['evaluation_enable_flg'])) {
                $res['error_msg'] = __d('gl', "%sは'ON'もしくは'OFF'のいずれかである必要があいます。", __d('gl', '評価対象'));
                return $res;
            }
            $this->csv_datas[$key]['TeamMember']['evaluation_enable_flg'] = strtolower($row['evaluation_enable_flg']) === 'on' ? true : false;
            //[6]Member Type
            //no check
            if (viaIsSet($row['member_type'])) {
                $this->csv_datas[$key]['MemberType']['name'] = $row['member_type'];
            }

            //[7]Local Name Language Code
            //available language code check
            if (viaIsSet($row['language']) && array_search($row['language'], $this->support_lang_codes) === false) {
                $res['error_msg'] = __d('gl', "'%s'はサポートされていないローカル姓名の言語コードです。", $row['language']);
                return $res;
            }
            if (viaIsSet($row['language']) && viaIsSet($row['local_first_name']) && viaIsSet($row['local_last_name'])) {
                $this->csv_datas[$key]['LocalName']['language'] = $row['language'];
                $this->csv_datas[$key]['LocalName']['first_name'] = $row['local_first_name'];
                $this->csv_datas[$key]['LocalName']['last_name'] = $row['local_last_name'];
            }

            //[8]Local First Name
            //no check

            //[9]Local Last Name
            //no check

            //[10]Phone
            //validation check
            if (viaIsSet($row['phone_no']) && !preg_match('/^[0-9-\(\)]+$/', $row['phone_no'])) {
                $res['error_msg'] = __d('gl', "'%s'の電話番号は正しくありません。使用できる文字は半角数字、'-()'です。", $row['phone_no']);
                return $res;
            }
            if (viaIsSet($row['phone_no'])) {
                $this->csv_datas[$key]['User']['phone_no'] = str_replace(["-", "(", ")"], '', $row['phone_no']);
            }

            //[11]Gender
            //validation check
            if (viaIsSet($row['gender']) && array_search($row['gender'], ['male', 'female']) === false) {
                $res['error_msg'] = __d('gl', "'%s'はサポートされていない性別表記です。'male'もしくは'female'で記入してください。", $row['gender']);
                return $res;
            }
            if (viaIsSet($row['gender'])) {
                $this->csv_datas[$key]['User']['gender_type'] = $row['gender'] === 'male' ? User::TYPE_GENDER_MALE : User::TYPE_GENDER_FEMALE;
            }

            //[12]Birth Year
            //all or nothing check
            if (!isAllOrNothing([$row['birth_year'], $row['birth_month'], $row['birth_day']])) {
                $res['error_msg'] = __d('gl', "誕生日を記入する場合は年月日のすべての項目を記入してください。");
                return $res;
            }
            //validation check
            if (viaIsSet($row['birth_year']) && !preg_match('/^\d{4}$/', $row['birth_year'])) {
                $res['error_msg'] = __d('gl', "'%s'は誕生年として正しくありません。", $row['birth_year']);
                return $res;
            }

            //[13]Birth Month
            //validation check
            if (viaIsSet($row['birth_month']) && !preg_match('/^\d{1,2}$/', $row['birth_month'])) {
                $res['error_msg'] = __d('gl', "'%s'は誕生月として正しくありません。", $row['birth_month']);
                return $res;
            }

            //[14]Birth Day
            //validation check
            if (viaIsSet($row['birth_day']) && !preg_match('/^\d{1,2}$/', $row['birth_day'])) {
                $res['error_msg'] = __d('gl', "'%s'は誕生日として正しくありません。", $row['birth_day']);
                return $res;
            }
            if (viaIsSet($row['birth_year']) && viaIsSet($row['birth_month']) && viaIsSet($row['birth_day'])) {
                $this->csv_datas[$key]['User']['birth_day'] = $row['birth_year'] . '/' . $row['birth_month'] . '/' . $row['birth_day'];
            }

            //[15]-[21]Group
            $groups = [];
            for ($i = 1; $i <= 7; $i++) {
                $groups[] = $row["group_{$i}"];
            }
            if (!isAlignLeft($groups)) {
                $res['error_msg'] = __d('gl', "グループ名は左詰めで記入してください。");
                return $res;
            }
            //duplicate group check.
            $filtered_groups = array_filter($groups, "strlen");
            if (count(array_unique($filtered_groups)) != count($filtered_groups)
            ) {
                $res['error_msg'] = __d('gl', "グループ名が重複しています。");
                return $res;
            }
            foreach ($groups as $v) {
                if (viaIsSet($v)) {
                    $this->csv_datas[$key]['Group'][] = $v;
                }
            }

            //[22]Coach ID
            //not allow include own member ID
            if (!empty($row['member_no']) && $row['member_no'] == $row['coach_member_no']) {
                $res['error_msg'] = __d('gl', "コーチIDに本人のIDを指定する事はできません。");
                return $res;
            }
            //exists check (after check)
            $this->csv_coach_ids[] = $row['coach_member_no'];
            if (viaIsSet($row['coach_member_no'])) {
                $this->csv_datas[$key]['Coach'] = $row['coach_member_no'];
            }

            //[23]-[29]Rater ID
            $raters = [];
            for ($i = 1; $i <= 7; $i++) {
                $raters[] = $row["rater_member_no_{$i}"];
            }
            if (!isAlignLeft($raters)) {
                $res['error_msg'] = __d('gl', "評価者IDは左詰めで記入してください。");
                return $res;
            }
            //not allow include own member ID
            if (!empty($row['member_no']) && in_array($row['member_no'], $raters)
            ) {
                $res['error_msg'] = __d('gl', "評価者IDに本人のIDを指定する事はできません。");
                return $res;
            }
            //duplicate rater check.
            $filtered_raters = array_filter($raters, "strlen");
            if (count(array_unique($filtered_raters)) != count($filtered_raters)
            ) {
                $res['error_msg'] = __d('gl', "評価者IDが重複しています。");
                return $res;
            }
            foreach ($raters as $v) {
                if (viaIsSet($v)) {
                    $this->csv_datas[$key]['Rater'][] = $v;
                }
            }

            //rater id check(after check)
            $this->csv_rater_ids[] = $filtered_raters;
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

        //rater id check
        //Rater ID must be already been registered or must be included in the member ID
        //remove empty elements
        foreach ($this->csv_rater_ids as $k => $v) {
            $this->csv_rater_ids[$k] = array_filter($v, "strlen");
        }

        //Merge all rater ID
        $merged_rater_ids = [];
        foreach ($this->csv_rater_ids as $v) {
            $merged_rater_ids = array_merge($merged_rater_ids, $v);
        }
        //Check for rater ID registered
        $exists_rater_ids = $this->find('all',
                                        [
                                            'conditions' => ['team_id' => $this->current_team_id, 'member_no' => $merged_rater_ids],
                                            'fields'     => ['member_no']
                                        ]
        );
        //remove the rater ID of the registered
        foreach ($exists_rater_ids as $er_k => $er_v) {
            $member_no = $er_v['TeamMember']['member_no'];
            foreach ($this->csv_rater_ids as $r_k => $r_v) {
                $key = array_search($member_no, $r_v);
                if ($key !== false) {
                    unset($this->csv_rater_ids[$r_k][$key]);
                }
            }
        }
        //Error if the unregistered rater ID is not included in the member ID
        foreach ($this->csv_rater_ids as $r_k => $r_v) {
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
            $csv_data[$k]['email'] = viaIsSet($v['User']['PrimaryEmail']['email']) ? $v['User']['PrimaryEmail']['email'] : null;
            $csv_data[$k]['first_name'] = viaIsSet($v['User']['first_name']) ? $v['User']['first_name'] : null;
            $csv_data[$k]['last_name'] = viaIsSet($v['User']['last_name']) ? $v['User']['last_name'] : null;
            $csv_data[$k]['member_no'] = viaIsSet($v['TeamMember']['member_no']) ? $v['TeamMember']['member_no'] : null;
            $csv_data[$k]['active_flg'] = viaIsSet($v['TeamMember']['active_flg']) && $v['TeamMember']['active_flg'] ? 'ON' : 'OFF';
            $csv_data[$k]['admin_flg'] = viaIsSet($v['TeamMember']['admin_flg']) && $v['TeamMember']['admin_flg'] ? 'ON' : 'OFF';
            $csv_data[$k]['evaluation_enable_flg'] = viaIsSet($v['TeamMember']['evaluation_enable_flg']) && $v['TeamMember']['evaluation_enable_flg'] ? 'ON' : 'OFF';
            $csv_data[$k]['member_type'] = viaIsSet($v['MemberType']['name']) ? $v['MemberType']['name'] : null;
            //group
            foreach ($v['User']['MemberGroup'] as $g_k => $g_v) {
                $key_index = $g_k + 1;
                $csv_data[$k]['group_' . $key_index] = viaIsSet($g_v['Group']['name']) ? $g_v['Group']['name'] : null;
            }
            //coach after

            //rater after
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
        //set rater member #
        foreach ($all_users as $k => $v) {
            $options = [
                'conditions' => ['team_id' => $team_id, 'ratee_user_id' => $v['User']['id']],
                'fields'     => ['rater_user_id'],
                'contain'    => [
                    'RaterUser' => [
                        'fields'     => ['id'],
                        'TeamMember' => [
                            'conditions' => ['team_id' => $team_id],
                            'fields'     => ['member_no']
                        ],
                    ]
                ]
            ];
            $raters = $this->Team->Rater->find('all', $options);
            foreach ($raters as $r_k => $r_v) {
                $key_index = $r_k + 1;
                if (viaIsSet($r_v['RaterUser']['TeamMember'][0]['member_no'])) {
                    $csv_data[$k]['rater_member_no_' . $key_index] = $r_v['RaterUser']['TeamMember'][0]['member_no'];
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
                'group_1'               => __d('gl', "グループ1"),
                'group_2'               => __d('gl', "グループ2"),
                'group_3'               => __d('gl', "グループ3"),
                'group_4'               => __d('gl', "グループ4"),
                'group_5'               => __d('gl', "グループ5"),
                'group_6'               => __d('gl', "グループ6"),
                'group_7'               => __d('gl', "グループ7"),
                'coach_member_no'       => __d('gl', "コーチID"),
                'rater_member_no_1'     => __d('gl', "評価者1"),
                'rater_member_no_2'     => __d('gl', "評価者2"),
                'rater_member_no_3'     => __d('gl', "評価者3"),
                'rater_member_no_4'     => __d('gl', "評価者4"),
                'rater_member_no_5'     => __d('gl', "評価者5"),
                'rater_member_no_6'     => __d('gl', "評価者6"),
                'rater_member_no_7'     => __d('gl', "評価者7"),
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
            'group_1'               => __d('gl', "グループ1"),
            'group_2'               => __d('gl', "グループ2"),
            'group_3'               => __d('gl', "グループ3"),
            'group_4'               => __d('gl', "グループ4"),
            'group_5'               => __d('gl', "グループ5"),
            'group_6'               => __d('gl', "グループ6"),
            'group_7'               => __d('gl', "グループ7"),
            'coach_member_no'       => __d('gl', "コーチID"),
            'rater_member_no_1'     => __d('gl', "評価者1"),
            'rater_member_no_2'     => __d('gl', "評価者2"),
            'rater_member_no_3'     => __d('gl', "評価者3"),
            'rater_member_no_4'     => __d('gl', "評価者4"),
            'rater_member_no_5'     => __d('gl', "評価者5"),
            'rater_member_no_6'     => __d('gl', "評価者6"),
            'rater_member_no_7'     => __d('gl', "評価者7"),
        ];

    }
}
