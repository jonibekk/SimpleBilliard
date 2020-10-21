<?php

App::uses('GoalousTestCase', 'Test');
App::import('Service', 'AuthService');
App::import('Service', 'TwoFAService');
App::uses('User', 'Model');
App::uses('Email', 'Model');
App::uses('SimplePasswordHasher', 'Controller/Component/Auth');
App::import('Vendor', 'paragmarx/google2fa/src/Google2FA');

use Goalous\Enum as Enum;
use Goalous\Exception as GlException;

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/05/31
 * Time: 10:25
 */
class AuthServiceTest extends GoalousTestCase
{

    private const TWOFA_SECRET = 'JUOQEYF6W7UWW3GX';

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.email',
        'app.circle_member',
        'app.device',
        'app.evaluation',
        'app.evaluation_setting',
        'app.notify_setting',
        'app.post',
        'app.saved_post',
        'app.team',
        'app.team_config',
        'app.team_member',
        'app.term',
        'app.user',
    ];

    public function test_authentication_success()
    {
        $this->assertNotEmpty($this->insertAndAuthenticateUser());
    }

    public function test_verifyWrongPassword_failed()
    {
        $this->insertNewUser();

        $userId = 101;
        $password = '123';

        /** @var AuthService $AuthService */
        $AuthService = ClassRegistry::init('AuthService');

        $this->assertFalse($AuthService->verifyPassword($userId, $password));
    }

    public function test_verifyEmptyPassword_failed()
    {
        $this->insertNewUser();

        $userId = 101;
        $password = '';

        /** @var AuthService $AuthService */
        $AuthService = ClassRegistry::init('AuthService');

        $this->assertFalse($AuthService->verifyPassword($userId, $password));
    }

    public function test_invalidate_success()
    {
        /** @var AuthService $AuthService */
        $AuthService = ClassRegistry::init('AuthService');

        $jwt = $this->insertAndAuthenticateUser()['data']['token'];

        try {
            $res = $AuthService->invalidateUser(JwtAuthentication::decode($jwt));
        } catch (Exception $e) {
            printf($e->getMessage());
            printf($e->getTraceAsString());
            $this->fail();
        }

        $this->assertTrue($res);
    }

    public function test_invalidateInvalidToken_failed()
    {
        /** @var AuthService $AuthService */
        $AuthService = ClassRegistry::init('AuthService');

        $this->insertAndAuthenticateUser();

        $failJwt = new JwtAuthentication(0, 0);
        $failJwt->withJwtId('failed');

        try {
            $AuthService->invalidateUser($failJwt);
        } catch (Exception $e) {
            printf($e->getMessage());
            printf($e->getTraceAsString());
            $this->assertNotEmpty($e);
        }
    }

    public function test_createLoginRequest_success()
    {
        $this->insertNewUser();

        $email = 'auth_test@email.com';

        /** @var AuthService $AuthService */
        $AuthService = ClassRegistry::init('AuthService');

        $requestData = $AuthService->createLoginRequest($email);

        $this->assertArrayHasKey('email', $requestData);
        $this->assertEquals($email, $requestData['email']);
        $this->assertArrayHasKey('default_team', $requestData);
        $this->assertArrayHasKey('auth_method', $requestData);
        $this->assertEquals(Enum\Auth\Method::PASSWORD, $requestData['auth_method']);
        $this->assertArrayHasKey('auth_content', $requestData);
    }

    /**
     * @expectedException \Goalous\Exception\Auth\AuthUserNotFoundException
     */
    public function test_createLoginRequestNoUser_failed()
    {
        /** @var AuthService $AuthService */
        $AuthService = ClassRegistry::init('AuthService');

        $AuthService->createLoginRequest("somerandomemail@123");

        $this->fail();
    }

    public function test_authenticateWithPassword_success()
    {
        $this->insertNewUser();

        $emailAddress = "auth_test@email.com";
        $password = '12345678';

        /** @var AuthService $AuthService */
        $AuthService = ClassRegistry::init('AuthService');

        $response = $AuthService->authenticateWithPassword($emailAddress, $password);

        $this->assertArrayHasKey('message', $response);
        $this->assertEquals(Enum\Auth\Status::OK, $response['message']);
        $this->assertArrayHasKey('data', $response);
        $this->assertArrayHasKey('me', $response['data']);
        $this->assertArrayHasKey('token', $response['data']);
        $this->assertNotNull($response['data']['me']['current_team_id']);
    }

    public function test_authenticateWithPasswordNoTeam_success()
    {
        $this->insertNewUser();

        $emailAddress = "auth_test@email.com";
        $password = '12345678';

        /** @var User $User */
        $User = ClassRegistry::init('User');
        $User->updateDefaultTeam(null, true, 101);

        /** @var AuthService $AuthService */
        $AuthService = ClassRegistry::init('AuthService');

        $response = $AuthService->authenticateWithPassword($emailAddress, $password);

        $this->assertArrayHasKey('message', $response);
        $this->assertEquals(Enum\Auth\Status::OK, $response['message']);
        $this->assertArrayHasKey('data', $response);
        $this->assertArrayHasKey('me', $response['data']);
        $this->assertArrayHasKey('token', $response['data']);
        $this->assertNull($response['data']['me']['current_team_id']);
    }

    public function test_authenticateWithPasswordWith2FA_success()
    {
        $this->insertNewUser(true);

        $emailAddress = "auth_test@email.com";
        $password = '12345678';

        /** @var AuthService $AuthService */
        $AuthService = ClassRegistry::init('AuthService');

        $response = $AuthService->authenticateWithPassword($emailAddress, $password);

        $this->assertArrayHasKey('message', $response);
        $this->assertEquals(Enum\Auth\Status::REQUEST_2FA, $response['message']);
        $this->assertArrayHasKey('data', $response);
        $this->assertArrayHasKey('auth_hash', $response['data']);
    }

    public function test_authenticateWith2FA_success()
    {
        $this->insertNewUser(true);

        $emailAddress = "auth_test@email.com";
        $password = '12345678';

        /** @var AuthService $AuthService */
        $AuthService = ClassRegistry::init('AuthService');

        $response = $AuthService->authenticateWithPassword($emailAddress, $password);

        $authHash = $response['data']['auth_hash'];

        $Google2Fa = new PragmaRX\Google2FA\Google2FA();
        $otpCode = $Google2Fa->getCurrentOtp(self::TWOFA_SECRET);

        $response = $AuthService->authenticateWith2FA($authHash, $otpCode);

        $this->assertArrayHasKey('message', $response);
        $this->assertEquals(Enum\Auth\Status::OK, $response['message']);
        $this->assertArrayHasKey('data', $response);
        $this->assertArrayHasKey('me', $response['data']);
        $this->assertArrayHasKey('token', $response['data']);
    }

    public function test_authenticateWith2FAHashDeleted_failed()
    {
        $this->insertNewUser(true);

        $emailAddress = "auth_test@email.com";
        $password = '12345678';

        /** @var AuthService $AuthService */
        $AuthService = ClassRegistry::init('AuthService');

        $response = $AuthService->authenticateWithPassword($emailAddress, $password);

        $authHash = $response['data']['auth_hash'];

        $Google2Fa = new PragmaRX\Google2FA\Google2FA();
        $otpCode = $Google2Fa->getCurrentOtp(self::TWOFA_SECRET);

        $AuthService->authenticateWith2FA($authHash, $otpCode);

        try {
            $AuthService->authenticateWith2FA($authHash, $otpCode);
        } catch (GlException\Auth\AuthUserNotFoundException $e) {
        }
    }

    /**
     * @expectedException \Goalous\Exception\Auth\AuthUserNotFoundException
     */
    public function test_authenticateWith2FANoHash_failed()
    {
        $this->insertNewUser(true);

        $emailAddress = "auth_test@email.com";
        $password = '12345678';

        /** @var AuthService $AuthService */
        $AuthService = ClassRegistry::init('AuthService');

        $Google2Fa = new PragmaRX\Google2FA\Google2FA();
        $otpCode = $Google2Fa->getCurrentOtp(self::TWOFA_SECRET);

        $AuthService->authenticateWith2FA('', $otpCode);
    }

    /**
     * @expectedException \Goalous\Exception\Auth\Auth2FAMismatchException
     */
    public function test_authenticateWith2FAWrongOTP_failed()
    {
        $this->insertNewUser(true);

        $emailAddress = "auth_test@email.com";
        $password = '12345678';

        /** @var AuthService $AuthService */
        $AuthService = ClassRegistry::init('AuthService');

        $response = $AuthService->authenticateWithPassword($emailAddress, $password);

        $authHash = $response['data']['auth_hash'];

        $AuthService->authenticateWith2FA($authHash, '123');
    }

    /**
     * @expectedException \Goalous\Exception\Auth\AuthUserNotFoundException
     */
    public function test_authenticateWithPasswordUserNotFound_failed()
    {
        $this->insertNewUser();

        /** @var AuthService $AuthService */
        $AuthService = ClassRegistry::init('AuthService');

        $emailAddress = "somerandomemail123@123123123";
        $password = '12345678';

        $AuthService->authenticateWithPassword($emailAddress, $password);

        $this->fail();
    }

    /**
     * @expectedException \Goalous\Exception\Auth\AuthUserNotFoundException
     */
    public function test_authenticateWithPasswordEmptyUsername_failed()
    {
        $this->insertNewUser();

        /** @var AuthService $AuthService */
        $AuthService = ClassRegistry::init('AuthService');

        $emailAddress = "";
        $password = '12345678';

        $AuthService->authenticateWithPassword($emailAddress, $password);

        $this->fail();
    }

    /**
     * @expectedException \Goalous\Exception\Auth\AuthMismatchException
     */
    public function test_authenticateWithPasswordNoPassword_failed()
    {
        $this->insertNewUser();

        /** @var AuthService $AuthService */
        $AuthService = ClassRegistry::init('AuthService');

        $emailAddress = "auth_test@email.com";
        $password = '';

        $AuthService->authenticateWithPassword($emailAddress, $password);

        $this->fail();
    }

    /**
     * @expectedException \Goalous\Exception\Auth\AuthMismatchException
     */
    public function test_authenticateWithPasswordWrongPassword_failed()
    {
        $this->insertNewUser();

        /** @var AuthService $AuthService */
        $AuthService = ClassRegistry::init('AuthService');

        $emailAddress = "auth_test@email.com";
        $password = 'wrongpassword';

        $AuthService->authenticateWithPassword($emailAddress, $password);

        $this->fail();
    }

    private function insertAndAuthenticateUser(bool $with2FA = false): array
    {
        $this->insertNewUser($with2FA);

        $emailAddress = "auth_test@email.com";
        $password = '12345678';

        /** @var AuthService $AuthService */
        $AuthService = ClassRegistry::init('AuthService');

        try {
            return $AuthService->authenticateWithPassword($emailAddress, $password);
        } catch (Exception $e) {
            printf($e->getMessage());
            printf($e->getTraceAsString());
            $this->fail();
        }

        return [];
    }

    private function insertNewUser(bool $with2FA = false)
    {
        $User = new User();
        $Email = new Email();

        $passwordHasher = new SimplePasswordHasher(['hashType' => 'sha1']);

        $newUser['User'] = [
            'id'                 => '101',
            'first_name'         => 'user_10',
            'last_name'          => 'user_10',
            'middle_name'        => 'Lorem ipsum dolor sit amet',
            'gender_type'        => 2,
            'birth_day'          => '2014-05-22',
            'hide_year_flg'      => 1,
            'hometown'           => 'Lorem ipsum dolor sit amet',
            'comment'            => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'password'           => $passwordHasher->hash('12345678'),
            'password_token'     => '',
            '2fa_secret'         => '',
            'password_modified'  => '2014-05-22 02:28:04',
            'no_pass_flg'        => 1,
            'photo_file_name'    => 'Lorem ipsum dolor sit amet',
            'primary_email_id'   => 50,
            'active_flg'         => 1,
            'last_login'         => '2014-05-22 02:28:04',
            'admin_flg'          => 1,
            'default_team_id'    => 1,
            'timezone'           => 10,
            'auto_timezone_flg'  => 1,
            'language'           => 'jpn',
            'auto_language_flg'  => 1,
            'romanize_flg'       => 1,
            'update_email_flg'   => 1,
            'setup_complete_flg' => 0,
            'del_flg'            => 0,
            'deleted'            => '',
            'created'            => '2014-05-22 02:28:04',
            'modified'           => '2014-05-22 02:28:04'
        ];

        if ($with2FA) {
            $newUser['User']['2fa_secret'] = self::TWOFA_SECRET;
        }

        $newEmail = [
            'id'                  => 50,
            'user_id'             => '101',
            'email'               => 'auth_test@email.com',
            'email_verified'      => 1,
            'email_token'         => 'Lorem ipsum dolor sit amet',
            'email_token_expires' => 1400725683,
            'del_flg'             => 0,
            'deleted'             => '',
            'created'             => 1400725683,
            'modified'            => 1400725683
        ];

        try {
            $User->save($newUser, ['validate' => false]);
            $Email->save($newEmail, ['validate' => false]);
        } catch (Exception $e) {
            $this->fail();
        }
    }
}
