<?php
App::uses('ApiController', 'Controller/Api');

/**
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 9/6/16
 * Time: 16:38
 *
 * @property KeyResult $KeyResult
 */
class KeyResultsController extends ApiController
{
    public $uses = [
        'KeyResult'
    ];

    /**
     * KRのバリデーションAPI
     * 成功(Status Code:200)、失敗(Status Code:400)
     *
     * @return CakeResponse
     */
    function post_validate()
    {
        $validation = $this->KeyResult->validateKrPOST($this->request->data);
        if ($validation === true) {
            return $this->_getResponseSuccess();
        }
        $validationMsg = $this->_validationExtract($validation);
        return $this->_getResponseValidationFail($validationMsg);
    }

}
