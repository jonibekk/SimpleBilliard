<?php
App::uses('GoalousTestCase', 'Test');
App::uses('User', 'Model');
App::uses('Email', 'Model');
App::import('Service/User', 'UserRegistererService');
App::import('Model/User', 'UserSignUpFromCsv');

/**
 * Class UserRegistererServiceTest
 * @property User  $User
 * @property Email  $Email
 */
class UserRegistererServiceTest extends GoalousTestCase
{
    public $fixtures = [
        'app.email',
        'app.user',
    ];

    public function setUp()
    {
        parent::setUp();
        $this->Email = ClassRegistry::init('Email');
        $this->Email->getDataSource()->truncate('emails');
        $this->User = ClassRegistry::init('User');
        $this->User->getDataSource()->truncate('users');
    }

    /**
     * @group signUpFromCsv
     */
    public function testSignUpFromCsv()
    {
        $signUpFromCsv = $this->createUserSignUpFromCsv();
        $service = new UserRegistererService();
        $userId = $service->signUpFromCsv($signUpFromCsv);

        $actual = $this->Email->find('first', [
            'conditions' => [
                'user_id' => $userId,
                'email' => $signUpFromCsv->getEmail(),
            ],
            'fields' => ['id', 'user_id', 'email', 'email_verified']
        ]);
        $this->assertEquals($userId, Hash::get($actual, 'Email.user_id'));
        $this->assertEquals($signUpFromCsv->getEmail(), Hash::get($actual, 'Email.email'));
        $this->assertEquals($signUpFromCsv->getEmailVerified(), Hash::get($actual, 'Email.email_verified'));

        $emailId = Hash::get($actual, 'Email.id');

        $actual = $this->User->findById($userId);
        $securePassword = $this->User->generateHash($signUpFromCsv->getPassword());
        $this->assertEquals($userId, Hash::get($actual, 'User.id'));
        $this->assertEquals($signUpFromCsv->getDefaultTeamId(), Hash::get($actual, 'User.default_team_id'));
        $this->assertEquals($signUpFromCsv->getFirstName(), Hash::get($actual, 'User.first_name'));
        $this->assertEquals($signUpFromCsv->getLastName(), Hash::get($actual, 'User.last_name'));
        $this->assertEquals($securePassword, Hash::get($actual, 'User.password'));
        $this->assertEquals($signUpFromCsv->getUpdateEmailFlg(), Hash::get($actual, 'User.update_email_flg'));
        $this->assertEquals($signUpFromCsv->getTimezone(), Hash::get($actual, 'User.timezone'));
        $this->assertEquals($signUpFromCsv->getLanguage(), Hash::get($actual, 'User.language'));
        $this->assertEquals($signUpFromCsv->getAgreedTermsOfServiceId(), Hash::get($actual, 'User.agreed_terms_of_service_id'));
        $this->assertEquals($signUpFromCsv->getActiveFlg(), Hash::get($actual, 'User.active_flg'));
        $this->assertEquals($emailId, Hash::get($actual, 'User.primary_email_id'));
    }

    /**
     * @return UserSignUpFromCsv
     */
    private function createUserSignUpFromCsv(): UserSignUpFromCsv
    {
        $signUpFromCsv = new UserSignUpFromCsv();
        $signUpFromCsv->setDefaultTeamId(rand(1, 100));
        $signUpFromCsv->setFirstName('FirstName');
        $signUpFromCsv->setLastName('LastName');
        $signUpFromCsv->setPassword('password');
        $signUpFromCsv->setUpdateEmailFlg(rand(0, 1));
        $signUpFromCsv->setTimezone(9.0);
        $signUpFromCsv->setLanguage('jpn');
        $signUpFromCsv->setAgreedTermsOfServiceId(rand(0, 2));
        $signUpFromCsv->setActiveFlg(!!rand(0, 1));
        $signUpFromCsv->setEmail('hoge@isao.co.jp');
        $signUpFromCsv->setEmailVerified(rand(0, 1));
        return $signUpFromCsv;
    }
}
