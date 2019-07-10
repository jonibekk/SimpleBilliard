<?php
App::uses('GoalousTestCase', 'Test');
App::import('Service', 'ActionService');
App::import('Service', 'AttachedFileService');
App::uses('KeyResult', 'Model');
App::uses('Goal', 'Model');
App::uses('ActionResult', 'Model');
App::uses('AttachedFile', 'Model');


/**
 * AccessUser Test Case
 *
 * @property ActionService $ActionService
 */
class ActionServiceTest extends GoalousTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.term',
        'app.team',
        'app.attached_file',
        'app.term',
        'app.goal',
        'app.key_result',
        'app.action_result',
        'app.action_result_file',
        'app.kr_progress_log',
        'app.post',
        'app.goal_member',
        'app.team_member',
        'app.goal_label',
        'app.goal_category',
        'app.user',
        'app.team_translation_language',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->setDefaultTeamIdAndUid();
        $this->ActionService = ClassRegistry::init('ActionService');
        $this->AttachedFileService = ClassRegistry::init('AttachedFileService');
        $this->AttachedFile = ClassRegistry::init('AttachedFile');
        $this->ActionResult = ClassRegistry::init('ActionResult');
        $this->KeyResult = ClassRegistry::init('KeyResult');
        $this->_setDefault();
    }

    function _setDefault()
    {
        $this->ActionResult->current_team_id = 1;
        $this->ActionResult->my_uid = 1;
        $this->KeyResult->current_team_id = 1;
        $this->KeyResult->my_uid = 1;
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
    }

    /**
     * TODO:KR最新更新日時以外のテストも記載
     */
    function testCreate()
    {
        $fileIds = $this->prepareUploadImages();
        // アクション登録
        $saveAction = [
            "goal_id" => 1,
            "team_id" => 1,
            "user_id" => 1,
            "name" => "ああああ\nいいいいいいい",
            "key_result_id" => 1,
            "key_result_current_value" => 10,
        ];
        $oldKr = $this->KeyResult->getById(1);
        $newActionId = $this->ActionService->create($saveAction, $fileIds, null);
        $updatedKr = $this->KeyResult->getById(1);
        $this->assertTrue($updatedKr['latest_actioned'] > $oldKr['latest_actioned']);
    }
}
