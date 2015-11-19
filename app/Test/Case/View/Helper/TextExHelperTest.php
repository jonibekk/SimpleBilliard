<?php App::uses('GoalousTestCase', 'Test');
App::uses('View', 'View');
App::uses('Helper', 'View');
App::uses('TextExHelper', 'View/Helper');

/**
 * TextExHelper Test Case
 *
 * @property TextExHelper $TextEx
 */
class TextExHelperTest extends GoalousTestCase
{

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $View = new View();
        $this->TextEx = new TextExHelper($View);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->TextEx);

        parent::tearDown();
    }

    /**
     * testAutoLink method
     *
     * @return void
     */
    public function testAutoLink()
    {
        $this->markTestIncomplete('testAutoLink not implemented.');
    }

    /**
     * testReplaceUrl method
     *
     * @return void
     */
    public function testReplaceUrl()
    {
        $this->markTestIncomplete('testReplaceUrl not implemented.');
    }

    /**
     * testAutoLinkUrlsEx method
     *
     * @return void
     */
    public function testAutoLinkUrlsEx()
    {
        $this->markTestIncomplete('testAutoLinkUrlsEx not implemented.');
    }

}
