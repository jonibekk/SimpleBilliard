<?php
App::uses('GoalousTestCase', 'Test');
App::import('Lib/Cache/Redis/Upload', 'UploadRedisClient');
App::import('Service', 'UploadService');
App::import('Lib/Upload', 'UploadedFile');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/07/27
 * Time: 18:29
 */

/**
 * Run 'redis-cli flushall' before re-running test!
 * Class UploadServiceTest
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

    /**
     * 6 kb image file
     *
     * @var string
     */
    private $encodedFile = "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";

    private $fileName = "200px-Noto_Emoji_KitKat_263a.svg";

    public function test_addFileToBuffer_success()
    {
        /** @var UploadService $UploadService */
        $UploadService = ClassRegistry::init('UploadService');

        $uuid = $UploadService->buffer(1, 1, $this->encodedFile, $this->fileName);

        $this->assertNotEmpty($uuid);
        $this->assertInternalType('string', $uuid);
        $this->assertEquals(1, preg_match("/[A-Fa-f0-9]{14}.[A-Fa-f0-9]{8}/", $uuid));
    }

    public function test_readFromBuffer_success()
    {
        /** @var UploadService $UploadService */
        $UploadService = ClassRegistry::init('UploadService');

        $UploadedFile = new UploadedFile($this->encodedFile, $this->fileName);

        $key = $UploadService->buffer(2, 1, $this->encodedFile, $this->fileName);

        $returnData = $UploadService->read(2, 1, $key);

        $this->assertEquals($UploadedFile->getBinaryString(), $returnData->getBinaryString());
        $this->assertEquals($UploadedFile->getFileName(), $returnData->getFileName());
        $this->assertEquals($UploadedFile->getMetadata(), $returnData->getMetadata());
        $this->assertNotEquals($UploadedFile->getUUID(), $returnData->getUUID());
    }

    public function test_countBuffer_success()
    {
        /** @var UploadService $UploadService */
        $UploadService = ClassRegistry::init('UploadService');

        $UploadService->buffer(6, 1, $this->encodedFile, $this->fileName);
        $UploadService->buffer(6, 1, $this->encodedFile, $this->fileName);
        $UploadService->buffer(6, 1, $this->encodedFile, $this->fileName);

        $UploadService->buffer(7, 1, $this->encodedFile, $this->fileName);

        $this->assertEquals(3, $UploadService->countBuffer(6, 1));
        $this->assertEquals(1, $UploadService->countBuffer(7, 1));
    }

    public function test_deleteFileFromBuffer_success()
    {
        /** @var UploadService $UploadService */
        $UploadService = ClassRegistry::init('UploadService');

        $uuid1 = $UploadService->buffer(4, 1, $this->encodedFile, $this->fileName);
        $uuid2 = $UploadService->buffer(5, 1, $this->encodedFile, $this->fileName);

        $UploadService->delete(4, 1, $uuid1);

        $this->assertEquals(0, $UploadService->countBuffer(4, 1));
        $this->assertEquals(1, $UploadService->countBuffer(5, 1));

        $this->assertEmpty($UploadService->read(4, 1, $uuid1));
        $this->assertNotEmpty($UploadService->read(5, 1, $uuid2));
    }

    public function test_deleteAllFileFromBuffer_success()
    {
        /** @var UploadService $UploadService */
        $UploadService = ClassRegistry::init('UploadService');

        $uuid1 = $UploadService->buffer(3, 1, $this->encodedFile, $this->fileName);
        $uuid2 = $UploadService->buffer(3, 1, $this->encodedFile, $this->fileName);

        $UploadService->delete(3, 1);

        $this->assertEquals(0, $UploadService->countBuffer(3, 1));

        $this->assertEmpty($UploadService->read(3, 1, $uuid1));
        $this->assertEmpty($UploadService->read(3, 1, $uuid2));
    }

    public function test_autoDeleteOverLimit_success()
    {
        /** @var UploadService $UploadService */
        $UploadService = ClassRegistry::init('UploadService');

        $count = 0;

        $uuid1 = $UploadService->buffer(8, 1, $this->encodedFile, $this->fileName);
        sleep(2);
        $uuid2 = $UploadService->buffer(8, 1, $this->encodedFile, $this->fileName);
        sleep(2);
        $uuid3 = $UploadService->buffer(8, 1, $this->encodedFile, $this->fileName);
        sleep(2);

        while ($count++ < 20) {
            $UploadService->buffer(8, 1, $this->encodedFile, $this->fileName);
        }

        $this->assertEquals(20, $UploadService->countBuffer(8, 1));
        $this->assertEmpty($UploadService->read(8, 1, $uuid1));
        $this->assertEmpty($UploadService->read(8, 1, $uuid2));
        $this->assertEmpty($UploadService->read(8, 1, $uuid3));
    }
}