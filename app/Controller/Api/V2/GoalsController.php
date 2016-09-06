<?php
/**
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 9/6/16
 * Time: 16:38
 */
App::uses('ApiController', 'Controller/Api');

/** @noinspection PhpUndefinedClassInspection */
class GoalsController extends ApiController
{
    function index()
    {
        return json_encode(['version' => '2']);
    }

}
