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
     * @param array $request_data from Controller
     *
     * @return array
     */
    function saveNewMembersFromCsv($request_data)
    {
        $res = [
            'error'         => false,
            'success_count' => 0,
            'error_line_no' => 0,
            'error_msg'     => null,
        ];
        $csv_array = convertCsvToArray($request_data['Team']['csv_file']['tmp_name']);
        $validate = $this->validateNewMemberCsvData($csv_array);
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

            //Mail(*)
            if (!viaIsSet($row[0])) {
                $res['error_msg'] = __d('gl', "メールアドレスは必須項目です。");
                return $res;
            }
            $this->User->Email->set(['email' => $row[0]]);
            if (!$this->User->Email->validates()) {
                $res['error_msg'] = __d('gl', "メールアドレスが正しくありません。");
                return $res;
            }
            //already joined team check

            //Member ID(*)
            if (!viaIsSet($row[1])) {
                $res['error_msg'] = __d('gl', "メンバーIDは必須項目です。");
                return $res;
            }
            //exists member id check

            //First Name(*)
            if (!viaIsSet($row[2])) {
                $res['error_msg'] = __d('gl', "ローマ字名は必須項目です。");
                return $res;
            }
            //user validation

            //Last Name(*)
            if (!viaIsSet($row[3])) {
                $res['error_msg'] = __d('gl', "ローマ字姓は必須項目です。");
                return $res;
            }
            //user validation

            //Member Active State(*)
            if (!viaIsSet($row[4])) {
                $res['error_msg'] = __d('gl', "メンバーのアクティブ状態は必須項目です。");
                return $res;
            }
            // ON or OFF check
            if (!isOnOrOff($row[4])) {
                $res['error_msg'] = __d('gl', "%sは'ON'もしくは'OFF'のいずれかである必要があいます。", __d('gl', 'メンバーアクティブ状態'));
                return $res;
            }

            //Administrator(*)
            if (!viaIsSet($row[5])) {
                $res['error_msg'] = __d('gl', "管理者は必須項目です。");
                return $res;
            }
            // ON or OFF check
            if (!isOnOrOff($row[5])) {
                $res['error_msg'] = __d('gl', "%sは'ON'もしくは'OFF'のいずれかである必要があいます。", __d('gl', '管理者'));
                return $res;
            }

            //Member Type(*)
            if (!viaIsSet($row[6])) {
                $res['error_msg'] = __d('gl', "メンバータイプは必須項目です。");
                return $res;
            }

            //Evaluated(*)
            if (!viaIsSet($row[7])) {
                $res['error_msg'] = __d('gl', "評価対象は必須項目です。");
                return $res;
            }
            // ON or OFF check
            if (!isOnOrOff($row[7])) {
                $res['error_msg'] = __d('gl', "%sは'ON'もしくは'OFF'のいずれかである必要があいます。", __d('gl', '評価対象'));
                return $res;
            }

            //Group
            //no check

            //Local Name Language Code
            //available language code check
            if (!array_search($row[9], $this->support_lang_codes)) {
                $res['error_msg'] = __d('gl', "%sはサポートされていないローカル姓名の言語コードです。", $row[9]);
                return $res;
            }

            //Local First Name
            //no check

            //Local Last Name
            //no check

            //Phone
            //validation check

            //Gender
            //validation check

            //Birth Year
            //validation check
            //no data or all year month day data check

            //Birth Month
            //validation check
            //no data or all year month day data check

            //Birth Day
            //validation check
            //no data or all year month day data check

            //Coach Member ID
            //exists in team or in csv file check

            //Rater1 Member ID
            //exists in team or in csv file check

            //Rater2 Member ID
            //exists in team or in csv file check

            //Rater3 Member ID
            //exists in team or in csv file check

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
                __d('gl', "メンバーのアクティブ状態(*)"),
                __d('gl', "管理者(*)"),
                __d('gl', "メンバータイプ(*)"),
                __d('gl', "評価対象(*)"),
                __d('gl', "グループ"),
                __d('gl', "ローカル姓名の言語コード"),
                __d('gl', "ローカル名"),
                __d('gl', "ローカル姓"),
                __d('gl', "電話"),
                __d('gl', "性別"),
                __d('gl', "誕生年"),
                __d('gl', "誕生月"),
                __d('gl', "誕生日"),
                __d('gl', "コーチID"),
                __d('gl', "評価者1"),
                __d('gl', "評価者2"),
                __d('gl', "評価者3"),
            ];
        }
        return [
            __d('gl', "メンバーID(*)"),
            __d('gl', "メール(*, 変更できません)"),
            __d('gl', "ローマ字名(*, 変更できません)"),
            __d('gl', "ローマ字姓(*, 変更できません)"),
            __d('gl', "管理者(*)"),
            __d('gl', "メンバータイプ(*)"),
            __d('gl', "評価対象(*)"),
            __d('gl', "グループ"),
            __d('gl', "コーチID"),
            __d('gl', "評価者1"),
            __d('gl', "評価者2"),
            __d('gl', "評価者3"),
        ];

    }

}
