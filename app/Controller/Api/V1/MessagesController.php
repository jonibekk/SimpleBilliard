<?php
App::uses('ApiController', 'Controller/Api');
App::uses('TopicMember', 'Model');

/**
 * Class MessagesController
 */
class MessagesController extends ApiController
{
    /**
     * Send a message
     * url: POST /api/v1/messages
     *
     * @data integer $topic_id required
     * @data string $body optional
     * @data array $file_ids optional
     * @return CakeResponse
     * @link https://confluence.goalous.com/display/GOAL/%5BPOST%5D+Send+message
     *       TODO: This is mock! We have to implement it!
     */
    function post()
    {
        $topicId = $this->request->data('topic_id');
        $body = $this->request->data('body');
        $fileIds = $this->request->data('file_ids');
        $dataMock = [
            'id'              => 123,
            'body'            => 'あついなー。',
            'created'         => 1438585548,
            'display_created' => '03/09 13:51',
            'type'            => 1,
            'user'            => [
                'id'               => 2,
                'img_url'          => '/img/no-image.jpg',
                'display_username' => '佐伯 翔平',
            ],
            'attached_files'  => [
                [
                    'id'            => 1,
                    'ext'           => 'jpg',
                    'type'          => 1,
                    'download_url'  => '/img/no-image.jpg',
                    'preview_url'   => '',
                    'thumbnail_url' => '/img/no-image.jpg',
                ],
            ]
        ];
        return $this->_getResponseSuccess($dataMock);
    }

    /**
     * Send a Like message
     * url: POST /api/v1/messages/like
     *
     * @data integer $topic_id required
     * @return CakeResponse
     * @link https://confluence.goalous.com/display/GOAL/%5BPOST%5D+Send+like+message
     *       TODO: This is mock! We have to implement it!
     */
    function post_like()
    {
        $topicId = $this->request->data('topic_id');

        /** @var TopicMember $TopicMember */
        $TopicMember = ClassRegistry::init('TopicMember');
        if (!$TopicMember->isMember($topicId, $this->Auth->user('id'))) {
            return $this->_getResponseBadFail(__("You cannot access the topic"));
        }

        //thumbs up character is bellow
        $body = "\xF0\x9F\x91\x8D";

        $dataMock = ['message_id' => 1234];
        return $this->_getResponseSuccessSimple($dataMock);
    }

}
