<?php
App::uses('ApiController', 'Controller/Api');
/** @noinspection PhpUndefinedClassInspection */

/**
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 9/6/16
 * Time: 16:38
 *
 * @property Goal $Goal
 */
class GoalsController extends ApiController
{
    public $uses = [
        'Goal'
    ];

    function post_validate()
    {
        return $this->_getResponseDefaultValidation($this->Goal);
    }

    function post()
    {
        /**
         * Validation
         */
        if ($validateResult = $this->_validateCreateGoal($this->request->data) !== true) {
            return $validateResult;
        }
        /**
         * 登録処理
         * TODO: タグの保存処理まだやってない
         */
        $this->Goal->add(
            [
                'Goal'      => $this->request->data,
                'KeyResult' => [$this->request->data['key_result']],
            ]
        );
        /**
         * 通知
         */
        $socketId = viaIsSet($this->request->data['socket_id']);
        $this->NotifyBiz->push($socketId, "all");
        $this->_sendNotifyToCoach($this->Goal->getLastInsertID(),
            NotifySetting::TYPE_MY_MEMBER_CREATE_GOAL);
        /**
         * セットアップガイドステータスの更新
         */
        $this->updateSetupStatusIfNotCompleted();
        /**
         * コーチと自分の認定件数を更新(キャッシュを削除)
         */
        if ($coach_id = $this->User->TeamMember->getCoachUserIdByMemberUserId(
            $this->Auth->user('id'))
        ) {
            Cache::delete($this->Goal->getCacheKey(CACHE_KEY_UNAPPROVED_COUNT, true), 'user_data');
            Cache::delete($this->Goal->getCacheKey(CACHE_KEY_UNAPPROVED_COUNT, true, $coach_id), 'user_data');
        }

        /**
         * Mixpanel
         */
        $this->Mixpanel->trackGoal(MixpanelComponent::TRACK_CREATE_GOAL,
            $this->Goal->getLastInsertID());

        //TODO 遷移先の情報は渡さなくて大丈夫か？api以外の場合はリダイレクトを分岐している。

        return $this->_getResponseSuccess(['goal_id' => $this->Goal->getLastInsertID()]);
    }

    /**
     * @param array $data
     *
     * @return bool|void
     */
    function _validateCreateGoal($data)
    {
        if (!viaIsSet($data['key_result'])) {
            return $this->_getResponseBadFail(__('top Key Result is required!'));
        }
        $validation = $this->_validationExtract($this->Goal->validateGoalCreate($data));
        $kr_validation = $this->_validationExtract($this->Goal->KeyResult->validateKrCreate($data['key_result']));
        if (!empty($kr_validation)) {
            $validation['key_result'] = $kr_validation;
        }
        if (!empty($validation)) {
            return $this->_getResponseBadFail(__('Saving Data Failed!'), $validation);
        }
        return true;
    }

}
