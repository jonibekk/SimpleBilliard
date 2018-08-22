<?php
App::uses('GoalousTestCase', 'Test');
App::import('Lib/Storage', 'UploadedFile');
App::import('Lib/Storage/Processor/Image', 'ImageResizeProcessor');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/08/21
 * Time: 16:10
 */
class ImageResizeProcessorTest extends GoalousTestCase
{
    /**
     * Get 1200 x 1800 image file
     *
     * @return UploadedFile uploaded file
     */
    private function getFile(): UploadedFile
    {
        return new UploadedFile($this->getTestFileData("Portrait_1.jpg"), "Portrait_1.jpg");
    }

    public function test_resizeBand_success()
    {
        $ImageResizeProcessor = new ImageResizeProcessor();
        $sourceFile = $this->getFile();
        $resizedFile = $ImageResizeProcessor->process($sourceFile, "[1000x1000]");

        list($xLength, $yLength) = getimagesizefromstring($resizedFile->getBinaryFile());
        $this->assertEquals(1000, $xLength);
        $this->assertEquals(1000, $yLength);

        $this->assertEquals($sourceFile->getFileName(), $resizedFile->getFileName());
        $this->assertEquals($sourceFile->getMIME(), $resizedFile->getMIME());
    }

    public function test_resizeBest_success()
    {
        $ImageResizeProcessor = new ImageResizeProcessor();
        $sourceFile = $this->getFile();
        $resizedFile = $ImageResizeProcessor->process($sourceFile, "1000x1000");

        list($xLength, $yLength) = getimagesizefromstring($resizedFile->getBinaryFile());
        $this->assertEquals(1000, $xLength);
        $this->assertEquals(1000, $yLength);

        $this->assertEquals($sourceFile->getFileName(), $resizedFile->getFileName());
        $this->assertEquals($sourceFile->getMIME(), $resizedFile->getMIME());
    }

    public function test_resizeForce_success()
    {
        $ImageResizeProcessor = new ImageResizeProcessor();
        $sourceFile = $this->getFile();
        $resizedFile = $ImageResizeProcessor->process($sourceFile, "f[1000x1000]");

        list($xLength, $yLength) = getimagesizefromstring($resizedFile->getBinaryFile());
        $this->assertEquals(1000, $xLength);
        $this->assertEquals(1000, $yLength);

        $this->assertEquals($sourceFile->getFileName(), $resizedFile->getFileName());
        $this->assertEquals($sourceFile->getMIME(), $resizedFile->getMIME());
    }

    public function test_resizeH_success()
    {
        $ImageResizeProcessor = new ImageResizeProcessor();
        $sourceFile = $this->getFile();
        $resizedFile = $ImageResizeProcessor->process($sourceFile, "1440h");

        list($xLength, $yLength) = getimagesizefromstring($resizedFile->getBinaryFile());
        $this->assertEquals(960, $xLength);
        $this->assertEquals(1440, $yLength);

        $this->assertEquals($sourceFile->getFileName(), $resizedFile->getFileName());
        $this->assertEquals($sourceFile->getMIME(), $resizedFile->getMIME());

        $resizedFile = $ImageResizeProcessor->process($sourceFile, "1440H");

        list($xLength, $yLength) = getimagesizefromstring($resizedFile->getBinaryFile());
        $this->assertEquals(960, $xLength);
        $this->assertEquals(1440, $yLength);

        $this->assertEquals($sourceFile->getFileName(), $resizedFile->getFileName());
        $this->assertEquals($sourceFile->getMIME(), $resizedFile->getMIME());

    }

    public function test_resizeW_success()
    {
        $ImageResizeProcessor = new ImageResizeProcessor();
        $sourceFile = $this->getFile();
        $resizedFile = $ImageResizeProcessor->process($sourceFile, "960w");

        list($xLength, $yLength) = getimagesizefromstring($resizedFile->getBinaryFile());
        $this->assertEquals(960, $xLength);
        $this->assertEquals(1440, $yLength);

        $this->assertEquals($sourceFile->getFileName(), $resizedFile->getFileName());
        $this->assertEquals($sourceFile->getMIME(), $resizedFile->getMIME());

        $resizedFile = $ImageResizeProcessor->process($sourceFile, "960W");

        list($xLength, $yLength) = getimagesizefromstring($resizedFile->getBinaryFile());
        $this->assertEquals(960, $xLength);
        $this->assertEquals(1440, $yLength);

        $this->assertEquals($sourceFile->getFileName(), $resizedFile->getFileName());
        $this->assertEquals($sourceFile->getMIME(), $resizedFile->getMIME());
    }

    public function test_resizeL_success()
    {
        $ImageResizeProcessor = new ImageResizeProcessor();
        $sourceFile = $this->getFile();
        $resizedFile = $ImageResizeProcessor->process($sourceFile, "1440l");

        list($xLength, $yLength) = getimagesizefromstring($resizedFile->getBinaryFile());
        $this->assertEquals(960, $xLength);
        $this->assertEquals(1440, $yLength);

        $this->assertEquals($sourceFile->getFileName(), $resizedFile->getFileName());
        $this->assertEquals($sourceFile->getMIME(), $resizedFile->getMIME());

        $resizedFile = $ImageResizeProcessor->process($sourceFile, "1440L");

        list($xLength, $yLength) = getimagesizefromstring($resizedFile->getBinaryFile());
        $this->assertEquals(960, $xLength);
        $this->assertEquals(1440, $yLength);

        $this->assertEquals($sourceFile->getFileName(), $resizedFile->getFileName());
        $this->assertEquals($sourceFile->getMIME(), $resizedFile->getMIME());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function test_resizeBadGeometry_failed()
    {
        $ImageResizeProcessor = new ImageResizeProcessor();
        $sourceFile = $this->getFile();
        $ImageResizeProcessor->process($sourceFile, "1000");
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function test_resizeBadQuality_failed()
    {
        $ImageResizeProcessor = new ImageResizeProcessor();
        $sourceFile = $this->getFile();
        $ImageResizeProcessor->process($sourceFile, "960w", -1);
    }
}