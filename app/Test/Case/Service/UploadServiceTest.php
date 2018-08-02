<?php
App::uses('GoalousTestCase', 'Test');
App::import('Service', 'UploadService');
App::import('Lib/Upload', 'UploadedFile');
App::import('Lib/Upload/Uploader', 'UploaderFactory');
App::import('Lib/Upload/Uploader/Local', 'LocalUploader');
App::import('Lib/Upload/Uploader/S3', 'S3Uploader');

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

    public function test_addFileToBuffer_success()
    {
        /** @var UploadService $UploadService */
        $UploadService = ClassRegistry::init('UploadService');

        $uuid = $UploadService->buffer(1, 1, $this->testEncodedFileData, $this->testFileName);

        $this->assertNotEmpty($uuid);
        $this->assertInternalType('string', $uuid);
        $this->assertEquals(1, preg_match("/[A-Fa-f0-9]{14}.[A-Fa-f0-9]{8}/", $uuid));

        $uploader = UploaderFactory::generate(1, 1);
        $file = $uploader->getBuffer($uuid);

        $this->assertEquals($uuid, $file->getUUID());
        $this->assertEquals($this->testEncodedFileData, $file->getEncodedFile());
        $this->assertEquals($this->testFileName, $file->getFileName());
    }
}