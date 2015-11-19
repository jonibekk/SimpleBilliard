<?php App::uses('GoalousTestCase', 'Test');
App::uses('View', 'View');
App::uses('Helper', 'View');
App::uses('UploadHelper', 'View/Helper');

/**
 * UploadHelper Test Case
 *
 * @property UploadHelper $Upload
 */
class UploadHelperTest extends GoalousTestCase
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
        $this->Upload = new UploadHelper($View);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Upload);

        parent::tearDown();
    }

    /**
     * testUploadImage method
     *
     * @return void
     */
    public function testUploadImage()
    {
        $this->markTestIncomplete('testUploadImage not implemented.');
    }

    /**
     * testUploadLink method
     *
     * @return void
     */
    public function testUploadLink()
    {
        $this->markTestIncomplete('testUploadLink not implemented.');
    }

    /**
     * testAttachedFileUrl method
     *
     * @return void
     */
    public function testAttachedFileUrl()
    {
        $this->markTestIncomplete('testAttachedFileUrl not implemented.');
    }

    /**
     * testGetAttachedFileName method
     *
     * @return void
     */
    public function testGetAttachedFileName()
    {
        $this->markTestIncomplete('testGetAttachedFileName not implemented.');
    }

    /**
     * testGetCssOfFileIcon method
     *
     * @return void
     */
    public function testGetCssOfFileIcon()
    {
        $this->markTestIncomplete('testGetCssOfFileIcon not implemented.');
    }

    /**
     * testIsCanPreview method
     *
     * @return void
     */
    public function testIsCanPreview()
    {
        $this->markTestIncomplete('testIsCanPreview not implemented.');
    }

    /**
     * testUploadUrl method
     *
     * @return void
     */
    public function testUploadUrl()
    {
        $this->markTestIncomplete('testUploadUrl not implemented.');
    }

    /**
     * testExtension method
     *
     * @return void
     */
    public function testExtension()
    {
        $this->markTestIncomplete('testExtension not implemented.');
    }

    /**
     * testSubstrS3Url method
     *
     * @return void
     */
    public function testSubstrS3Url()
    {
        $this->markTestIncomplete('testSubstrS3Url not implemented.');
    }

    /**
     * testGsGetStringToSign method
     *
     * @return void
     */
    public function testGsGetStringToSign()
    {
        $this->markTestIncomplete('testGsGetStringToSign not implemented.');
    }

    /**
     * testGsEncodeSignature method
     *
     * @return void
     */
    public function testGsEncodeSignature()
    {
        $this->markTestIncomplete('testGsEncodeSignature not implemented.');
    }

    /**
     * testGsPrepareS3URL method
     *
     * @return void
     */
    public function testGsPrepareS3URL()
    {
        $this->markTestIncomplete('testGsPrepareS3URL not implemented.');
    }

}
