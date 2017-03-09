<?php
App::uses('ApiController', 'Controller/Api');

/**
 * Class MessagesController
 */
class MessagesController extends ApiController
{
    /**
     * Send Like message
     * url: POST /api/v1/messages
     *
     * @data integer $topic_id required
     * @data string $body optional
     * @data array $file_ids optional
     * @return CakeResponse
     */
    function post()
    {
        $topicId = $this->request->data('topic_id');
        $body = $this->request->data('body');
        $fileIds = $this->request->data('file_ids');
        $dataMock = ['message_id' => 1234];
        return $this->_getResponseSuccessSimple($dataMock);
    }

    /**
     * Send Like message
     * url: POST /api/v1/messages/like
     *
     * @data integer $topic_id required
     * @return CakeResponse
     */
    function post_like()
    {
        $topicId = $this->request->data('topic_id');
        $dataMock = ['message_id' => 1234];
        return $this->_getResponseSuccessSimple($dataMock);
    }

}
