<?php
App::uses('AppController', 'Controller');

/**
 * Topics Controller
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
     * Search action
     *
     * @return void
     */
    public function search()
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
        $topicId = $this->request->param('topic_id');
        if (!$topicId) {
            $this->Pnotify->outError(__("Invalid screen transition."));
            return $this->redirect("/");
        }

        /** @var TopicMember $TopicMember */
        $TopicMember = ClassRegistry::init('TopicMember');
        // checking permission
        $loginUserId = $this->Auth->user('id');
        if (!$TopicMember->isMember($topicId, $loginUserId)) {
            $this->Pnotify->outError(__("You cannot access the topic"));
            return $this->redirect("/");
        }

        // updating message notify count.
        $this->NotifyBiz->removeMessageNotification($topicId);
        $this->NotifyBiz->updateCountNewMessageNotification();
        $this->_setNotifyCnt();

        return $this->render("index");
    }

    /**
     * Topic detail action
     *
     * @return void
     */
    public function create()
    {
        return $this->render("index");
    }
}
