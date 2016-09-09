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
}
