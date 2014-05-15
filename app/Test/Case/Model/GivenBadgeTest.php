<?php
App::uses('GivenBadge', 'Model');

/**
 * GivenBadge Test Case

 */
class GivenBadgeTest extends CakeTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.given_badge',
        'app.user',
        'app.grant_user',
        'app.team',
        'app.post'
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->GivenBadge = ClassRegistry::init('GivenBadge');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->GivenBadge);

        parent::tearDown();
    }

}
