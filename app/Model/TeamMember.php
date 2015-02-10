<?php
App::uses('AppModel', 'Model');

/**
 * TeamMember Model
 *
 * @property User              $User
 * @property Team              $Team
 * @property MemberType        $MemberType
 * @property User              $CoachUser
 * @property Group             $Group
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
        'Group',
        'JobCategory',
        'MemberType',
    ];

    public $myStatusWithTeam = [];

    /**
     * 現在有効なチーム一覧を取得
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
        $emails = [];
        $member_ids = [];
        $coach_ids = [];
        $rater_ids = [];
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

            //[0]Mail(*)
            if (!viaIsSet($row[0])) {
                $res['error_msg'] = __d('gl', "メールアドレスは必須項目です。");
                return $res;
            }
            if (!Validation::email($row[0])) {
                $res['error_msg'] = __d('gl', "メールアドレスが正しくありません。");
                return $res;
            }
            //already joined team check(after check)
            $emails[] = $row[0];

            //[1]Member ID(*)
            if (!viaIsSet($row[1])) {
                $res['error_msg'] = __d('gl', "メンバーIDは必須項目です。");
                return $res;
            }
            //exists member id check(after check)
            $member_ids[] = $row[1];

            //[2]First Name(*)
            if (!viaIsSet($row[2])) {
                $res['error_msg'] = __d('gl', "ローマ字名は必須項目です。");
                return $res;
            }
            //user validation
            $this->User->set(['first_name' => $row[2]]);
            if (!$this->User->validates()) {
                $res['error_msg'] = __d('gl', "ローマ字名はローマ字のみで入力してください。");
                return $res;
            }
            //[3]Last Name(*)
            if (!viaIsSet($row[3])) {
                $res['error_msg'] = __d('gl', "ローマ字姓は必須項目です。");
                return $res;
            }
            //user validation
            $this->User->set(['last_name' => $row[3]]);
            if (!$this->User->validates()) {
                $res['error_msg'] = __d('gl', "ローマ字姓はローマ字のみで入力してください。");
                return $res;
            }

            //[4]Administrator(*)
            if (!viaIsSet($row[4])) {
                $res['error_msg'] = __d('gl', "管理者は必須項目です。");
                return $res;
            }
            // ON or OFF check
            if (!isOnOrOff($row[4])) {
                $res['error_msg'] = __d('gl', "%sは'ON'もしくは'OFF'のいずれかである必要があいます。", __d('gl', '管理者'));
                return $res;
            }

            //[5]Evaluated(*)
            if (!viaIsSet($row[5])) {
                $res['error_msg'] = __d('gl', "評価対象は必須項目です。");
                return $res;
            }
            // ON or OFF check
            if (!isOnOrOff($row[5])) {
                $res['error_msg'] = __d('gl', "%sは'ON'もしくは'OFF'のいずれかである必要があいます。", __d('gl', '評価対象'));
                return $res;
            }
            //[6]Member Type
            //no check

            //[7]Local Name Language Code
            //available language code check
            if (viaIsSet($row[7]) && array_search($row[7], $this->support_lang_codes) === false) {
                $res['error_msg'] = __d('gl', "'%s'はサポートされていないローカル姓名の言語コードです。", $row[7]);
                return $res;
            }

            //[8]Local First Name
            //no check

            //[9]Local Last Name
            //no check

            //[10]Phone
            //validation check
            if (viaIsSet($row[10]) && !preg_match('/^[0-9-\(\)]+$/', $row[10])) {
                $res['error_msg'] = __d('gl', "'%s'の電話番号は正しくありません。使用できる文字は半角数字、'-()'です。", $row[10]);
                return $res;
            }

            //[11]Gender
            //validation check
            if (viaIsSet($row[11]) && array_search($row[11], ['male', 'female']) === false) {
                $res['error_msg'] = __d('gl', "'%s'はサポートされていない性別表記です。'male'もしくは'female'で記入してください。", $row[11]);
                return $res;
            }

            //[12]Birth Year
            //all or nothing check
            if (!isAllOrNothing([$row[12], $row[13], $row[14]])) {
                $res['error_msg'] = __d('gl', "誕生日を記入する場合は年月日のすべての項目を記入してください。");
                return $res;
            }
            //validation check
            if (viaIsSet($row[12]) && !preg_match('/^\d{4}$/', $row[12])) {
                $res['error_msg'] = __d('gl', "'%s'は誕生年として正しくありません。", $row[12]);
                return $res;
            }

            //[13]Birth Month
            //validation check
            if (viaIsSet($row[13]) && !preg_match('/^\d{1,2}$/', $row[13])) {
                $res['error_msg'] = __d('gl', "'%s'は誕生月として正しくありません。", $row[13]);
                return $res;
            }

            //[14]Birth Day
            //validation check
            if (viaIsSet($row[14]) && !preg_match('/^\d{1,2}$/', $row[14])) {
                $res['error_msg'] = __d('gl', "'%s'は誕生日として正しくありません。", $row[14]);
                return $res;
            }
            //[15]-[21]Group
            $groups = [$row[15], $row[16], $row[17], $row[18], $row[19], $row[20], $row[21]];
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

            //[22]Coach ID
            //not allow include own member ID
            if (!empty($row[1]) && $row[1] == $row[22]) {
                $res['error_msg'] = __d('gl', "コーチIDに本人のIDを指定する事はできません。");
                return $res;
            }
            //exists check (after check)
            $coach_ids[] = $row[22];

            //[23]-[29]Rater ID
            $raters = [$row[23], $row[24], $row[25], $row[26], $row[27], $row[28], $row[29]];
            if (!isAlignLeft($raters)) {
                $res['error_msg'] = __d('gl', "評価者IDは左詰めで記入してください。");
                return $res;
            }
            //not allow include own member ID
            if (!empty($row[1]) && in_array($row[1], $raters)
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
            //rater id check(after check)
            $rater_ids[] = $filtered_raters;
        }

        //email exists check
        //E-mail address should not be duplicated
        if (count($emails) != count(array_unique($emails))) {
            $duplicate_emails = array_filter(array_count_values($emails), 'isOver2');
            $duplicate_email = key($duplicate_emails);
            //set line no
            $res['error_line_no'] = array_search($duplicate_email, $emails) + 2;
            $res['error_msg'] = __d('gl', "重複したメールアドレスが含まれています。");
            return $res;
        }

        //already joined team check
        $joined_emails = $this->User->Email->getEmailsBelongTeamByEmail($emails);
        foreach ($joined_emails as $email) {
            //set line no
            $res['error_line_no'] = array_search($email['Email']['email'], $emails) + 2;
            $res['error_msg'] = __d('gl', "既にチームに参加しているメールアドレスです。");
            return $res;
        }

        //member id duplicate check
        if (count($member_ids) != count(array_unique($member_ids))) {
            $duplicate_member_ids = array_filter(array_count_values($member_ids), 'isOver2');
            $duplicate_member_id = key($duplicate_member_ids);
            //set line no
            $res['error_line_no'] = array_search($duplicate_member_id, $member_ids) + 2;
            $res['error_msg'] = __d('gl', "重複したメンバーIDが含まれています。");
            return $res;
        }

        //exists member id check
        $members = $this->find('all',
                               [
                                   'conditions' => ['team_id' => $this->current_team_id, 'member_no' => $member_ids],
                                   'fields'     => ['member_no']
                               ]
        );
        if (viaIsSet($members[0]['TeamMember']['member_no'])) {
            $res['error_line_no'] = array_search($members[0]['TeamMember']['member_no'], $member_ids) + 2;
            $res['error_msg'] = __d('gl', "既に存在するメンバーIDです。");
            return $res;
        }

        //coach id check
        $coach_ids = array_filter($coach_ids, "strlen");
        //Coach ID must be already been registered or must be included in the member ID
        //First check coach ID whether registered
        $exists_coach_ids = $this->find('all',
                                        [
                                            'conditions' => ['team_id' => $this->current_team_id, 'member_no' => $coach_ids],
                                            'fields'     => ['member_no']
                                        ]
        );
        //remove the registered coach
        foreach ($exists_coach_ids as $k => $v) {
            $member_no = $v['TeamMember']['member_no'];
            $key = array_search($member_no, $coach_ids);
            if ($key !== false) {
                unset($coach_ids[$key]);
            }
        }
        //Error if the unregistered coach is not included in the member ID
        foreach ($coach_ids as $k => $v) {
            $key = array_search($v, $member_ids);
            if ($key === false) {
                $res['error_line_no'] = $k + 2;
                $res['error_msg'] = __d('gl', "存在しないメンバーIDがコーチIDに含まれています。");
                return $res;
            }
        }

        //rater id check
        //Rater ID must be already been registered or must be included in the member ID
        //remove empty elements
        foreach ($rater_ids as $k => $v) {
            $rater_ids[$k] = array_filter($v, "strlen");
        }

        //Merge all rater ID
        $merged_rater_ids = [];
        foreach ($rater_ids as $v) {
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
            foreach ($rater_ids as $r_k => $r_v) {
                $key = array_search($member_no, $r_v);
                if ($key !== false) {
                    unset($rater_ids[$r_k][$key]);
                }
            }
        }
        //Error if the unregistered rater ID is not included in the member ID
        foreach ($rater_ids as $r_k => $r_v) {
            foreach ($r_v as $k => $v) {
                $key = array_search($v, $member_ids);
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
                __d('gl', "メール(*)"),
                __d('gl', "メンバーID(*)"),
                __d('gl', "ローマ字名(*)"),
                __d('gl', "ローマ字姓(*)"),
                __d('gl', "管理者(*)"),
                __d('gl', "評価対象(*)"),
                __d('gl', "メンバータイプ"),
                __d('gl', "ローカル姓名の言語コード"),
                __d('gl', "ローカル名"),
                __d('gl', "ローカル姓"),
                __d('gl', "電話"),
                __d('gl', "性別"),
                __d('gl', "誕生年"),
                __d('gl', "誕生月"),
                __d('gl', "誕生日"),
                __d('gl', "グループ1"),
                __d('gl', "グループ2"),
                __d('gl', "グループ3"),
                __d('gl', "グループ4"),
                __d('gl', "グループ5"),
                __d('gl', "グループ6"),
                __d('gl', "グループ7"),
                __d('gl', "コーチID"),
                __d('gl', "評価者1"),
                __d('gl', "評価者2"),
                __d('gl', "評価者3"),
                __d('gl', "評価者4"),
                __d('gl', "評価者5"),
                __d('gl', "評価者6"),
                __d('gl', "評価者7"),
            ];
        }

        return [
            __d('gl', "メール(*, 変更できません)"),
            __d('gl', "ローマ字名(*, 変更できません)"),
            __d('gl', "ローマ字姓(*, 変更できません)"),
            __d('gl', "メンバーID(*)"),
            __d('gl', "メンバーアクティブ状態(*)"),
            __d('gl', "管理者(*)"),
            __d('gl', "評価対象(*)"),
            __d('gl', "メンバータイプ"),
            __d('gl', "グループ1"),
            __d('gl', "グループ2"),
            __d('gl', "グループ3"),
            __d('gl', "グループ4"),
            __d('gl', "グループ5"),
            __d('gl', "グループ6"),
            __d('gl', "グループ7"),
            __d('gl', "コーチID"),
            __d('gl', "評価者1"),
            __d('gl', "評価者2"),
            __d('gl', "評価者3"),
            __d('gl', "評価者4"),
            __d('gl', "評価者5"),
            __d('gl', "評価者6"),
            __d('gl', "評価者7"),
        ];

    }

}
