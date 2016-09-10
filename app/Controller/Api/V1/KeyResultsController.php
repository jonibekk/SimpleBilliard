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

    function post_validate()
    {
        return $this->_getResponseDefaultValidation($this->KeyResult);
    }

}
