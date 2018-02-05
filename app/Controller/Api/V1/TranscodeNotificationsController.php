<?php
App::uses('ApiController', 'Controller/Api');
App::uses('TranscodeNotificationAwsSns', 'Model/Video/Stream');
App::uses('Video', 'Model');
App::uses('VideoStream', 'Model');
App::uses('PostResource', 'Model');
App::uses('PostShareCircle', 'Model');
App::uses('PostDraft', 'Model');
App::uses('Post', 'Model');
App::import('Service', 'PostService');
App::import('Service', 'PostResourceService');
App::import('Service', 'VideoStreamService');

use Goalous\Model\Enum as Enum;

/**
 * Class SnsNotificationController
 *
 * @property NotifyBizComponent NotifyBiz
 */
class TranscodeNotificationsController extends ApiController
{
    public $components = [
        'NotifyBiz'
    ];

    public function beforeFilter()
    {
        // This api is called from external transcoder service.
        // (e.g. AWS ETS -> AWS SNS -> Goalous)
        // Must not do authentication process for Goalous user.
        $this->Auth->allow();
        $this->Security->validatePost = false;
        $this->Security->csrfCheck = false;
    }

    /**
     * If posted json from Aws SNS containing SubscribeURL key,
     * Accessing to that URL for registering endpoint
     *
     * @see https://docs.aws.amazon.com/sns/latest/dg/json-formats.html#http-subscription-confirmation-json
     * @see https://docs.aws.amazon.com/ja_jp/sns/latest/dg/json-formats.html#http-subscription-confirmation-json (same document in japanese)
     * "SubscribeURL" key exists only on first time
     *
     * @param array $jsonData
     *
     * @return bool
     */
    private function subscribeAwsSns(array $jsonData)
    {
        $subscribeUrl = $jsonData['SubscribeURL'];
        GoalousLog::info('SubscribeURL exists, accessing to SubscribeURL', [
            'SubscribeURL' => $subscribeUrl,
        ]);
        $client = new GuzzleHttp\Client();
        try {
            $res = $client->request('GET', $subscribeUrl, [
                'allow_redirects' => true,
                'http_errors'     => false,
            ]);
        } catch (Exception $e) {
            GoalousLog::error('failed to access SubscribeURL', [
                'message' => $e->getMessage(),
            ]);
            return false;
        }
        GoalousLog::info('Accessed to AWS SNS SubscribeURL', [
            'Status' => $res->getStatusCode(),
            'Body'   => (string)$res->getBody(),
        ]);
        return true;
    }

