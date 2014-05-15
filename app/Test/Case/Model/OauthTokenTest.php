<?php
App::uses('OauthToken', 'Model');

/**
 * OauthToken Test Case

 */
class OauthTokenTest extends CakeTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.oauth_token',
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
        $this->OauthToken = ClassRegistry::init('OauthToken');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->OauthToken);

        parent::tearDown();
    }

}
