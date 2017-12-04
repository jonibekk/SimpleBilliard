<?php
App::uses('ApiController', 'Controller/Api');
App::uses('TranscodeNotificationAwsSns', 'Model/Video/Stream');
App::uses('VideoStream', 'Model');
App::uses('PostResource', 'Model');
App::uses('PostShareCircle', 'Model');
App::uses('PostDraft', 'Model');
App::uses('Post', 'Model');
App::uses('PostResourceService', 'Service');
App::uses('VideoStreamService', 'Service');

use Goalous\Model\Enum as Enum;

class SnsNotificationController extends ApiController
{
    public function beforeFilter()
    {
        // parent::beforeFilter();
        $this->Auth->allow();
        $this->Security->validatePost = false;
        $this->Security->csrfCheck = false;
    }

    public function callback_notify()
    {
        $jsonBody = $this->request->input();

        $transcodeNotificationAwsSns = TranscodeNotificationAwsSns::parseJsonString($jsonBody);

        // $videoId = $transcodeNotificationAwsSns->getMetaData('videos.id'); // currently not using videos.id
        $videoStreamId = $transcodeNotificationAwsSns->getMetaData('video_streams.id');
        if (is_null($videoStreamId)) {
            return $this->_getResponseNotFound("video_streams.id not found");
        }

        /** @var VideoStream $VideoStream */
        $VideoStream = ClassRegistry::init('VideoStream');
        $videoStream = $VideoStream->getById($videoStreamId);
        if (empty($videoStream)) {
            return $this->_getResponseNotFound("video_streams.id({$videoStreamId}) not found");
        }

        $VideoStreamService = ClassRegistry::init('VideoStreamService');
        $VideoStreamService->updateFromTranscodeProgressData($videoStream, $transcodeNotificationAwsSns);

        return $this->_getResponseSuccess([]);
    }

    private function getRequestHeaders(): Generator
    {
        foreach ($_SERVER as $k => $v) {
            if (0 === strpos($k, 'HTTP_')) {
                yield $k => $v;
            }
        }
    }
}