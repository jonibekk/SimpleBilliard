<?php
App::uses('Team', 'Model');

/**
 * Team Test Case

 */
class TeamTest extends CakeTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.team',
        'app.image',
        'app.user',
        'app.badge',
        'app.default_badge',
        'app.post',
        'app.goal',
        'app.comment_mention',
        'app.comment',
        'app.comment_like',
        'app.comment_read',
        'app.given_badge',
        'app.grant_user',
        'app.post_like',
        'app.post_mention',
        'app.post_read',
        'app.posts_image',
        'app.group',
        'app.team_member',
        'app.coach_user',
        'app.job_category',
        'app.invite',
        'app.from_user',
        'app.to_user',
        'app.notification',
        'app.thread'
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->Team = ClassRegistry::init('Team');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Team);

        parent::tearDown();
    }

}
