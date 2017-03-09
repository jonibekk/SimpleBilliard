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
        return $this->render("index");
    }

    /**
     * Topic detail action
     *
     * @return void
     */
     public function detail()
     {
         $topicId = $this->request->params['topic_id'];
         return $this->render("index");
     }
}
