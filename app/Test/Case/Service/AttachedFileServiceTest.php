<?php
App::uses('GoalousTestCase', 'Test');
App::import('Service', 'AttachedFileService');

/**
 * GroupServiceTest Class
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 2016/12/08
 * Time: 17:50
 *
 * @property AttachedFileService $AttachedFileService
 * @property AttachedFile        $AttachedFile
 */
class AttachedFileServiceTest extends GoalousTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.attached_file',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->AttachedFileService = ClassRegistry::init('AttachedFileService');
        $this->AttachedFile = ClassRegistry::init('AttachedFile');
        $this->AttachedFile->my_uid = 1;
        $this->AttachedFile->current_team_id = 1;
    }

    function testPreUpLoadFileSuccess()
    {
        $data = [
            'file' => [
                'name'     => 'test',
                'type'     => 'image/jpeg',
                'tmp_name' => IMAGES . 'no-image.jpg',
                'size'     => '12345',
            ]
        ];
        $res = $this->AttachedFileService->preUploadFile($data);
        $this->assertFalse($res['error']);
    }

    function testPreUpLoadFileFailEmpty()
    {
        $res = $this->AttachedFileService->preUploadFile([]);
        $this->assertTrue($res['error']);
    }

    function testPreUpLoadFileFailSizeOver()
    {
        $data = [
            'file' => [
                'name'     => 'test',
                'type'     => 'image/jpeg',
                'tmp_name' => IMAGES . 'no-image.jpg',
                'size'     => Attachedfile::ATTACHABLE_MAX_FILE_SIZE_MB * 1024 * 1024 + 1,
            ]
        ];
        $res = $this->AttachedFileService->preUploadFile($data);
        $this->assertTrue($res['error']);
    }

}
