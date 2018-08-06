<?php
App::uses('GoalousTestCase', 'Test');
App::import('Service', 'UploadService');
App::import('Lib/Upload', 'UploadedFile');
App::import('Lib/Storage/Client', 'BufferStorageClient');
App::import('Lib/Storage/Client', 'AssetsStorageClient');

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
        var_dump(ENV_NAME);
        $uuid = $UploadService->buffer(1, 1, $this->getTestFileData(), $this->getTestFileName());

        $this->assertNotEmpty($uuid);
        $this->assertInternalType('string', $uuid);
        $this->assertEquals(1, preg_match("/[A-Fa-f0-9]{14}.[A-Fa-f0-9]{8}/", $uuid));

        $uploader = new BufferStorageClient(1, 1);
        $file = $uploader->get($uuid);

        $this->assertEquals($uuid, $file->getUUID());
        $this->assertEquals($this->getTestFileData(), $file->getEncodedFile());
        $this->assertEquals($this->getTestFileName(), $file->getFileName());
    }

    public function test_getFileFromBuffer_success()
    {
    }



}