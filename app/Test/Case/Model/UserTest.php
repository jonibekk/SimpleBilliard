<?php
App::uses('User', 'Model');

/**
 * User Test Case
 *
 */
class UserTest extends CakeTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.user'
    );

    /**
     * @var User User
     */
    public $User;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->User = ClassRegistry::init('User');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->User);

        parent::tearDown();
    }

    /**
     * validationのテスト
     */
    public function testUserValidations()
    {
        $baseCase = [
            'password'   => 'hogehoge',
            'email'      => 'test@test.com',
            'first_name' => 'hoge',
            'last_name'  => 'fuga'
        ];

        $testCases = [
            true  => [
                ['password' => 'aaa'],
                ['email' => 'test@aaa.com']
            ],
            false => [
                ['password' => ''],
                ['email' => ''],
                ['first_name' => ''],
                ['last_name' => ''],
            ]
        ];

        foreach ($testCases as $expected => $cases) {
            foreach ($cases as $case) {
                $testCase = array_merge($baseCase, $case);
                $this->User->set($testCase);
                $result = $this->User->validates();
                if ((boolean)$expected) {
                    $this->assertTrue($result);
                }
                else {
                    $this->assertFalse($result);
                }
            }
        }
    }

}
