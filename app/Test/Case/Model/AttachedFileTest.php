<?php App::uses('GoalousTestCase', 'Test');
App::uses('AttachedFile', 'Model');
App::import('Service', 'AttachedFileService');

/**
 * AttachedFile Test Case
 *
 * @property AttachedFile $AttachedFile
 * @property Post $Post
 * @property PostFile $PostFile
 * @property ActionResultFile $ActionResultFile
 */
class AttachedFileTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.attached_file',
        'app.user',
        'app.team',
        'app.circle',
        'app.circle_member',
        'app.post',
        'app.goal',
        'app.action_result',
        'app.comment',
        'app.email',
        'app.notify_setting',
        'app.comment_file',
        'app.post_file',
        'app.action_result_file',
        'app.post_share_circle',
        'app.post_share_user',
        'app.message_file',
        'app.post_resource',
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->AttachedFile = ClassRegistry::init('AttachedFile');
        $this->Post = ClassRegistry::init('Post');
        $this->PostFile = ClassRegistry::init('PostFile');
        $this->ActionResultFile = ClassRegistry::init('ActionResultFile');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->AttachedFile);

        parent::tearDown();
    }

    function _setDefault()
    {
        $this->AttachedFile->current_team_id = 1;
        $this->AttachedFile->my_uid = 1;
        $this->AttachedFile->PostFile->current_team_id = 1;
        $this->AttachedFile->PostFile->my_uid = 1;
        $this->AttachedFile->CommentFile->current_team_id = 1;
        $this->AttachedFile->CommentFile->my_uid = 1;
        $this->AttachedFile->PostFile->Post->current_team_id = 1;
        $this->AttachedFile->PostFile->Post->my_uid = 1;
        $this->AttachedFile->PostFile->Post->PostShareCircle->current_team_id = 1;
        $this->AttachedFile->PostFile->Post->PostShareCircle->my_uid = 1;
        $this->AttachedFile->PostFile->Post->PostShareUser->current_team_id = 1;
        $this->AttachedFile->PostFile->Post->PostShareUser->my_uid = 1;
        $this->AttachedFile->PostFile->Post->PostShareCircle->Circle->CircleMember->my_uid = 1;
        $this->AttachedFile->PostFile->Post->PostShareCircle->Circle->CircleMember->current_team_id = 1;
    }

    function testCancelUploadFileSuccess()
    {
        $data = [
            'file' => [
                'name'     => 'test',
                'type'     => 'image/jpeg',
                'tmp_name' => IMAGES . 'no-image.jpg',
                'size'     => '12345',
            ]
        ];
        /** @var AttachedFileService $AttachedFileService */
        $AttachedFileService = ClassRegistry::init('AttachedFileService');
        $resPreUpload = $AttachedFileService->preUploadFile($data);
        $res = $this->AttachedFile->cancelUploadFile($resPreUpload['id']);
        $this->assertTrue($res);
    }

    function testCancelUploadFileFail()
    {
        $res = $this->AttachedFile->cancelUploadFile(null);
        $this->assertFalse($res);
    }

    function testIsUnavailableModelTypeFalse()
    {
        $res = $this->AttachedFile->isUnavailableModelType(AttachedFile::TYPE_MODEL_POST);
        $this->assertFalse($res);
    }

    function testIsUnavailableModelTypeTrue()
    {
        $res = $this->AttachedFile->isUnavailableModelType(1000);
        $this->assertTrue($res);
    }

    function testSaveRelatedFilesSuccess()
    {
        $this->_setDefault();
        $this->_resetTable();
        $hashes = $this->_prepareTestFiles();
        $upload_setting = $this->AttachedFile->actsAs['Upload'];
        $upload_setting['attached']['path'] = ":webroot/upload/test/:model/:id/:hash_:style.:extension";
        $this->AttachedFile->Behaviors->load('Upload', $upload_setting);
        $res = $this->AttachedFile->saveRelatedFiles(1, AttachedFile::TYPE_MODEL_POST, $hashes);
        $this->assertTrue($res);
        $this->assertCount(2, $this->AttachedFile->find('all'));
        $this->assertCount(2, $this->AttachedFile->PostFile->find('all'));
    }

    function testSaveRelatedFilesActionImgSuccess()
    {
        $this->_setDefault();
        $this->_resetTable();
        $hashes = $this->_prepareTestFiles();
        $upload_setting = $this->AttachedFile->actsAs['Upload'];
        $upload_setting['attached']['path'] = ":webroot/upload/test/:model/:id/:hash_:style.:extension";
        $this->AttachedFile->Behaviors->load('Upload', $upload_setting);
        $res = $this->AttachedFile->saveRelatedFiles(1, AttachedFile::TYPE_MODEL_ACTION_RESULT, $hashes);
        $this->assertTrue($res);
        $this->assertCount(2, $this->AttachedFile->find('all'));
        $this->assertCount(2, $this->AttachedFile->ActionResultFile->find('all'));
        $options = [
            'conditions' => [
                'removable_flg'         => false,
                'display_file_list_flg' => false
            ],
            'contain'    => [
                'ActionResultFile'
            ]
        ];
        $main_img = $this->AttachedFile->find('all', $options);
        $this->assertCount(1, $main_img);
        $this->assertEquals(0, $main_img[0]['ActionResultFile'][0]['index_num']);
    }

    function testSaveRelatedFilesFail()
    {
        $res = $this->AttachedFile->saveRelatedFiles(1, 1000, ['test']);
        $this->assertFalse($res);
    }

    function testUpdateRelatedFilesFail()
    {
        $res = $this->AttachedFile->updateRelatedFiles(1, 1000, [1, 2], ['test']);
        $this->assertFalse($res);
    }

    /**
     * ファイルアップデートのテスト
     * もともと２ファイルを持っており、１ファイル削除し、２ファイル追加したときに合計３ファイルになる事を確認
     */
    function testUpdateRelatedFilesSuccess()
    {
        $this->_setDefault();
        $this->_resetTable();
        $hashes = $this->_prepareTestFiles();
        $upload_setting = $this->AttachedFile->actsAs['Upload'];
        $upload_setting['attached']['path'] = ":webroot/upload/test/:model/:id/:hash_:style.:extension";
        $this->AttachedFile->Behaviors->load('Upload', $upload_setting);
        $prepare_post_file_data = [
            'AttachedFile' => [
                'attached_file_name' => 'test.jpg',
                'user_id'            => 1,
                'team_id'            => 1,
                'file_type'          => AttachedFile::TYPE_FILE_IMG,
                'file_ext'           => 'jpg',
                'file_size'          => '1111',
                'model_type'         => AttachedFile::TYPE_MODEL_POST
            ],
            'PostFile'     => [
                [
                    'post_id'   => 1,
                    'index_num' => 0,
                    'team_id'   => 1,
                ]
            ]
        ];
        $this->AttachedFile->saveAll($prepare_post_file_data);
        $prepare_post_file_data['PostFile']['index_num'] = 1;
        $this->AttachedFile->saveAll($prepare_post_file_data);
        $res = $this->AttachedFile->updateRelatedFiles(1, AttachedFile::TYPE_MODEL_POST, array_merge([1], $hashes),
            [2]);
        $files = $this->AttachedFile->find('all');
        $post_files = $this->AttachedFile->PostFile->find('all');
        $this->assertTrue($res);
        $this->assertCount(3, $files);
        $this->assertCount(3, $post_files);
        $this->assertEquals(4, $post_files[1]['PostFile']['id']);
        $this->assertEquals(3, $post_files[1]['PostFile']['attached_file_id']);
    }

    /**
     * ファイルアップデートのテスト(アクション)
     * もともと２ファイルを持っており、１ファイル削除し、２ファイル追加したときに合計３ファイルになる事を確認
     */
    function testUpdateRelatedFilesActionSuccess()
    {
        $this->_setDefault();
        $this->_resetTable();
        $hashes = $this->_prepareTestFiles();
        $upload_setting = $this->AttachedFile->actsAs['Upload'];
        $upload_setting['attached']['path'] = ":webroot/upload/test/:model/:id/:hash_:style.:extension";
        $this->AttachedFile->Behaviors->load('Upload', $upload_setting);
        $prepare_post_file_data = [
            'AttachedFile'     => [
                'attached_file_name' => 'test_abc.jpg',
                'user_id'            => 1,
                'team_id'            => 1,
                'file_type'          => AttachedFile::TYPE_FILE_IMG,
                'file_ext'           => 'jpg',
                'file_size'          => '1111',
                'model_type'         => AttachedFile::TYPE_MODEL_ACTION_RESULT
            ],
            'ActionResultFile' => [
                [
                    'action_result_id' => 1,
                    'index_num'        => 0,
                    'team_id'          => 1,
                ]
            ]
        ];
        $this->AttachedFile->saveAll($prepare_post_file_data);
        $prepare_post_file_data['ActionResultFile'][0]['index_num'] = 1;
        $prepare_post_file_data['AttachedFile']['attached_file_name'] = 'test_zzz.jpg';
        $this->AttachedFile->saveAll($prepare_post_file_data);
        $res = $this->AttachedFile->updateRelatedFiles(1, AttachedFile::TYPE_MODEL_ACTION_RESULT,
            array_merge($hashes, [2]),
            [1]);
        $files = $this->AttachedFile->find('all');
        $action_res_files = $this->AttachedFile->ActionResultFile->find('all', ['order' => ['index_num asc']]);
        $main_img = $this->AttachedFile->find('all', ['conditions' => ['display_file_list_flg' => false]]);
        $main_img_action_res_file = $this->AttachedFile->ActionResultFile->find('first',
            ['conditions' => ['attached_file_id' => $main_img[0]['AttachedFile']['id']]]);
        $this->assertTrue($res);
        $this->assertCount(3, $files);
        $this->assertCount(3, $action_res_files);
        $this->assertCount(1, $main_img);
        $this->assertFalse($main_img[0]['AttachedFile']['display_file_list_flg']);
        $this->assertFalse($main_img[0]['AttachedFile']['removable_flg']);
        $this->assertEquals(0, $main_img_action_res_file['ActionResultFile']['index_num']);
    }

    function testUpdateRelatedFilesActionNoChangesSuccess()
    {
        $this->_setDefault();
        $this->_resetTable();
        $hashes = $this->_prepareTestFiles();
        $upload_setting = $this->AttachedFile->actsAs['Upload'];
        $upload_setting['attached']['path'] = ":webroot/upload/test/:model/:id/:hash_:style.:extension";
        $this->AttachedFile->Behaviors->load('Upload', $upload_setting);
        $prepare_post_file_data = [
            'AttachedFile'     => [
                'attached_file_name'    => 'test_abc.jpg',
                'user_id'               => 1,
                'team_id'               => 1,
                'file_type'             => AttachedFile::TYPE_FILE_IMG,
                'file_ext'              => 'jpg',
                'file_size'             => '1111',
                'model_type'            => AttachedFile::TYPE_MODEL_ACTION_RESULT,
                'removable_flg'         => false,
                'display_file_list_flg' => false,

            ],
            'ActionResultFile' => [
                [
                    'action_result_id' => 1,
                    'index_num'        => 0,
                    'team_id'          => 1,
                ]
            ]
        ];
        $this->AttachedFile->saveAll($prepare_post_file_data);
        $prepare_post_file_data['ActionResultFile'][0]['index_num'] = 1;
        $prepare_post_file_data['AttachedFile']['attached_file_name'] = 'test_zzz.jpg';
        $this->AttachedFile->saveAll($prepare_post_file_data);
        $res = $this->AttachedFile->updateRelatedFiles(1, AttachedFile::TYPE_MODEL_ACTION_RESULT,
            array_merge([1], $hashes), [2]);
        $files = $this->AttachedFile->find('all');
        $action_res_files = $this->AttachedFile->ActionResultFile->find('all', ['order' => ['index_num asc']]);
        $main_img = $this->AttachedFile->find('all', ['conditions' => ['display_file_list_flg' => false]]);
        $main_img_action_res_file = $this->AttachedFile->ActionResultFile->find('first',
            ['conditions' => ['attached_file_id' => $main_img[0]['AttachedFile']['id']]]);
        $this->assertTrue($res);
        $this->assertCount(3, $files);
        $this->assertCount(3, $action_res_files);
        $this->assertCount(1, $main_img);
        $this->assertFalse($main_img[0]['AttachedFile']['display_file_list_flg']);
        $this->assertFalse($main_img[0]['AttachedFile']['removable_flg']);
        $this->assertEquals(0, $main_img_action_res_file['ActionResultFile']['index_num']);
    }

    function testDeleteAllRelatedFilesSuccess()
    {
        $this->_setDefault();
        $this->_resetTable();
        $hashes = $this->_prepareTestFiles();
        $upload_setting = $this->AttachedFile->actsAs['Upload'];
        $upload_setting['attached']['path'] = ":webroot/upload/test/:model/:id/:hash_:style.:extension";
        $this->AttachedFile->Behaviors->load('Upload', $upload_setting);
        $this->AttachedFile->saveRelatedFiles(1, AttachedFile::TYPE_MODEL_POST, $hashes);

        $res = $this->AttachedFile->deleteAllRelatedFiles(1, AttachedFile::TYPE_MODEL_POST);
        $this->assertTrue($res);
        $this->assertCount(0, $this->AttachedFile->find('all'));
        $this->assertCount(0, $this->AttachedFile->PostFile->find('all'));

    }

    function testDeleteAllRelatedFilesFail()
    {
        $res = $this->AttachedFile->deleteAllRelatedFiles(1, 1000);
        $this->assertFalse($res);
    }

    function testDeleteFile()
    {
        $this->_setDefault();
        $this->_resetTable();
        $hashes = $this->_prepareTestFiles();
        $upload_setting = $this->AttachedFile->actsAs['Upload'];
        $upload_setting['attached']['path'] = ":webroot/upload/test/:model/:id/:hash_:style.:extension";
        $this->AttachedFile->Behaviors->load('Upload', $upload_setting);
        $this->AttachedFile->saveRelatedFiles(1, AttachedFile::TYPE_MODEL_POST, $hashes);
        $id = $this->AttachedFile->getLastInsertID();
        $this->AttachedFile->delete($id);
        $this->assertCount(1, $this->AttachedFile->find('all'));
        $this->assertCount(1, $this->AttachedFile->PostFile->find('all'));
    }

    function testGetFileTypeOptions()
    {
        $res = $this->AttachedFile->getFileTypeOptions();
        $this->assertNotEmpty($res);
    }

    function testGetFileTypeId()
    {
        $res = $this->AttachedFile->getFileTypeId('image');
        $this->assertEquals(AttachedFile::TYPE_FILE_IMG, $res);
        $res = $this->AttachedFile->getFileTypeId('not_found_item');
        $this->assertNull($res);
    }

    /**
     * MIMEタイプからファイル種別を取得
     */
    function test_getFileType()
    {
        $res = $this->AttachedFile->getFileType('image/jpeg');
        $this->assertEquals(AttachedFile::TYPE_FILE_IMG, $res);

        $res = $this->AttachedFile->getFileType('image/gif');
        $this->assertEquals(AttachedFile::TYPE_FILE_IMG, $res);

        $res = $this->AttachedFile->getFileType('image/png');
        $this->assertEquals(AttachedFile::TYPE_FILE_IMG, $res);

        $res = $this->AttachedFile->getFileType('image/x-photoshop');
        $this->assertEquals(AttachedFile::TYPE_FILE_DOC, $res);

        $res = $this->AttachedFile->getFileType('image/bmp');
        $this->assertEquals(AttachedFile::TYPE_FILE_DOC, $res);

        $res = $this->AttachedFile->getFileType('text/plain');
        $this->assertEquals(AttachedFile::TYPE_FILE_DOC, $res);

        $res = $this->AttachedFile->getFileType('application/pdf');
        $this->assertEquals(AttachedFile::TYPE_FILE_DOC, $res);

        $res = $this->AttachedFile->getFileType('video/avi');
        $this->assertEquals(AttachedFile::TYPE_FILE_VIDEO, $res);
    }

    function testGetCountOfAttachedFilesFalse()
    {
        $res = $this->AttachedFile->getCountOfAttachedFiles(1, 10000);
        $this->assertFalse($res);
    }

    function testGetCountOfAttachedFilesNoFileType()
    {
        $this->_setDefault();
        $this->_resetTable();
        $hashes = $this->_prepareTestFiles();
        $upload_setting = $this->AttachedFile->actsAs['Upload'];
        $upload_setting['attached']['path'] = ":webroot/upload/test/:model/:id/:hash_:style.:extension";
        $this->AttachedFile->Behaviors->load('Upload', $upload_setting);
        $this->AttachedFile->saveRelatedFiles(1, AttachedFile::TYPE_MODEL_POST, $hashes);
        $res = $this->AttachedFile->getCountOfAttachedFiles(1, AttachedFile::TYPE_MODEL_POST);
        $this->assertEquals(2, $res);
    }

    function testGetCountOfAttachedFilesWithFileType()
    {
        $this->_setDefault();
        $this->_resetTable();
        $hashes = $this->_prepareTestFiles();
        $upload_setting = $this->AttachedFile->actsAs['Upload'];
        $upload_setting['attached']['path'] = ":webroot/upload/test/:model/:id/:hash_:style.:extension";
        $this->AttachedFile->Behaviors->load('Upload', $upload_setting);
        $this->AttachedFile->saveRelatedFiles(1, AttachedFile::TYPE_MODEL_POST, $hashes);
        $res = $this->AttachedFile->getCountOfAttachedFiles(1, AttachedFile::TYPE_MODEL_POST,
            AttachedFile::TYPE_FILE_IMG);
        $this->assertEquals(1, $res);
    }

    function testIsReadable()
    {
        $this->_setDefault();

        // 投稿への添付ファイル
        $res = $this->AttachedFile->isReadable(1);
        $this->assertTrue($res);

        // 投稿のコメントへの添付ファイル
        $res = $this->AttachedFile->isReadable(3);
        $this->assertTrue($res);

        // アクションのコメントへの添付ファイル
        $res = $this->AttachedFile->isReadable(4);
        $this->assertTrue($res);

        // 公開サークルへの添付ファイル
        $res = $this->AttachedFile->isReadable(5);
        $this->assertTrue($res);

        // 個人共有投稿の添付ファイル
        $res = $this->AttachedFile->isReadable(6);
        $this->assertTrue($res);

        // アクションへの添付ファイル
        $res = $this->AttachedFile->isReadable(2);
        $this->assertTrue($res);

        // 秘密サークルへの添付ファイル
        $res = $this->AttachedFile->isReadable(7);
        $this->assertTrue($res);

        Cache::delete($this->AttachedFile->getCacheKey(CACHE_KEY_CHANNEL_CIRCLES_ALL, true), 'user_data');
        Cache::delete($this->AttachedFile->getCacheKey(CACHE_KEY_CHANNEL_CIRCLES_NOT_HIDE, true), 'user_data');

        // 秘密サークルへの添付ファイル
        $res = $this->AttachedFile->isReadable(7, 4, 1);
        $this->assertFalse($res);


        // 存在しないファイルID
        $res = $this->AttachedFile->isReadable(99889988);
        $this->assertFalse($res);
    }

    function testGetFileUrl()
    {
        $this->_setDefault();
        // 正常
        $url = $this->AttachedFile->getFileUrl(1);
        $this->assertNotEmpty($url);

        // 存在しないファイルID
        $url = $this->AttachedFile->getFileUrl(99889988);
        $this->assertEmpty($url);
    }

    function _prepareTestFiles($file_size = 1000)
    {
        $destDir = TMP . 'attached_file';
        if (!file_exists($destDir)) {
            @mkdir($destDir, 0777, true);
            @chmod($destDir, 0777);
        }
        $file_1_path = TMP . 'attached_file' . DS . 'attached_file_1.jpg';
        $file_2_path = TMP . 'attached_file' . DS . 'attached_file_2.php';
        copy(IMAGES . 'no-image.jpg', $file_1_path);
        copy(APP . WEBROOT_DIR . DS . 'test.php', $file_2_path);

        $data = [
            'file' => [
                'name'     => 'test.jpg',
                'type'     => 'image/jpeg',
                'tmp_name' => $file_1_path,
                'size'     => $file_size,
                'remote'   => true
            ]
        ];
        App::import('Service', 'AttachedFileService');
        /** @var AttachedFileService $AttachedFileService */
        $AttachedFileService = ClassRegistry::init('AttachedFileService');
        $hash_1 = $AttachedFileService->preUploadFile($data);
        $data = [
            'file' => [
                'name'     => 'test.php',
                'type'     => 'test/php',
                'tmp_name' => $file_2_path,
                'size'     => 1000,
                'remote'   => true
            ]
        ];
        $hash_2 = $AttachedFileService->preUploadFile($data);

        return [$hash_1['id'], $hash_2['id']];
    }

    function _resetTable()
    {
        $tables = [
            $this->AttachedFile->useTable,
            $this->AttachedFile->PostFile->useTable,
            $this->AttachedFile->ActionResultFile->useTable,
            $this->AttachedFile->CommentFile->useTable,
        ];
        foreach ($tables as $table) {
            $this->AttachedFile->query("DELETE FROM {$table}");
            if ($this->AttachedFile->getDataSource()->config['datasource'] == 'Database/Sqlite') {
                $this->AttachedFile->query("delete from sqlite_sequence where name='{$table}'");
            } else {
                $this->AttachedFile->query("ALTER TABLE {$table} AUTO_INCREMENT = 1");
            }
        }
    }

    function test_findAttachedImgEachPost()
    {
        // Empty
        $teamId = 1;
        $userId = 1;
        // Empty record
        $res = $this->AttachedFile->findAttachedImgEachPost($teamId, []);
        $this->assertEquals($res, []);

        // saved_posts record exist, but posts record doesn't exist
        $this->Post->create();
        $this->Post->save([
            'team_id' => $teamId,
            'user_id' => $userId,
            'type' => Post::TYPE_NORMAL,
        ]);
        $postId = $this->Post->getLastInsertID();

        $this->AttachedFile->create();
        $this->AttachedFile->save([
            'team_id' => $teamId,
            'user_id' => $userId
        ]);
        $attachedFileId = $this->AttachedFile->getLastInsertID();
        $res = $this->AttachedFile->findAttachedImgEachPost($teamId, [$postId]);
        $this->assertEquals($res, []);

        $this->PostFile->create();
        $this->PostFile->save([
            'team_id' => $teamId,
            'post_id' => $postId,
            'attached_file_id' => $attachedFileId
        ]);
        $res = $this->AttachedFile->findAttachedImgEachPost($teamId, [$postId]);
        $this->assertEquals(count($res), 1);
        $this->assertEquals($res[0]['id'], $attachedFileId);
        $this->assertEquals($res[0]['post_id'], $postId);

        // 1 post with multiple files
        $this->AttachedFile->create();
        $this->AttachedFile->save([
            'team_id' => $teamId,
            'user_id' => $userId
        ]);
        $attachedFileId2 = $this->AttachedFile->getLastInsertID();
        $this->PostFile->create();
        $this->PostFile->save([
            'team_id' => $teamId,
            'post_id' => $postId,
            'attached_file_id' => $attachedFileId2
        ]);
        $res = $this->AttachedFile->findAttachedImgEachPost($teamId, [$postId]);
        $this->assertEquals(count($res), 1);
        $this->assertEquals($res[0]['id'], $attachedFileId);
        $this->assertEquals($res[0]['post_id'], $postId);


        // multiple posts
        $this->Post->create();
        $this->Post->save([
            'team_id' => $teamId,
            'user_id' => $userId,
            'type' => Post::TYPE_NORMAL,
        ]);
        $postId2 = $this->Post->getLastInsertID();

        $this->AttachedFile->create();
        $this->AttachedFile->save([
            'team_id' => $teamId,
            'user_id' => $userId
        ]);
        $attachedFileId3 = $this->AttachedFile->getLastInsertID();

        $this->PostFile->create();
        $this->PostFile->save([
            'team_id' => $teamId,
            'post_id' => $postId2,
            'attached_file_id' => $attachedFileId3
        ]);
        $res = $this->AttachedFile->findAttachedImgEachPost($teamId, [$postId, $postId2]);
        $this->assertEquals(count($res), 2);
        $res = $this->AttachedFile->findAttachedImgEachPost($teamId, [$postId2]);
        $this->assertEquals(count($res), 1);
        $this->assertEquals($res[0]['id'], $attachedFileId3);
        $this->assertEquals($res[0]['post_id'], $postId2);
    }

    function test_findAttachedImgEachAction()
    {
        // Empty
        $teamId = 1;
        $userId = 1;
        // Empty record
        $res = $this->AttachedFile->findAttachedImgEachAction($teamId, []);
        $this->assertEquals($res, []);

        // saved_posts record exist, but posts record doesn't exist
        $this->Post->create();
        $this->Post->save([
            'team_id' => $teamId,
            'user_id' => $userId,
            'type' => Post::TYPE_NORMAL,
        ]);
        $actionResultId = $this->Post->getLastInsertID();

        $this->AttachedFile->create();
        $this->AttachedFile->save([
            'team_id' => $teamId,
            'user_id' => $userId
        ]);
        $attachedFileId = $this->AttachedFile->getLastInsertID();
        $res = $this->AttachedFile->findAttachedImgEachAction($teamId, [$actionResultId]);
        $this->assertEquals($res, []);

        $this->ActionResultFile->create();
        $this->ActionResultFile->save([
            'team_id' => $teamId,
            'action_result_id' => $actionResultId,
            'attached_file_id' => $attachedFileId
        ]);
        $res = $this->AttachedFile->findAttachedImgEachAction($teamId, [$actionResultId]);
        $this->assertEquals(count($res), 1);
        $this->assertEquals($res[0]['id'], $attachedFileId);
        $this->assertEquals($res[0]['action_result_id'], $actionResultId);

        // 1 post with multiple files
        $this->AttachedFile->create();
        $this->AttachedFile->save([
            'team_id' => $teamId,
            'user_id' => $userId
        ]);
        $attachedFileId2 = $this->AttachedFile->getLastInsertID();
        $this->ActionResultFile->create();
        $this->ActionResultFile->save([
            'team_id' => $teamId,
            'action_result_id' => $actionResultId,
            'attached_file_id' => $attachedFileId2
        ]);
        $res = $this->AttachedFile->findAttachedImgEachAction($teamId, [$actionResultId]);
        $this->assertEquals(count($res), 1);
        $this->assertEquals($res[0]['id'], $attachedFileId);
        $this->assertEquals($res[0]['action_result_id'], $actionResultId);


        // multiple posts
        $this->Post->create();
        $this->Post->save([
            'team_id' => $teamId,
            'user_id' => $userId,
            'type' => Post::TYPE_NORMAL,
        ]);
        $actionResultId2 = $this->Post->getLastInsertID();

        $this->AttachedFile->create();
        $this->AttachedFile->save([
            'team_id' => $teamId,
            'user_id' => $userId
        ]);
        $attachedFileId3 = $this->AttachedFile->getLastInsertID();

        $this->ActionResultFile->create();
        $this->ActionResultFile->save([
            'team_id' => $teamId,
            'action_result_id' => $actionResultId2,
            'attached_file_id' => $attachedFileId3
        ]);
        $res = $this->AttachedFile->findAttachedImgEachAction($teamId, [$actionResultId, $actionResultId2]);
        $this->assertEquals(count($res), 2);
        $res = $this->AttachedFile->findAttachedImgEachAction($teamId, [$actionResultId2]);
        $this->assertEquals(count($res), 1);
        $this->assertEquals($res[0]['id'], $attachedFileId3);
        $this->assertEquals($res[0]['action_result_id'], $actionResultId2);
    }
}
