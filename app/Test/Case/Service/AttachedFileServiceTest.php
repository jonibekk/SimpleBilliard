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

    function test_get()
    {
        $modelName = 'AttachedFile';
        $id = 1;
        /* First data */
        // Save cache
        $data = $this->AttachedFileService->get($id);
        $this->assertNotEmpty($data);
        $cacheList = $this->AttachedFileService->getCacheList();
        $this->assertSame($data, $cacheList[$modelName][$id]);

        // Check data is as same as data getting from db directly
        $ret = $this->AttachedFile->useType()->findById($id)[$modelName];
        // Extract only db record columns(exclude additional data. e.g. img_url)
        $tmp = array_intersect_key($data, $ret);
        $this->assertSame($tmp, $ret);

        // Get from cache
        $data = $this->AttachedFileService->get($id);
        $this->assertSame($data, $cacheList[$modelName][$id]);

        /* Multiple data */
        // Save cache
        $id2 = 2;
        $data2 = $this->AttachedFileService->get($id2);
        $this->assertNotEmpty($data2);
        $cacheList = $this->AttachedFileService->getCacheList();
        $this->assertSame($data2, $cacheList[$modelName][$id2]);

        $ret = $this->AttachedFile->useType()->findById($id2)[$modelName];
        $tmp = array_intersect_key($data2, $ret);
        $this->assertSame($tmp, $ret);

        // Get from cache
        $data2 = $this->AttachedFileService->get($id2);
        $this->assertSame($data2, $cacheList[$modelName][$id2]);
        $this->assertNotEquals($data, $data2);

        /* Empty */
        $id = 0;
        $data = $this->AttachedFileService->get($id);
        $this->assertSame($data, []);
        $cacheList = $this->    AttachedFileService->getCacheList();
        $this->assertFalse(array_key_exists($id, $cacheList[$modelName]));

        $id = 9999999;
        $data = $this->AttachedFileService->get($id);
        $this->assertSame($data, []);
        $cacheList = $this->AttachedFileService->getCacheList();
        $this->assertSame($data, $cacheList[$modelName][$id]);
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
