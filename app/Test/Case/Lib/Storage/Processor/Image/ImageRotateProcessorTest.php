<?php
App::uses('GoalousTestCase', 'Test');
App::import('Lib/Storage', 'UploadedFile');
App::import('Lib/Storage/Processor/Image', 'ImageRotateProcessor');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/08/21
 * Time: 12:03
 */
class ImageRotateProcessorTest extends GoalousTestCase
{
    public function test_getRotation1_success()
    {
        $fileName = "Landscape_1.jpg";
        $file = new UploadedFile($this->getTestFileData($fileName), $fileName);
        $ImageRotateProcessor = new ImageRotateProcessor();
        list($res, $flip) = $ImageRotateProcessor->getRotation($file);
        $this->assertEquals($res, 0);
        $this->assertFalse($flip);

        $fileName = "Portrait_1.jpg";
        $file = new UploadedFile($this->getTestFileData($fileName), $fileName);
        $ImageRotateProcessor = new ImageRotateProcessor();
        list($res, $flip) = $ImageRotateProcessor->getRotation($file);
        $this->assertEquals($res, 0);
        $this->assertFalse($flip);
    }

    public function test_getRotation2_success()
    {
        $fileName = "Landscape_2.jpg";
        $file = new UploadedFile($this->getTestFileData($fileName), $fileName);
        $ImageRotateProcessor = new ImageRotateProcessor();
        list($res, $flip) = $ImageRotateProcessor->getRotation($file);
        $this->assertEquals($res, 0);
        $this->assertTrue($flip);

        $fileName = "Portrait_2.jpg";
        $file = new UploadedFile($this->getTestFileData($fileName), $fileName);
        $ImageRotateProcessor = new ImageRotateProcessor();
        list($res, $flip) = $ImageRotateProcessor->getRotation($file);
        $this->assertEquals($res, 0);
        $this->assertTrue($flip);
    }

    public function test_getRotation3_success()
    {

        $fileName = "Landscape_3.jpg";
        $file = new UploadedFile($this->getTestFileData($fileName), $fileName);
        $ImageRotateProcessor = new ImageRotateProcessor();
        list($res, $flip) = $ImageRotateProcessor->getRotation($file);
        $this->assertEquals($res, 180);
        $this->assertFalse($flip);

        $fileName = "Portrait_3.jpg";
        $file = new UploadedFile($this->getTestFileData($fileName), $fileName);
        $ImageRotateProcessor = new ImageRotateProcessor();
        list($res, $flip) = $ImageRotateProcessor->getRotation($file);
        $this->assertEquals($res, 180);
        $this->assertFalse($flip);
    }

    public function test_getRotation4_success()
    {
        $fileName = "Landscape_4.jpg";
        $file = new UploadedFile($this->getTestFileData($fileName), $fileName);
        $ImageRotateProcessor = new ImageRotateProcessor();
        list($res, $flip) = $ImageRotateProcessor->getRotation($file);
        $this->assertEquals($res, 180);
        $this->assertTrue($flip);

        $fileName = "Portrait_4.jpg";
        $file = new UploadedFile($this->getTestFileData($fileName), $fileName);
        $ImageRotateProcessor = new ImageRotateProcessor();
        list($res, $flip) = $ImageRotateProcessor->getRotation($file);
        $this->assertEquals($res, 180);
        $this->assertTrue($flip);
    }

    public function test_getRotation5_success()
    {
        $fileName = "Landscape_5.jpg";
        $file = new UploadedFile($this->getTestFileData($fileName), $fileName);
        $ImageRotateProcessor = new ImageRotateProcessor();
        list($res, $flip) = $ImageRotateProcessor->getRotation($file);
        $this->assertEquals($res, 270);
        $this->assertTrue($flip);

        $fileName = "Portrait_5.jpg";
        $file = new UploadedFile($this->getTestFileData($fileName), $fileName);
        $ImageRotateProcessor = new ImageRotateProcessor();
        list($res, $flip) = $ImageRotateProcessor->getRotation($file);
        $this->assertEquals($res, 270);
        $this->assertTrue($flip);
    }

