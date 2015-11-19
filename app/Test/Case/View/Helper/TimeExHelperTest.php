<?php App::uses('GoalousTestCase', 'Test');
App::uses('View', 'View');
App::uses('Helper', 'View');
App::uses('TimeExHelper', 'View/Helper');

/**
 * TimeExHelper Test Case
 *
 * @property TimeExHelper $TimeEx
 */
class TimeExHelperTest extends GoalousTestCase
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
        $this->TimeEx = new TimeExHelper($View);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->TimeEx);

        parent::tearDown();
    }

    /**
     * testDate method
     *
     * @return void
     */
    public function testDate()
    {
        $this->markTestIncomplete('testDate not implemented.');
    }

    /**
     * testDateNoYear method
     *
     * @return void
     */
    public function testDateNoYear()
    {
        $this->markTestIncomplete('testDateNoYear not implemented.');
    }

    /**
     * testDatetimeNoYear method
     *
     * @return void
     */
    public function testDatetimeNoYear()
    {
        $this->markTestIncomplete('testDatetimeNoYear not implemented.');
    }

    /**
     * testFullDatetime method
     *
     * @return void
     */
    public function testFullDatetime()
    {
        $this->markTestIncomplete('testFullDatetime not implemented.');
    }

    /**
     * testElapsedTime method
     *
     * @return void
     */
    public function testElapsedTime()
    {
        $this->markTestIncomplete('testElapsedTime not implemented.');
    }

    /**
     * testElapsedMinutes method
     *
     * @return void
     */
    public function testElapsedMinutes()
    {
        $this->markTestIncomplete('testElapsedMinutes not implemented.');
    }

    /**
     * testElapsedHours method
     *
     * @return void
     */
    public function testElapsedHours()
    {
        $this->markTestIncomplete('testElapsedHours not implemented.');
    }

    /**
     * testDatetimeLocalFormat method
     *
     * @return void
     */
    public function testDatetimeLocalFormat()
    {
        $this->markTestIncomplete('testDatetimeLocalFormat not implemented.');
    }

    /**
     * testDateLocalFormat method
     *
     * @return void
     */
    public function testDateLocalFormat()
    {
        $this->markTestIncomplete('testDateLocalFormat not implemented.');
    }

    /**
     * testYearDayLocalFormat method
     *
     * @return void
     */
    public function testYearDayLocalFormat()
    {
        $this->markTestIncomplete('testYearDayLocalFormat not implemented.');
    }

    /**
     * testFullTimeLocalFormat method
     *
     * @return void
     */
    public function testFullTimeLocalFormat()
    {
        $this->markTestIncomplete('testFullTimeLocalFormat not implemented.');
    }

}
