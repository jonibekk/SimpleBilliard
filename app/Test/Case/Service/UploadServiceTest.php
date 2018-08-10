<?php
App::uses('GoalousTestCase', 'Test');
App::uses('UploadService', 'Service');
App::import('Service', 'UploadService');
App::import('Lib/Upload', 'UploadedFile');
App::import('Lib/Storage/Client', 'BufferStorageClient');
App::import('Lib/Storage/Client', 'AssetsStorageClient');

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
        $returnValue = "1234567890abcd.12345678";

        $bufferClient = mock::mock('BufferStorageClient');

        $bufferClient->shouldReceive('save')
                     ->once()
                     ->andReturn($returnValue);

        ClassRegistry::addObject(BufferStorageClient::class, $bufferClient);

        /** @var UploadService $UploadService */
        $UploadService = ClassRegistry::init('UploadService');
        $mocked = $UploadService->buffer(1, 1, $this->getTestFileData(), $this->getTestFileName());

        $this->assertEquals($returnValue, $mocked);
    }
}