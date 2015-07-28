<?php
App::uses('AttachedFile', 'Model');

/**
 * AttachedFile Test Case
 *
 * @property AttachedFile $AttachedFile
 */
class AttachedFileTest extends CakeTestCase
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
        'app.badge',
        'app.circle',
        'app.circle_member',
        'app.post_share_circle',
        'app.post',
        'app.goal',
        'app.purpose',
        'app.goal_category',
        'app.key_result',
        'app.action_result',
        'app.collaborator',
        'app.approval_history',
        'app.follower',
        'app.evaluation',
        'app.evaluate_term',
        'app.evaluator',
        'app.evaluate_score',
        'app.comment',
        'app.comment_like',
        'app.comment_read',
        'app.post_share_user',
        'app.post_like',
        'app.post_read',
        'app.comment_mention',
        'app.given_badge',
        'app.post_mention',
        'app.group',
        'app.member_group',
        'app.group_vision',
        'app.invite',
        'app.job_category',
        'app.team_member',
        'app.member_type',
        'app.thread',
        'app.message',
        'app.evaluation_setting',
        'app.team_vision',
        'app.email',
        'app.notify_setting',
        'app.oauth_token',
        'app.local_name',
        'app.comment_file',
        'app.post_file'
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
    }

    function testPreUpLoadFileSuccess()
    {
        $data = [
            'file' => [
                'name'     => 'test',
                'type'     => 'image/jpeg',
                'tmp_name' => IMAGES . 'no-image.jpg'
            ]
        ];
        $res = $this->AttachedFile->preUploadFile($data);
        $this->assertNotEmpty($res);
    }

    function testPreUpLoadFileFail()
    {
        $res = $this->AttachedFile->preUploadFile([]);
        $this->assertFalse($res);
    }

    function testCancelUploadFileSuccess()
    {
        $data = [
            'file' => [
                'name'     => 'test',
                'type'     => 'image/jpeg',
                'tmp_name' => IMAGES . 'no-image.jpg'
            ]
        ];
        $hashed_key = $this->AttachedFile->preUploadFile($data);
        $res = $this->AttachedFile->cancelUploadFile($hashed_key);
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
        $hashes = $this->_prepareTestFiles();
        $upload_setting = $this->AttachedFile->actsAs['Upload'];
        $upload_setting['attached']['path'] = ":webroot/upload/test/:model/:id/:hash_:style.:extension";
        $this->AttachedFile->Behaviors->load('Upload', $upload_setting);
        $this->AttachedFile->saveRelatedFiles(1, AttachedFile::TYPE_MODEL_POST, $hashes);
        $res = $this->AttachedFile->saveRelatedFiles(1, AttachedFile::TYPE_MODEL_POST, [$hash_1, $hash_2]);
        $this->assertTrue($res);
        $this->assertCount(2, $this->AttachedFile->find('all'));
        $this->assertCount(2, $this->AttachedFile->PostFile->find('all'));
    }

    function testSaveRelatedFilesFail()
    {
        $res = $this->AttachedFile->saveRelatedFiles(1, 1000, ['test']);
        $this->assertFalse($res);
    }

    function testDeleteAllRelatedFilesSuccess()
    {
        $this->_setDefault();
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

    function _prepareTestFiles()
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
                'size'     => 1000,
                'remote'   => true
            ]
        ];
        $hash_1 = $this->AttachedFile->preUploadFile($data);
        $data = [
            'file' => [
                'name'     => 'test.php',
                'type'     => 'test/php',
                'tmp_name' => $file_2_path,
                'size'     => 1000,
                'remote'   => true
            ]
        ];
        $hash_2 = $this->AttachedFile->preUploadFile($data);

        return [$hash_1, $hash_2];
    }

}
