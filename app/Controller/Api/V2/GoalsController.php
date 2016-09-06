<?php
/**
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 9/6/16
 * Time: 16:38
 */
App::uses('GoalsBaseController', 'Controller/Api');

/** @noinspection PhpUndefinedClassInspection */
class GoalsController extends GoalsBaseController
{
    function test()
    {
        return json_encode(['version' => '2']);
    }

    function index()
    {
        return json_encode(['file' => 'index']);
    }

}
