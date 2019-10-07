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
     * @param UserSignUpFromCsv $sign_up_model
     * @return int
     * @throws Exception
     */
    public function signUpFromCsv(UserSignUpFromCsv $sign_up_model): int
    {
        $this->getUserEntity()->create();
        $user = $this->getUserEntity()->save([
            'default_team_id' => $sign_up_model->getDefaultTeamId(),
            'first_name' => $sign_up_model->getFirstName(),
            'last_name' => $sign_up_model->getLastName(),
            'password' => $this->getUserEntity()->generateHash($sign_up_model->getPassword()),
            'update_email_flg' => $sign_up_model->getUpdateEmailFlg(),
            'timezone' => $sign_up_model->getTimezone(),
            'language' => $sign_up_model->getLanguage(),
            'agreed_terms_of_service_id' => $sign_up_model->getAgreedTermsOfServiceId(),
            'active_flg' => $sign_up_model->getActiveFlg(),
        ]);

        $user_id = Hash::get($user, 'User.id');
        $this->getEmailEntity()->create();
        $email = $this->getEmailEntity()->save([
            'user_id' => $user_id,
            'email' => $sign_up_model->getEmail(),
            'email_verified' => $sign_up_model->getEmailVerified()
        ]);

        $primary_email_id = Hash::get($email, 'Email.id');
        $this->getUserEntity()->save(['User' => ['primary_email_id' => $primary_email_id]]);

        return $user_id;
    }

    /**
     * @return User
     */
    protected function getUserEntity(): User
    {
        return $this->User;
    }

    /**
     * @return Email
     */
    protected function getEmailEntity(): Email
    {
        return $this->Email;
    }
}