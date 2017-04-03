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
        $topicId = $this->request->params['topic_id'];
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

    /**
     * get topic modal members in modal
     * - TODO: must move to /api/v1/topics/members
     * - it contain html rendering, and it's difficult to implement this in api controller,
     *   So was judged to place in this controller at first message release.
     *
     * @param  int $topicId
     *
     * @return CakeResponse
     */
    public function ajax_get_members(int $topicId)
    {
        $this->_ajaxPreProcess();

        /** @var TopicMember $TopicMember */
        $TopicMember = ClassRegistry::init('TopicMember');
        $userId = $this->Auth->user('id');

        // permission check
        if (!$TopicMember->isMember($topicId, $userId)) {
            // TODO: Response as 403 after moved to /api/v1/topics/members
            return $this->_ajaxGetResponse(null);
        }

        $users = $TopicMember->findMembers($topicId);
        $this->set(compact('users'));

        //エレメントの出力を変数に格納する
        //htmlレンダリング結果
        $response = $this->render('modal_message_range');
        $html = $response->__toString();
        return $this->_ajaxGetResponse($html);
    }
}
