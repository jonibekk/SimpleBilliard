<?php
App::uses('PostLike', 'Model');

/**
 * PostLike Test Case
 *
 * @property PostLike $PostLike
 */
class PostLikeTest extends CakeTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.post_like',
        'app.post',
        'app.user',
        'app.team'
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->PostLike = ClassRegistry::init('PostLike');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->PostLike);

        parent::tearDown();
    }

    function testChangeLike()
    {
        $uid = '1';
        $team_id = '1';
        $this->PostLike->me['id'] = $uid;
        $this->PostLike->current_team_id = $team_id;
        $this->PostLike->changeLike(null);
    }

}
