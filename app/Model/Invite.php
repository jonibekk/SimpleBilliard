<?php
App::uses('AppModel', 'Model');

/**
 * Invite Model
 *
 * @property User $FromUser
 * @property User $ToUser
 * @property Team $Team
 */
class Invite extends AppModel
{

    public $tokenData = [];
    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'email'          => ['email' => ['rule' => ['email']]],
        'email_verified' => ['boolean' => ['rule' => ['boolean']]],
        'del_flg'        => ['boolean' => ['rule' => ['boolean']]],
    ];

    //The Associations below have been created with all possible keys, those that are not needed can be removed

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [
        'FromUser' => ['className' => 'User', 'foreignKey' => 'from_user_id',],
        'ToUser'   => ['className' => 'User', 'foreignKey' => 'to_user_id',],
        'Team',
    ];

    function saveInvite($email, $team_id, $from_uid, $message = null)
    {
        //既に招待済みの場合は古い招待メールを削除
        $exists = $this->find('first',
                              ['conditions' => [
                                  'team_id' => $team_id,
                                  'email'   => $email
                              ]]);
        if (!empty($exists)) {
            $this->delete($exists['Invite']['id']);
        }

        $data = [];
        $data['Invite']['from_user_id'] = $from_uid;
        $data['Invite']['team_id'] = $team_id;
        $data['Invite']['email'] = $email;
        $data['Invite']['email_token'] = $this->generateToken();
        $data['Invite']['email_token_expires'] = $this->getTokenExpire();
        //既に登録済みのユーザの場合はuser_idをセット
        if (!empty($user_id = $this->ToUser->Email->findByEmail($email))) {
            $data['Invite']['to_user_id'] = $user_id['Email']['user_id'];
        }
        //メッセージがある場合は
        if ($message) {
            $data['Invite']['message'] = $message;
        }
        $this->create();
        $res = $this->save($data);
        return $res;
    }

    /**
     * トークンのチェック
     *
     * @param $token
     *
     * @return bool
     * @throws RuntimeException
     */
    public function confirmToken($token)
    {
        $invite = $this->getByToken($token);
        if (empty($invite)) {
            throw new RuntimeException(
                __d('exception', "トークンが正しくありません。送信されたメールを再度ご確認下さい。"));
        }
        if ($invite['Invite']['email_verified']) {
            throw new RuntimeException(__d('exception', 'このトークンは使用済みです。'));
        }
        $expires = strtotime($invite['Invite']['email_token_expires']);
        if ($expires < time()) {
            throw new RuntimeException(__d('exception', 'トークンの期限が切れています。'));
        }
        return true;
    }

    /**
     * 招待の認証
     *
     * @param string $token The token that wa sent to the user
     *
     * @internal param $uid
     * @return array On success it returns the user data record
     */
    public function verify($token)
    {
        $this->confirmToken($token);
        $invite = $this->getByToken($token);
        $invite['Invite']['email_verified'] = true;
        $res = $this->save($invite);
        return $res;
    }

    function getByToken($token)
    {
        if (empty($this->tokenData)) {
            return $this->setInviteByToken($token);
        }
        else {
            return $this->tokenData;
        }
    }

    function isForMe($token, $uid)
    {
        $invite = $this->getByToken($token);
        if (isset($invite['Invite']['to_user_id']) && !empty($invite['Invite']['to_user_id'])) {
            return $invite['Invite']['to_user_id'] === $uid;
        }
        return false;
    }

    function setInviteByToken($token)
    {
        $options = [
            'conditions' => [
                'email_token' => $token
            ],
        ];
        $invite = $this->find('first', $options);
        $this->tokenData = $invite;
        return $this->tokenData;
    }

    function isUser($token)
    {
        $invite = $this->getByToken($token);
        if (isset($invite['Invite']['to_user_id']) && !empty($invite['Invite']['to_user_id'])) {
            return true;
        }
        return false;
    }

}
