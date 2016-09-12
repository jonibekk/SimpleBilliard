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
        $this->_validateCreateGoal($this->request->data);

        /**
         * 登録処理
         */

        /**
         * 通知
         */

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