    public function test_getRotation6_success()
    {
        $fileName = "Landscape_6.jpg";
        $file = new UploadedFile($this->getTestFileData($fileName), $fileName);
        $ImageRotateProcessor = new ImageRotateProcessor();
        list($res, $flip) = $ImageRotateProcessor->getRotation($file);
        $this->assertEquals($res, 270);
        $this->assertFalse($flip);

        $fileName = "Portrait_6.jpg";
        $file = new UploadedFile($this->getTestFileData($fileName), $fileName);
        $ImageRotateProcessor = new ImageRotateProcessor();
        list($res, $flip) = $ImageRotateProcessor->getRotation($file);
        $this->assertEquals($res, 270);
        $this->assertFalse($flip);
    }

    public function test_getRotation7_success()
    {
        $fileName = "Landscape_7.jpg";
        $file = new UploadedFile($this->getTestFileData($fileName), $fileName);
        $ImageRotateProcessor = new ImageRotateProcessor();
        list($res, $flip) = $ImageRotateProcessor->getRotation($file);
        $this->assertEquals($res, 90);
        $this->assertTrue($flip);

        $fileName = "Portrait_7.jpg";
        $file = new UploadedFile($this->getTestFileData($fileName), $fileName);
        $ImageRotateProcessor = new ImageRotateProcessor();
        list($res, $flip) = $ImageRotateProcessor->getRotation($file);
        $this->assertEquals($res, 90);
        $this->assertTrue($flip);
    }

    public function test_getRotation8_success()
    {
        $fileName = "Landscape_8.jpg";
        $file = new UploadedFile($this->getTestFileData($fileName), $fileName);
        $ImageRotateProcessor = new ImageRotateProcessor();
        list($res, $flip) = $ImageRotateProcessor->getRotation($file);
        $this->assertEquals($res, 90);
        $this->assertFalse($flip);

        $fileName = "Portrait_8.jpg";
        $file = new UploadedFile($this->getTestFileData($fileName), $fileName);
        $ImageRotateProcessor = new ImageRotateProcessor();
        list($res, $flip) = $ImageRotateProcessor->getRotation($file);
        $this->assertEquals($res, 90);
        $this->assertFalse($flip);
    }

    public function test_rotate1_success()
    {
        $fileName = "Landscape_1.jpg";
        $sourceFile = new UploadedFile($this->getTestFileData($fileName), $fileName);
        $ImageRotateProcessor = new ImageRotateProcessor();
        $file = $ImageRotateProcessor->process($sourceFile);
        list($res, $flip) = $ImageRotateProcessor->getRotation($file);
        $this->assertEquals($res, 0);
        $this->assertFalse($flip);

        $this->assertEquals($sourceFile->getFileName(), $file->getFileName());
        $this->assertEquals($sourceFile->getMIME(), $file->getMIME());

        $fileName = "Portrait_1.jpg";
        $sourceFile = new UploadedFile($this->getTestFileData($fileName), $fileName);
        $ImageRotateProcessor = new ImageRotateProcessor();
        $file = $ImageRotateProcessor->process($sourceFile);
        list($res, $flip) = $ImageRotateProcessor->getRotation($file);
        $this->assertEquals($res, 0);
        $this->assertFalse($flip);

        $this->assertEquals($sourceFile->getFileName(), $file->getFileName());
        $this->assertEquals($sourceFile->getMIME(), $file->getMIME());
    }