    /**
     * Callback notification API for transcode notification
     * /api/v1/transcode_notifications/callback
     *
     * Be careful to change this API URL
     * URL is registered in AWS SNS service
     *
     * @see https://docs.aws.amazon.com/sns/latest/dg/SendMessageToHttp.html#SendMessageToHttp.subscribe
     * @see https://docs.aws.amazon.com/ja_jp/sns/latest/dg/SendMessageToHttp.html#SendMessageToHttp.subscribe (same in ja-jp)
     *
     * @return CakeResponse
     */
    public function post_callback()
    {
        $jsonBody = $this->request->input();

        try {
            $jsonData = json_decode($jsonBody, true);
            if (is_null($jsonData)) {
                throw new InvalidArgumentException('invalid json posted');
            }

            // If decoded json has 'SubscribeURL' key
            // Try for sending registration access
            if (isset($jsonData['SubscribeURL'])) {
                if ($this->subscribeAwsSns($jsonData)) {
                    return $this->_getResponseSuccess();
                }
            }

            $transcodeNotificationAwsSns = TranscodeNotificationAwsSns::createFromArray($jsonData);

            // Also we can get videos.id from meta-data
            // But currently not using, so just left in comment
            // $videoId = $transcodeNotificationAwsSns->getMetaData('videos.id');

            $videoStreamId = $transcodeNotificationAwsSns->getMetaData('video_streams.id');
            if (is_null($videoStreamId)) {
                GoalousLog::error('transcode callback error', [
                    'message' => 'video_streams.id not found',
                ]);
                return $this->_getResponseBadFail('');
            }
            if (!AppUtil::isInt($videoStreamId)) {
                GoalousLog::error('transcode callback error', [
                    'message' => 'video_streams.id is not numeric',
                    'video_streams.id' => $videoStreamId,
                ]);
                return $this->_getResponseBadFail('');
            }

            /** @var VideoStream $VideoStream */
            $VideoStream = ClassRegistry::init('VideoStream');
            $videoStream = $VideoStream->getById($videoStreamId);
            if (empty($videoStream)) {
                GoalousLog::error('transcode callback error', [
                    'message' => 'video_streams.id not found',
                    'video_streams.id' => $videoStreamId,
                ]);
                return $this->_getResponseNotFound();
            }

            GoalousLog::info("transcode progress notified", [
                'video_streams.id' => $videoStream['id'],
                'state' => $transcodeNotificationAwsSns->getProgressState()->getKey(),
            ]);

            /** @var VideoStreamService $VideoStreamService */
            $VideoStreamService = ClassRegistry::init('VideoStreamService');
            $videoStream = $VideoStreamService->updateFromTranscodeProgressData($videoStream, $transcodeNotificationAwsSns);

            $updatedVideoStreamProgress = new Enum\Video\VideoTranscodeStatus(intval($videoStream['transcode_status']));

            // If transcode notification is COMPLETED notify
            // Video resource related to draft post is prepared for video post
            /** @var PostDraft $PostDraft */
            $PostDraft = ClassRegistry::init('PostDraft');
            if ($updatedVideoStreamProgress->equals(Enum\Video\VideoTranscodeStatus::TRANSCODE_COMPLETE())) {
                // if we received COMPLETE notify, post related draft post
                // TODO: 現在は紐付いている動画が変換完了 = 下書きからの投稿OK と見なしているので修正が必要
                // Serviceに $PostDraftService->isPreparedToPost($draftPost); という判断するメソッドを追加するべき
                $postDrafts = $PostDraft->getByResourceTypeAndResourceId(Enum\Post\PostResourceType::VIDEO_STREAM(), $videoStreamId);
                /** @var PostService $PostService */
                $PostService = ClassRegistry::init('PostService');
                foreach ($postDrafts as $postDraft) {
                    // This API is called by external service, we do not have $this->current_team_id on this session
                    $this->current_team_id = $postDraft['team_id'];
                    // TODO: related to TODO comment above
                    // if ($PostDraftService->isPreparedToPost($draftPost);) {
                    //     ...
                    // }
                    $post = $PostService->addNormalFromPostDraft($postDraft);
                    if (false === $post) {
                        // failed post from draft post
                        GoalousLog::error('Failed posting from draft post', [
                            'post_drafts.id' => $postDraft['id'],
                        ]);
                    } else {
                        // succeed
                        $this->notifyTranscodeCompleteAndDraftPublished($post['id'], $post['user_id'], $post['team_id']);
                    }
                }
            } else if ($updatedVideoStreamProgress->equals(Enum\Video\VideoTranscodeStatus::ERROR())) {
                /** @var Video $Video */
                $Video = ClassRegistry::init('Video');
                $video = $Video->getById($videoStream['video_id']);
                $this->notifyTranscodeFailed($video['user_id'], $video['team_id']);
            }
            return $this->_getResponseSuccess();
        } catch (InvalidArgumentException $e) {
            GoalousLog::error('caught error on transcode SNS notification', [
                'message' => $e->getMessage(),
            ]);
            GoalousLog::error($e->getTraceAsString());
            return $this->_getResponseBadFail('');
        } catch (Exception $e) {
            GoalousLog::error('caught error on transcode SNS notification', [
                'message' => $e->getMessage(),
            ]);
            GoalousLog::error($e->getTraceAsString());
            return $this->_getResponseBadFail('');
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