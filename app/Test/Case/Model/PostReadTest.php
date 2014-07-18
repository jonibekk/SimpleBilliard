<?php
App::uses('PostRead', 'Model');

/**
 * PostRead Test Case
 *
 * @property  PostRead $PostRead
 */
class PostReadTest extends CakeTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.post_read',
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
        $this->PostRead = ClassRegistry::init('PostRead');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->PostRead);

        parent::tearDown();
    }

    public function testRed()
    {
        $uid = '1';
        $team_id = '1';
        $this->PostRead->me['id'] = $uid;
        $this->PostRead->current_team_id = $team_id;
        $test_save_data = [
            'Post' => [
                'user_id' => $uid,
                'team_id' => $team_id,
                'body'    => 'test',
            ],
        ];
        $this->PostRead->Post->save($test_save_data);
        $this->PostRead->red($this->PostRead->Post->getLastInsertID());
        $post_read = $this->PostRead->read();
        $this->assertEquals($uid, $post_read['PostRead']['user_id']);
    }

}
