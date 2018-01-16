<?php
App::uses('ApiController', 'Controller/Api');
App::uses('VideoStream', 'Model');
App::uses('Video', 'Model');
App::uses('TranscodeOutputVersionDefinition', 'Model/Video/Transcode');
App::uses('VideoStorageClient', 'Model/Video');

use Goalous\Model\Enum as Enum;

/**
 * @property VideoStream $VideoStream
 * @property Video $Video
 */
class VideoStreamsController extends ApiController
{
    public $uses = [
        'VideoStream',
        'Video',
    ];

    function get_source($id)
    {
        $type = $this->request->query('type');
        if (empty($type)) {
            return $this->_getResponseBadFail('bad request');
        }
        $videoStream = $this->VideoStream->findById($id);
        if (empty($videoStream)) {
            return $this->_getResponseNotFound();
        }
        $videoStream = reset($videoStream);
        $videoStoragePath = $videoStream['storage_path'];
        $outputVersion = new Enum\Video\TranscodeOutputVersion(intval($videoStream['output_version']));
        $transcodeOutputVersionDefinition = TranscodeOutputVersionDefinition::getVersion($outputVersion);
        foreach ($transcodeOutputVersionDefinition->getVideoSources($videoStoragePath) as $videoSource) {
            if ($videoSource->getType()->getValue() === $type) {
                $preSignedUrl = VideoStorageClient::createPreSignedUriFromTranscoded(
                    $videoSource->getSource(),
                    GoalousDateTime::now()->addHour(1)
                    );
                $this->redirect($preSignedUrl);
                return;
            }
        }
        return $this->_getResponseBadFail('bad request');
    }
}
