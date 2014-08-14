<?php
App::uses('PostMention', 'Model');

/**
 * PostMention Test Case
 *
 * @property mixed PostMention
 */
class PostMentionTest extends CakeTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.post_mention',
        'app.post',
        'app.user', 'app.notify_setting',
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
        $this->PostMention = ClassRegistry::init('PostMention');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->PostMention);

        parent::tearDown();
    }

    //ダミーテスト
    function testDummy()
    {
    }

}
