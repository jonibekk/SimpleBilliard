<?php
App::uses('Email', 'Model');

/**
 * Email Test Case
 *
 * @property mixed Email
 */
class EmailTest extends CakeTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.email',
        'app.user'
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->Email = ClassRegistry::init('Email');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Email);
        parent::tearDown();
    }

    public $baseData = [];

    function getValidationRes($data = [])
    {
        if (empty($data)) {
            return null;
        }
        $testData = array_merge($this->baseData, $data);
        $this->Email->create();
        $this->Email->set($testData);
        return $this->Email->validates();
    }

    /**
     * Emailモデルのバリデーションチェックのテスト
     */
    public function testEmailValidations()
    {
        $this->assertFalse(
             $this->getValidationRes(['email' => '']),
             "[異常系]メールアドレスは空を受け付けない"
        );
        $this->assertTrue(
             $this->getValidationRes(['email' => 'xxx@aaa.com']),
             "[正常系]メールアドレスとして正しいか"
        );
        $this->assertFalse(
             $this->getValidationRes(['email' => 'xxxaaa.com']),
             "[異常系]メールアドレスとして正しいか"
        );
        $this->assertFalse(
             $this->getValidationRes(['email' => 'xxxaaa.comaaaa']),
             "[異常系]メールアドレスとして正しいか"
        );
        $this->assertFalse(
             $this->getValidationRes(['email' => 'xxxaaacomaaaa']),
             "[異常系]メールアドレスとして正しいか"
        );
        $this->assertFalse(
             $this->getValidationRes(['email' => 'xxx@aaa.com,xxx@aaa.com']),
             "[異常系]メールアドレスとして正しいか"
        );
    }

}
