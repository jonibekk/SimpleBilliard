<?php
App::uses('ApiController', 'Controller/Api');
App::uses('TranscodeNotificationAwsSns', 'Model/Video/Stream');
App::uses('VideoStream', 'Model');
App::uses('PostResource', 'Model');
App::uses('PostShareCircle', 'Model');
App::uses('PostDraft', 'Model');
App::uses('Post', 'Model');
App::uses('PostResourceService', 'Service');

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
        // TODO: fix these code.
        // current code is for just 動作確認(for test)
        $jsonBody = $this->request->input();
        $jsonData = json_decode($jsonBody, true);
        $headers = iterator_to_array($this->getRequestHeaders());
        /*
        CakeLog::info(sprintf('log video callback: %s', AppUtil::jsonOneLine([
            'headers' => $headers,
            'jsonBody' => $jsonData,
            'message' => json_decode($jsonData['Message']),
        ])));
        */
        $result = [
            'meta' => [
                'status' => '200',
                'message' => 'ok',
            ],
            'data' => [
                'header' => $headers,
                'body'   => $jsonData,
                'message' => json_decode($jsonData['Message']),
            ],
        ];

        ///** @var TranscodeNotificationAwsSns $TranscodeNotificationAwsSns */
        //$TranscodeNotificationAwsSns = ClassRegistry::init('TranscodeNotificationAwsSns');
        $transcodeNotificationAwsSns = TranscodeNotificationAwsSns::parseJsonString($jsonBody);

        $progressState = $transcodeNotificationAwsSns->getProgressState();
        // $videoId = $transcodeNotificationAwsSns->getMetaData('videos.id');// not used
        $videoStreamId = $transcodeNotificationAwsSns->getMetaData('video_streams.id');

        /** @var VideoStream $VideoStream */
        $VideoStream = ClassRegistry::init('VideoStream');
        $videoStream = $VideoStream->getById($videoStreamId);
        $currentVideoStreamStatus = new Enum\Video\VideoTranscodeStatus(intval($videoStream['status_transcode']));
        if ($progressState->equals(Enum\Video\VideoTranscodeProgress::PROGRESS())) {
            if (!$currentVideoStreamStatus->equals(Enum\Video\VideoTranscodeStatus::QUEUED())) {
                return $this->_getResponseBadFail("video_streams.id({$videoStreamId}) is not queued");
            }
            $status = Enum\Video\VideoTranscodeStatus::TRANSCODING;
            $videoStream['status_transcode'] = $status;
            $videoStream['transcode_info'] = "[]";// TODO: add ETS JobId at least
            $VideoStream->save($videoStream);
            CakeLog::info(sprintf('transcode status changed: %s', AppUtil::jsonOneLine([
                'video_streams.id' => $videoStreamId,
                'state' => $progressState->getValue(),
                'status_value' => $status,
            ])));
        } else if ($progressState->equals(Enum\Video\VideoTranscodeProgress::ERROR())) {
            $status = Enum\Video\VideoTranscodeStatus::ERROR;
            if (!$currentVideoStreamStatus->equals(Enum\Video\VideoTranscodeStatus::TRANSCODING())) {
                return $this->_getResponseBadFail("video_streams.id({$videoStreamId}) is not transcoding");
            }
            $videoStream['status_transcode'] = $status;
            $VideoStream->save($videoStream);
            CakeLog::info(sprintf('transcode status changed: %s', AppUtil::jsonOneLine([
                'video_streams.id' => $videoStreamId,
                'state' => $progressState->getValue(),
                'status_value' => $status,
            ])));
        } else if ($progressState->equals(Enum\Video\VideoTranscodeProgress::COMPLETE())) {
            $status = Enum\Video\VideoTranscodeStatus::TRANSCODE_COMPLETE;

            if (!$currentVideoStreamStatus->equals(Enum\Video\VideoTranscodeStatus::TRANSCODING())) {
                return $this->_getResponseBadFail("video_streams.id({$videoStreamId}) is not transcoding");
            }
            $videoStream['status_transcode'] = Enum\Video\VideoTranscodeStatus::TRANSCODE_COMPLETE;
            $videoStream['duration'] = $transcodeNotificationAwsSns->getDuration();
            $videoStream['aspect_ratio'] = $transcodeNotificationAwsSns->getAspectRatio();
            $videoStream['master_playlist_path'] = $transcodeNotificationAwsSns->getPlaylistPath();
            $VideoStream->save($videoStream);
            CakeLog::info(sprintf('transcode status changed: %s', AppUtil::jsonOneLine([
                'video_streams.id' => $videoStreamId,
                'state' => $progressState->getValue(),
                'status_value' => $status,
            ])));

            /** @var PostDraft $PostDraft */
            $PostDraft = ClassRegistry::init('PostDraft');
            $postDraft = $PostDraft->getFirstByResourceTypeAndResourceId(Enum\Post\PostResourceType::VIDEO_STREAM(), $videoStreamId);
            if (!empty($postDraft)) {
                /** @var Post $Post */
                $Post = ClassRegistry::init('Post');
                $Post->addNormalFromPostDraft($postDraft);
            }
        }

        return $this->_getResponseSuccess($result);
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