<?php

App::uses('Email', 'Model');
App::uses('User', 'Model');
App::import('Model/User', 'UserSignUpFromCsv');

class UserRegistererService
{
    /** @var User */
    private $User;
    /** @var Email */
    private $Email;

    public function __construct()
    {
        $this->User = ClassRegistry::init('User');
        $this->Email = ClassRegistry::init('Email');
    }

    /**
     * @param UserSignUpFromCsv $signUpModel
     * @return int
     * @throws Exception
     */
    public function signUpFromCsv(UserSignUpFromCsv $signUpModel): int
    {
        $this->User->create();
        $user = $this->User->save([
            'default_team_id' => $signUpModel->getDefaultTeamId(),
            'first_name' => $signUpModel->getFirstName(),
            'last_name' => $signUpModel->getLastName(),
            'password' => $this->User->generateHash($signUpModel->getPassword()),
            'update_email_flg' => $signUpModel->getUpdateEmailFlg(),
            'timezone' => $signUpModel->getTimezone(),
            'language' => $signUpModel->getLanguage(),
            'agreed_terms_of_service_id' => $signUpModel->getAgreedTermsOfServiceId(),
            'active_flg' => $signUpModel->getActiveFlg(),
        ]);

        $userId = Hash::get($user, 'User.id');
        $this->Email->create();
        $email = $this->Email->save([
            'user_id' => $userId,
            'email' => $signUpModel->getEmail(),
            'email_verified' => $signUpModel->getEmailVerified()
        ]);

        $primaryEmailId = Hash::get($email, 'Email.id');
        $this->User->save(['User' => ['primary_email_id' => $primaryEmailId]]);

        return $userId;
    }
}