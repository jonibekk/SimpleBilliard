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
        if (!viaIsSet($this->request->data['key_result'])) {
            return $this->_getResponseBadFail(__('top Key Result is required!'));
        }
        $validation = $this->_validationExtract($this->Goal->validateGoalCreate($this->request->data));
        $kr_validation = $this->_validationExtract($this->Goal->KeyResult->validateKrCreate($this->request->data['key_result']));
        if (!empty($kr_validation)) {
            $validation['key_result'] = $kr_validation;
        }
        if (!empty($validation)) {
            return $this->_getResponseBadFail(__('Saving Data Failed!'), $validation);
        }

        /**
         * 登録処理
         */

        /**
         * 通知
         */

        return $this->_getResponseSuccess(['goal_id' => $this->Goal->getLastInsertID()]);
    }

}
