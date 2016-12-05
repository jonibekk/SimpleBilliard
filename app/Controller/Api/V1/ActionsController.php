<?php
App::uses('ApiController', 'Controller/Api');
App::uses('TimeExHelper', 'View/Helper');
App::uses('UploadHelper', 'View/Helper');
App::import('Service', 'GoalService');
App::import('Service', 'ActionService');

/**
 * Class ActionsController
 */
class ActionsController extends ApiController
{

    public $components = [
        'Pnotify',
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
        if (!empty($errRes)) {
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
        $this->updateSetupStatusIfNotCompleted();

        // pusherに通知
        $socketId = Hash::get($this->request->data, 'socket_id');
        $channelName = "goal_" . $goalId;
        $this->NotifyBiz->push($socketId, $channelName);

        $krId = isset($this->request->data['ActionResult']['key_result_id']) ? $this->request->data['ActionResult']['key_result_id'] : null;
        $this->Mixpanel->trackGoal(MixpanelComponent::TRACK_CREATE_ACTION, $goalId, $krId,
            $this->Goal->ActionResult->getLastInsertID());
        $this->NotifyBiz->execSendNotify(NotifySetting::TYPE_FEED_CAN_SEE_ACTION,
            $this->Goal->ActionResult->getLastInsertID());

        // TODO:削除 APIはステートレスであるべき
        $this->Pnotify->outSuccess(__("Added an action."));

        return $this->_getResponseSuccess(['id' => $newActionId]);
    }

    /**
     * アクション登録のバリデーション
     *
     * @param array $data
     *
     * @return CakeResponse | null
     */
    private function _validateCreateAction(array $data)
    {
        /** @var GoalService $GoalService */
        $GoalService = ClassRegistry::init("GoalService");
        /** @var KeyResultService $KeyResultService */
        $KeyResultService = ClassRegistry::init("KeyResultService");

        // ゴール存在チェック
        $goalId = Hash::get($data, 'ActionResult.goal_id');
        $goal = $GoalService->get($goalId);
        if (empty($goal)) {
            $this->log(sprintf("[%s]Not exist goal. goal_id:%s", __METHOD__, $goalId), LOG_INFO);
            return $this->_getResponseBadFail(__("Not exist"));
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
        $this->log(sprintf("[%s] request:%s db:%s", __METHOD__, $krBeforeValue,
            Security::hash(Hash::get($kr, 'current_value'))));
        if ($krBeforeValue != Security::hash(Hash::get($kr, 'current_value'))) {
            return $this->_getResponseConflict("KR progress has been updated by another user. Please try again.");
        }

        // ゴールメンバーか
        if (!$this->Goal->GoalMember->isCollaborated($goalId)) {
            $this->log(sprintf("[%s]Not goal member. goal_id:%s", __METHOD__, $goalId), LOG_INFO);
            return $this->_getResponseForbidden();
        }

        return null;
    }
}
