<?php
App::uses('GoalousTestCase', 'Test');
App::uses('UploadService', 'Service');
App::import('Service', 'UploadService');
App::import('Lib/Upload', 'UploadedFile');
App::import('Lib/Storage/Client', 'BufferStorageClient');
App::import('Lib/Storage/Client', 'AssetsStorageClient');
App::import('Lib/Storage/Processor/Image', 'ImageRotateProcessor');
App::import('Lib/Storage/Processor/Image', 'ImageResizeProcessor');

use Mockery as mock;

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/07/27
 * Time: 18:29
 */
class UploadServiceTest extends GoalousTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.team_member',
        'app.team',
        'app.user',
        'app.local_name'
    ];

    public function test_bufferFile_success()
    {
        $returnValue = "5beb86de5430f7.89168598";

        $bufferClient = mock::mock('BufferStorageClient');

        $bufferClient->shouldReceive('save')
                     ->once()
                     ->andReturn(true);

        ClassRegistry::addObject(BufferStorageClient::class, $bufferClient);

        /** @var UploadService $UploadService */
        $UploadService = ClassRegistry::init('UploadService');
        $ret = $UploadService->buffer(1, 1, $this->getTestFileData(), $this->getTestFileName());

        $this->assertTrue(!empty($ret) && is_string($ret));
    }

    public function test_saveFile_success()
    {
        $returnValue = true;

        $assetsClient = mock::mock('AssetsStorageClient');

        $assetsClient->shouldReceive('save')
                     ->once()
                     ->andReturn($returnValue);

        ClassRegistry::addObject(AssetsStorageClient::class, $assetsClient);

        /** @var UploadService $UploadService */
        $UploadService = ClassRegistry::init('UploadService');
        $result = $UploadService->save("Aaa", 1, new UploadedFile("as", "as"), "lala");

        $this->assertTrue($result);
    }

    public function test_processImage_success()
    {
        $fileName = "Portrait_4.jpg";
        $sourceFile = new UploadedFile($this->getTestFileData($fileName), $fileName);
        $ImageRotateProcessor = new ImageRotateProcessor();
        $ImageResizeProcessor = new ImageResizeProcessor();

        $file = $ImageResizeProcessor->process($sourceFile, "[1000x1000]");
        $file = $ImageRotateProcessor->process($file);

        list($xLength, $yLength) = getimagesizefromstring($file->getBinaryFile());
        list($res, $flip) = $ImageRotateProcessor->getRotation($file);
        $this->assertEquals($res, 0);
        $this->assertFalse($flip);
        $this->assertEquals(1000, $xLength);
        $this->assertEquals(1000, $yLength);
        $this->assertEquals($sourceFile->getFileName(), $file->getFileName());
        $this->assertEquals($sourceFile->getMIME(), $file->getMIME());
    }
}