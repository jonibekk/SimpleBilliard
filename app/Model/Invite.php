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

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'from_user_id'   => ['uuid' => ['rule' => ['uuid']]],
        'team_id'        => ['uuid' => ['rule' => ['uuid']]],
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
        //既に招待済みの場合は古い招待メールを削除
        if (!empty($user_id)) {
            $exists = $this->find('first',
                                  ['conditions' => [
                                      'team_id'    => $team_id,
                                      'to_user_id' => $user_id['Email']['user_id']
                                  ]]);
            if (!empty($exists)) {
                $this->delete($exists['Invite']['id']);
            }
        }
        $this->create();
        $res = $this->save($data);
        return $res;
    }
}
