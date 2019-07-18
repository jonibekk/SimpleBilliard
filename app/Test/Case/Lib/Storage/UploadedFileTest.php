<?php
App::uses('GoalousTestCase', 'Test');
App::import('Lib/Storage', 'UploadedFile');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/08/01
 * Time: 17:27
 */
class UploadedFileTest extends GoalousTestCase
{

    public function test_createUploadedFile_success()
    {
        $file = new UploadedFile($this->getTestFileData(), $this->getTestFileName());

        $this->assertEquals($this->getTestFileName(), $file->getFileName());
        $this->assertEquals($this->getTestFileData(), $file->getEncodedFile());
        $this->assertEquals("image", $file->getFileType());
        $this->assertEquals("png", $file->getFileExt());
        $this->assertNotEmpty($file->getFileSize());
        $this->assertNotEmpty($file->getMetadata());
        $this->assertNotEmpty($file->getUUID());
        $this->assertRegExp(UploadedFile::UUID_REGEXP, $file->getUUID());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function test_createEmptyFileContent_failure()
    {
        $file = new UploadedFile("", $this->getTestFileName());
        //If exception not thrown, fail the test
        $this->fail();
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function test_createEmptyFileName_failure()
    {
        $file = new UploadedFile($this->getTestFileData(), "");
        //If exception not thrown, fail the test
        $this->fail();
    }

    /**
     * @expectedException RuntimeException
     */
    public function test_randomStringData_failure()
    {
        $file = new UploadedFile("!)(&$^)(&@^)$^", $this->getTestFileName());
        //If exception not thrown, fail the test
        $this->fail();
    }

    public function test_decodingWithHeaderSuccess()
    {
        $file = new UploadedFile($this->getTestFileDataBase64WithHeader(), $this->getTestFileName());
        $this->assertInstanceOf(UploadedFile::class, $file);
    }
}