    public function test_rotate2_success()
    {
        $fileName = "Landscape_2.jpg";
        $sourceFile = new UploadedFile($this->getTestFileData($fileName), $fileName);
        $ImageRotateProcessor = new ImageRotateProcessor();
        $file = $ImageRotateProcessor->process($sourceFile);
        list($res, $flip) = $ImageRotateProcessor->getRotation($file);
        $this->assertEquals($res, 0);
        $this->assertFalse($flip);

        $this->assertEquals($sourceFile->getFileName(), $file->getFileName());
        $this->assertEquals($sourceFile->getMIME(), $file->getMIME());

        $fileName = "Portrait_2.jpg";
        $sourceFile = new UploadedFile($this->getTestFileData($fileName), $fileName);
        $ImageRotateProcessor = new ImageRotateProcessor();
        $file = $ImageRotateProcessor->process($sourceFile);
        list($res, $flip) = $ImageRotateProcessor->getRotation($file);
        $this->assertEquals($res, 0);
        $this->assertFalse($flip);

        $this->assertEquals($sourceFile->getFileName(), $file->getFileName());
        $this->assertEquals($sourceFile->getMIME(), $file->getMIME());
    }

    public function test_rotate3_success()
    {
        $fileName = "Landscape_3.jpg";
        $sourceFile = new UploadedFile($this->getTestFileData($fileName), $fileName);
        $ImageRotateProcessor = new ImageRotateProcessor();
        $file = $ImageRotateProcessor->process($sourceFile);
        list($res, $flip) = $ImageRotateProcessor->getRotation($file);
        $this->assertEquals($res, 0);
        $this->assertFalse($flip);

        $this->assertEquals($sourceFile->getFileName(), $file->getFileName());
        $this->assertEquals($sourceFile->getMIME(), $file->getMIME());

        $fileName = "Portrait_3.jpg";
        $sourceFile = new UploadedFile($this->getTestFileData($fileName), $fileName);
        $ImageRotateProcessor = new ImageRotateProcessor();
        $file = $ImageRotateProcessor->process($sourceFile);
        list($res, $flip) = $ImageRotateProcessor->getRotation($file);
        $this->assertEquals($res, 0);
        $this->assertFalse($flip);

        $this->assertEquals($sourceFile->getFileName(), $file->getFileName());
        $this->assertEquals($sourceFile->getMIME(), $file->getMIME());
    }

    public function test_rotate4_success()
    {
        $fileName = "Landscape_4.jpg";
        $sourceFile = new UploadedFile($this->getTestFileData($fileName), $fileName);
        $ImageRotateProcessor = new ImageRotateProcessor();
        $file = $ImageRotateProcessor->process($sourceFile);
        list($res, $flip) = $ImageRotateProcessor->getRotation($file);
        $this->assertEquals($res, 0);
        $this->assertFalse($flip);

        $this->assertEquals($sourceFile->getFileName(), $file->getFileName());
        $this->assertEquals($sourceFile->getMIME(), $file->getMIME());

        $fileName = "Portrait_4.jpg";
        $sourceFile = new UploadedFile($this->getTestFileData($fileName), $fileName);
        $ImageRotateProcessor = new ImageRotateProcessor();
        $file = $ImageRotateProcessor->process($sourceFile);
        list($res, $flip) = $ImageRotateProcessor->getRotation($file);
        $this->assertEquals($res, 0);
        $this->assertFalse($flip);

        $this->assertEquals($sourceFile->getFileName(), $file->getFileName());
        $this->assertEquals($sourceFile->getMIME(), $file->getMIME());
    }

    public function test_rotate5_success()
    {
        $fileName = "Landscape_5.jpg";
        $sourceFile = new UploadedFile($this->getTestFileData($fileName), $fileName);
        $ImageRotateProcessor = new ImageRotateProcessor();
        $file = $ImageRotateProcessor->process($sourceFile);
        list($res, $flip) = $ImageRotateProcessor->getRotation($file);
        $this->assertEquals($res, 0);
        $this->assertFalse($flip);

        $this->assertEquals($sourceFile->getFileName(), $file->getFileName());
        $this->assertEquals($sourceFile->getMIME(), $file->getMIME());

        $fileName = "Portrait_5.jpg";
        $sourceFile = new UploadedFile($this->getTestFileData($fileName), $fileName);
        $ImageRotateProcessor = new ImageRotateProcessor();
        $file = $ImageRotateProcessor->process($sourceFile);
        list($res, $flip) = $ImageRotateProcessor->getRotation($file);
        $this->assertEquals($res, 0);
        $this->assertFalse($flip);

        $this->assertEquals($sourceFile->getFileName(), $file->getFileName());
        $this->assertEquals($sourceFile->getMIME(), $file->getMIME());
    }

