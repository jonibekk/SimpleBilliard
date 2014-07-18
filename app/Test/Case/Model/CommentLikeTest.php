<?php
App::uses('CommentLike', 'Model');

/**
 * CommentLike Test Case
 *
 * @property  CommentLike $CommentLike
 */
class CommentLikeTest extends CakeTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.comment_like',
        'app.comment',
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
        $this->CommentLike = ClassRegistry::init('CommentLike');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->CommentLike);

        parent::tearDown();
    }

    function testChangeLike()
    {
        $uid = '1';
        $team_id = '1';
        $this->CommentLike->me['id'] = $uid;
        $this->CommentLike->current_team_id = $team_id;
        $this->CommentLike->changeLike(null);
    }

}
