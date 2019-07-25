<?php
App::uses('ApiController', 'Controller/Api');
App::uses('TimeExHelper', 'View/Helper');
App::uses('UploadHelper', 'View/Helper');
App::import('Service', 'GoalService');
App::import('Service', 'ActionService');
App::import('Service', 'AttachedFileService');
App::uses('TeamTranslationStatus', 'Model');
App::uses('TeamMember', 'Model');
App::import('Controller/Traits/Notification', 'TranslationNotificationTrait');

/**
 * Class ActionsController
 */
class ActionsController extends ApiController
{
    use TranslationNotificationTrait;

    public $components = [
        'Notification',
    ];

    /**
     * 作成
     */
    public function post()
    {
        /** @var ActionService $ActionService */
        $ActionService = ClassRegistry::init("ActionService");

        // ゴールID取得
        $goalId = Hash::get($this->request->data, 'ActionResult.goal_id');
        $data = $this->request->data ?? [];
        $errRes = $this->_validateCreateAction($data);
        if ($errRes !== true) {
            return $errRes;
        }

        $fileIds = $this->request->data('file_id');
        $share = Hash::get($this->request->data, 'ActionResult.share');
        $action = [
            'goal_id'                  => $goalId,
            'team_id'                  => $this->current_team_id,
            'user_id'                  => $this->Auth->user('id'),
            'name'                     => Hash::get($this->request->data, 'ActionResult.name'),
            'key_result_id'            => Hash::get($this->request->data, 'ActionResult.key_result_id'),
            'key_result_current_value' => Hash::get($this->request->data, 'ActionResult.key_result_current_value'),

        ];

        // アクション登録
        $newActionId = $ActionService->create($action, $fileIds, $share);

        if (empty($newActionId)) {
            return $this->_getResponseInternalServerError();
        }

        //セットアップガイドステータスの更新
        $this->_updateSetupStatusIfNotCompleted();

        // pusherに通知
        $socketId = Hash::get($this->request->data, 'socket_id');
        $channelName = "goal_" . $goalId;
        $this->NotifyBiz->push($socketId, $channelName);

        $krId = $this->request->data['ActionResult']['key_result_id'] ?? null;
        $this->Mixpanel->trackGoal(MixpanelComponent::TRACK_CREATE_ACTION, $goalId, $krId,
            $this->Goal->ActionResult->getLastInsertID());
        $this->NotifyBiz->execSendNotify(NotifySetting::TYPE_FEED_CAN_SEE_ACTION,
            $this->Goal->ActionResult->getLastInsertID());

        // Send translation usage notification if applicable
        /** @var TeamTranslationLanguage $TeamTranslationLanguage */
        $TeamTranslationLanguage = ClassRegistry::init('TeamTranslationLanguage');

        if ($TeamTranslationLanguage->canTranslate($this->current_team_id)) {
            $this->sendTranslationUsageNotification($this->current_team_id);
        }

        // TODO:削除 APIはステートレスであるべき
        $this->Notification->outSuccess(__("Added an action."));

        return $this->_getResponseSuccess(['id' => $newActionId]);
    }

    /**
     * アクション登録のバリデーション
     *
     * @param array $data
     *
     * @return CakeResponse | true
     */
    private function _validateCreateAction(array $data)
    {
        /** @var GoalService $GoalService */
        $GoalService = ClassRegistry::init("GoalService");
        /** @var KeyResultService $KeyResultService */
        $KeyResultService = ClassRegistry::init("KeyResultService");
        /** @var AttachedFileService $AttachedFileService */
        $AttachedFileService = ClassRegistry::init("AttachedFileService");

        // ゴール存在チェック
        $goalId = Hash::get($data, 'ActionResult.goal_id');
        $goal = $GoalService->get($goalId);
        if (empty($goal)) {
            return $this->_getResponseBadFail(__("Not exist"));
        }

        // 画像アップロードチェック
        $fileIds = $this->request->data('file_id');
        if (empty($fileIds) || !is_array($fileIds)) {
            return $this->_getResponseBadFail(__("Please reselect an image."));
        }
        $file = $this->GlRedis->getPreUploadedFile($this->current_team_id, $this->my_uid, reset($fileIds));
        //バリデーションの為に一時的に保存する
        file_put_contents($file['info']['tmp_name'], $file['content']);
        $imgValidateRes = $AttachedFileService->validateImgType($file['info']);
        //一時保存したファイルを削除
        unlink($file['info']['tmp_name']);

        if ($imgValidateRes['error']) {
            return $this->_getResponseBadFail($imgValidateRes['msg']);
        }

        // アクションのフォームバリデーション
        $action = Hash::get($data, 'ActionResult');
        $this->Goal->ActionResult->validate = $this->Goal->ActionResult->postValidate;
        $this->Goal->ActionResult->set($action);
        if (!$this->Goal->ActionResult->validates()) {
            $errMsgs = [];
            foreach ($this->Goal->ActionResult->validationErrors as $field => $errors) {
                $errMsgs[$field] = array_shift($errors);
            }

            return $this->_getResponseValidationFail($errMsgs);
        }

        // KR進捗が既に他のユーザーによって更新されていないか
        $krBeforeValue = Hash::get($data, "kr_before_value");
        $krId = Hash::get($data, 'ActionResult.key_result_id');
        $kr = $KeyResultService->get($krId);
        if ($krBeforeValue != Security::hash(Hash::get($kr, 'current_value'))) {
            return $this->_getResponseConflict("KR progress has been updated by another user. Please try again.");
        }

        // ゴールメンバーか
        if (!$this->Goal->GoalMember->isCollaborated($goalId)) {
            $this->log(sprintf("[%s]Not goal member. goal_id:%s", __METHOD__, $goalId), LOG_INFO);
            return $this->_getResponseForbidden();
        }

        return true;
    }
}