    public function test_rotate6_success()
    {
        $fileName = "Landscape_6.jpg";
        $sourceFile = new UploadedFile($this->getTestFileData($fileName), $fileName);
        $ImageRotateProcessor = new ImageRotateProcessor();
        $file = $ImageRotateProcessor->process($sourceFile);
        list($res, $flip) = $ImageRotateProcessor->getRotation($file);
        $this->assertEquals($res, 0);
        $this->assertFalse($flip);

        $this->assertEquals($sourceFile->getFileName(), $file->getFileName());
        $this->assertEquals($sourceFile->getMIME(), $file->getMIME());

        $fileName = "Portrait_6.jpg";
        $sourceFile = new UploadedFile($this->getTestFileData($fileName), $fileName);
        $ImageRotateProcessor = new ImageRotateProcessor();
        $file = $ImageRotateProcessor->process($sourceFile);
        list($res, $flip) = $ImageRotateProcessor->getRotation($file);
        $this->assertEquals($res, 0);
        $this->assertFalse($flip);

        $this->assertEquals($sourceFile->getFileName(), $file->getFileName());
        $this->assertEquals($sourceFile->getMIME(), $file->getMIME());
    }

    public function test_rotate7_success()
    {
        $fileName = "Landscape_7.jpg";
        $sourceFile = new UploadedFile($this->getTestFileData($fileName), $fileName);
        $ImageRotateProcessor = new ImageRotateProcessor();
        $file = $ImageRotateProcessor->process($sourceFile);
        list($res, $flip) = $ImageRotateProcessor->getRotation($file);
        $this->assertEquals($res, 0);
        $this->assertFalse($flip);

        $this->assertEquals($sourceFile->getFileName(), $file->getFileName());
        $this->assertEquals($sourceFile->getMIME(), $file->getMIME());

        $fileName = "Portrait_7.jpg";
        $sourceFile = new UploadedFile($this->getTestFileData($fileName), $fileName);
        $ImageRotateProcessor = new ImageRotateProcessor();
        $file = $ImageRotateProcessor->process($sourceFile);
        list($res, $flip) = $ImageRotateProcessor->getRotation($file);
        $this->assertEquals($res, 0);
        $this->assertFalse($flip);

        $this->assertEquals($sourceFile->getFileName(), $file->getFileName());
        $this->assertEquals($sourceFile->getMIME(), $file->getMIME());
    }

    public function test_rotate8_success()
    {
        $fileName = "Landscape_8.jpg";
        $sourceFile = new UploadedFile($this->getTestFileData($fileName), $fileName);
        $ImageRotateProcessor = new ImageRotateProcessor();
        $file = $ImageRotateProcessor->process($sourceFile);
        list($res, $flip) = $ImageRotateProcessor->getRotation($file);
        $this->assertEquals($res, 0);
        $this->assertFalse($flip);

        $this->assertEquals($sourceFile->getFileName(), $file->getFileName());
        $this->assertEquals($sourceFile->getMIME(), $file->getMIME());

        $fileName = "Portrait_8.jpg";
        $sourceFile = new UploadedFile($this->getTestFileData($fileName), $fileName);
        $ImageRotateProcessor = new ImageRotateProcessor();
        $file = $ImageRotateProcessor->process($sourceFile);
        list($res, $flip) = $ImageRotateProcessor->getRotation($file);
        $this->assertEquals($res, 0);
        $this->assertFalse($flip);

        $this->assertEquals($sourceFile->getFileName(), $file->getFileName());
        $this->assertEquals($sourceFile->getMIME(), $file->getMIME());
    }
}