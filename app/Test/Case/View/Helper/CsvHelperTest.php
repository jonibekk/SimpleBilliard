<?php App::uses('GoalousTestCase', 'Test');
App::uses('View', 'View');
App::uses('Helper', 'View');
App::uses('CsvHelper', 'View/Helper');

/**
 * CsvHelper Test Case
 */
class CsvHelperTest extends GoalousTestCase
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
        $this->Csv = new CsvHelper($View);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Csv);

        parent::tearDown();
    }

    /**
     * testClear method
     *
     * @return void
     */
    public function testClear()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    /**
     * testAddField method
     *
     * @return void
     */
    public function testAddField()
    {
        $this->markTestIncomplete('testAddField not implemented.');
    }

    /**
     * testEndRow method
     *
     * @return void
     */
    public function testEndRow()
    {
        $this->markTestIncomplete('testEndRow not implemented.');
    }

    /**
     * testAddRow method
     *
     * @return void
     */
    public function testAddRow()
    {
        $this->markTestIncomplete('testAddRow not implemented.');
    }

    /**
     * testEncfputscv method
     *
     * @return void
     */
    public function testEncfputscv()
    {
        $this->markTestIncomplete('testEncfputscv not implemented.');
    }

    /**
     * testRenderHeaders method
     *
     * @return void
     */
    public function testRenderHeaders()
    {
        $this->markTestIncomplete('testRenderHeaders not implemented.');
    }

    /**
     * testSetFilename method
     *
     * @return void
     */
    public function testSetFilename()
    {
        $this->markTestIncomplete('testSetFilename not implemented.');
    }

    /**
     * testRender method
     *
     * @return void
     */
    public function testRender()
    {
        $this->markTestIncomplete('testRender not implemented.');
    }

    /**
     * testDefaultRender method
     *
     * @return void
     */
    public function testDefaultRender()
    {
        $this->markTestIncomplete('testDefaultRender not implemented.');
    }

}
