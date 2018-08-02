<?php
App::uses('GoalousTestCase', 'Test');
App::import('Lib/Upload', 'UploadedFile');

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
        $file = new UploadedFile($this->testEncodedFileData, $this->testFileName);

        $this->assertEquals($this->testFileName, $file->getFileName());
        $this->assertEquals($this->testEncodedFileData, $file->getEncodedFile());
        $this->assertEquals("image", $file->getFileType());
        $this->assertEquals("png", $file->getFileExt());
        $this->assertNotEmpty($file->getFileSize());
        $this->assertNotEmpty($file->getMetadata());
    }

    public function test_createEmptyFileContent_failure()
    {
        try {
            $file = new UploadedFile("", $this->testFileName);
            //If exception not thrown, fail the test
            $this->fail();
        } catch (InvalidArgumentException $e) {

        }
    }

    public function test_createEmptyFileName_failure()
    {
        try {
            $file = new UploadedFile($this->testEncodedFileData, "");
            //If exception not thrown, fail the test
            $this->fail();
        } catch (InvalidArgumentException $e) {

        }
    }
}