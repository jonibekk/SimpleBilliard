<?php
App::uses('ApiController', 'Controller/Api');
App::uses('TranscodeNotificationAwsSns', 'Model/Video/Stream');
App::uses('Video', 'Model');
App::uses('VideoStream', 'Model');
App::uses('PostResource', 'Model');
App::uses('PostShareCircle', 'Model');
App::uses('PostDraft', 'Model');
App::uses('Post', 'Model');
App::uses('PostResourceService', 'Service');
App::uses('VideoStreamService', 'Service');

use Goalous\Model\Enum as Enum;

/**
 * Class SnsNotificationController
 *
 * @property NotifyBizComponent NotifyBiz
 *
 * TODO: this notification is especially for video transcoding(should change class name?)
 */
class SnsNotificationController extends ApiController
{
    public $components = [
        'NotifyBiz'
    ];

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

        try {
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

            GoalousLog::info("transcode progress notified", [
                'video_streams.id' => $videoStream['id'],
                'state' => $transcodeNotificationAwsSns->getProgressState()->getKey(),
            ]);

            /** @var VideoStreamService $VideoStreamService */
            $VideoStreamService = ClassRegistry::init('VideoStreamService');
            $videoStream = $VideoStreamService->updateFromTranscodeProgressData($videoStream, $transcodeNotificationAwsSns);

            $updatedVideoStreamProgress = new Enum\Video\VideoTranscodeStatus(intval($videoStream['status_transcode']));
            /** @var PostDraft $PostDraft */
            $PostDraft = ClassRegistry::init('PostDraft');
            if ($updatedVideoStreamProgress->equals(Enum\Video\VideoTranscodeStatus::TRANSCODE_COMPLETE())) {
                $postDraft = $PostDraft->getFirstByResourceTypeAndResourceId(Enum\Post\PostResourceType::VIDEO_STREAM(), $videoStreamId);
                // TODO: ここ、複数紐付いている下書きがあった場合、一つしかpostされない
                if (!empty($postDraft)) {
                    /** @var Post $Post */
                    $Post = ClassRegistry::init('Post');
                    $this->current_team_id = $postDraft['team_id'];
                    $post = $Post->addNormalFromPostDraft($postDraft);
                    $this->notifyTranscodeCompleteAndDraftPublished($post['id'], $post['user_id'], $post['team_id']);
                }
            } else if ($updatedVideoStreamProgress->equals(Enum\Video\VideoTranscodeStatus::ERROR())) {
                /** @var Video $Video */
                $Video = ClassRegistry::init('Video');
                $video = $Video->getById($videoStream['video_id']);
                $this->notifyTranscodeFailed($video['user_id'], $video['team_id']);
            }

            return $this->_getResponseSuccess([]);
        } catch (InvalidArgumentException $e) {
            GoalousLog::error('caught error on transcode SNS notification', [
                'message' => $e->getMessage(),
            ]);
            return $this->_getResponseBadFail([
                'message' => 'unexpected json format',
            ]);
        } catch (Exception $e) {
            GoalousLog::error('caught error on transcode SNS notification', [
                'message' => $e->getMessage(),
            ]);
            return $this->_getResponseBadFail([
                'message' => 'internal server error',
            ]);
        }
    }

    private function notifyTranscodeCompleteAndDraftPublished(int $postId, int $userId, int $teamId)
    {
        // this is need for relative url
        // if we don't have this line, the url will be "http://localhost"
        Router::fullBaseUrl('');
        $this->NotifyBiz->sendNotify(NotifySetting::TYPE_TRANSCODE_COMPLETED_AND_PUBLISHED, $postId, null, null, $userId, $teamId);
    }

    private function notifyTranscodeFailed(int $userId, int $teamId)
    {
        Router::fullBaseUrl('');
        $this->NotifyBiz->sendNotify(NotifySetting::TYPE_TRANSCODE_FAILED, null, null, null, $userId, $teamId);
    }
}