<?php
App::uses('AppModel', 'Model');

/**
 * TeamMember Model
 *
 * @property User        $User
 * @property Team        $Team
 * @property User        $CoachUser
 * @property Group       $Group
 * @property JobCategory $JobCategory
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
        $team_member['TeamMember']['last_login'] = time();
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
            if (isset($this->me['id'])) {
                $uid = $this->me['id'];
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

    public function getAllMemberUserIdList()
    {
        $options = [
            'conditions' => [
                'team_id'    => $this->current_team_id,
                'active_flg' => true,
            ],
            'fields'     => ['user_id'],
        ];
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

}
