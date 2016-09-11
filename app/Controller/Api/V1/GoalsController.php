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
         * validation
         */
        $goal_required_fields = [
            'name'             => null,
            'goal_category_id' => null,
            'term_type'        => null
        ];
        $data = am($goal_required_fields, $this->request->data);
        $this->Goal->set($data);
        if (!$this->Goal->validates()) {
            return $this->_getResponseBadFail(__('Saving Data Failed!'),
                $this->_validationExtract($this->Goal->validationErrors));
        }

        /**
         * 登録処理
         */

        /**
         * 通知
         */

        return $this->_getResponseSuccess(['goal_id' => 999]);
    }

}
