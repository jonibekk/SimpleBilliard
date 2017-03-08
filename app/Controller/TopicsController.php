<?php
App::uses('AppController', 'Controller');

/**
 * Topics Controller
 *
 */
class TopicsController extends AppController
{
    /**
     * index action
     *
     * @return void
     */
    public function index()
    {
        return $this->render("/Messages/index");
    }

}
