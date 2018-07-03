<?php
App::uses('GoalousTestCase', 'Test');
App::import('Service', 'AuthService');
App::uses('User', 'Model');
App::uses('Email', 'Model');
App::uses('SimplePasswordHasher', 'Controller/Component/Auth');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/05/31
 * Time: 10:25
 */
class AuthServiceTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.email',
        'app.team',
        'app.user'
    ];

    public function test_authentication_success()
    {
        $jwt = $this->insertAndAuthenticateUser();

        $this->assertEquals(1, $jwt->getTeamId());
    }

    /**
     * @expectedException \Goalous\Exception\Auth\AuthMismatchException
     */
    public function test_authWrongPassword_failed()
    {
        $this->insertNewUser();

        $emailAddress = 'auth_test@email.com';
        $password = '123';

        /** @var AuthService $AuthService */
        $AuthService = ClassRegistry::init('AuthService');

        $AuthService->authenticateUser($emailAddress, $password);
    }


    /**
     * @expectedException \Goalous\Exception\Auth\AuthMismatchException
     */
    public function test_authWrongUsername_failed()
    {
        $this->insertNewUser();

        $emailAddress = 'auth_missing@email.com';
        $password = '12345678';

        /** @var AuthService $AuthService */
        $AuthService = ClassRegistry::init('AuthService');

        $AuthService->authenticateUser($emailAddress, $password);
    }

    /**
     * @expectedException \Goalous\Exception\Auth\AuthMismatchException
     */
    public function test_authEmptyUsername_failed()
    {
        $this->insertNewUser();

        $emailAddress = '';
        $password = '12345678';

        /** @var AuthService $AuthService */
        $AuthService = ClassRegistry::init('AuthService');

        $AuthService->authenticateUser($emailAddress, $password);
    }


    /**
     * @expectedException \Goalous\Exception\Auth\AuthMismatchException
     */
    public function test_authEmptyPassword_failed()
    {
        $this->insertNewUser();

        $emailAddress = 'auth_testt@email.com';
        $password = '';

        /** @var AuthService $AuthService */
        $AuthService = ClassRegistry::init('AuthService');

        $AuthService->authenticateUser($emailAddress, $password);
    }

    public function test_invalidate_success()
    {
        /** @var AuthService $AuthService */
        $AuthService = ClassRegistry::init('AuthService');

        $jwt = $this->insertAndAuthenticateUser();

        try {

            $res = $AuthService->invalidateUser($jwt);

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

        $failJwt = new JwtAuthentication(0,0);
        $failJwt->withJwtId('failed');

        try {
            $AuthService->invalidateUser($failJwt);
        } catch (Exception $e) {
            printf($e->getMessage());
            printf($e->getTraceAsString());
            $this->assertNotEmpty($e);
        }
    }

    private function insertAndAuthenticateUser()
    {

        $this->insertNewUser();

        $emailAddress = "auth_test@email.com";
        $password = '12345678';

        /** @var AuthService $AuthService */
        $AuthService = ClassRegistry::init('AuthService');

        try {
            $jwt = $AuthService->authenticateUser($emailAddress, $password);
        } catch (Exception $e) {
            printf($e->getMessage());
            printf($e->getTraceAsString());
            $this->fail();
        }

        if (empty($jwt)) {
            $this->fail();
        }

        return $jwt;
    }

    private function insertNewUser()
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