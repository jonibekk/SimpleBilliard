<?php
App::uses('ApiController', 'Controller/Api');
App::uses('TranscodeNotificationAwsSns', 'Model/Video/Stream');
App::uses('Video', 'Model');
App::uses('VideoStream', 'Model');
App::uses('PostResource', 'Model');
App::uses('PostShareCircle', 'Model');
App::uses('PostDraft', 'Model');
App::uses('Post', 'Model');
App::uses('User', 'Model');
App::import('Service', 'PostService');
App::import('Service', 'PostResourceService');
App::import('Service', 'PostDraftService');
App::import('Service', 'VideoStreamService');
App::import('Service', 'PostResourceService');

use Goalous\Enum as Enum;

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

        // For investigating post body
        // comment in if need to see post body
        // GoalousLog::info('Post body from AWS SNS', ['body' => $jsonBody,]);

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

            $envVideoUploaded = $transcodeNotificationAwsSns->getMetaData('env');
            if (!is_null($envVideoUploaded) && ENV_NAME !== $envVideoUploaded) {
                GoalousLog::error('env does not match', [
                    'envVideoUploaded' => $envVideoUploaded,
                    'video_streams.id' => $videoStreamId,
                ]);
                return $this->_getResponseNotFound();
            }
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

            /** @var PostDraftService $PostDraftService */
            $PostDraftService = ClassRegistry::init('PostDraftService');
            /** @var VideoStreamService $VideoStreamService */
            $VideoStreamService = ClassRegistry::init('VideoStreamService');
            $videoStream = $VideoStreamService->updateFromTranscodeProgressData($videoStream, $transcodeNotificationAwsSns);

            $updatedVideoStreamProgress = new Enum\Model\Video\VideoTranscodeStatus(intval($videoStream['transcode_status']));

            // If transcode notification is COMPLETED notify
            // Video resource related to draft post is prepared for video post
            /** @var PostDraft $PostDraft */
            $PostDraft = ClassRegistry::init('PostDraft');

            if (// If transcode notification is COMPLETED notify
                $transcodeNotificationAwsSns->getProgressState()->equals(Enum\Model\Video\VideoTranscodeProgress::COMPLETE())
                // and if video_streams.transcode_status = TRANSCODE_COMPLETE then, draft is ready to post
                && $updatedVideoStreamProgress->equals(Enum\Model\Video\VideoTranscodeStatus::TRANSCODE_COMPLETE())) {
                GoalousLog::info('transcode is completed and try to post from draft', [
                    'video_streams.id'    => $videoStreamId,
                    'video_stream_state'  => $updatedVideoStreamProgress->getValue(),
                    'progress_state'      => $transcodeNotificationAwsSns->getProgressState()->getValue(),
                ]);

                // if we received COMPLETE notify, post related draft post if its ready
                $postDrafts = $PostDraft->getByResourceTypeAndResourceId(Enum\Model\Post\PostResourceType::VIDEO_STREAM(), $videoStreamId);
                /** @var PostService $PostService */
                $PostService = ClassRegistry::init('PostService');
                foreach ($postDrafts as $postDraft) {
                    // This API is called by external service, we do not have $this->current_team_id on this session
                    $this->current_team_id = $postDraft['team_id'];
                    if (!$PostDraftService->isPreparedToPost($postDraft['id'])) {
                        GoalousLog::info('draft post is not prepared to post', [
                            'post_drafts.id' => $postDraft['id'],
                        ]);
                        continue;
                    }
                    if ($postDraft['data']['is_api_v2']) {
                        $post = $PostService->addCirclePost(
                            $postDraft['data'],
                            $postDraft['data']['circle_id'],
                            $postDraft['user_id'],
                            $postDraft['team_id']);

                        /** @var PostResourceService $PostResourceService */
                        $PostResourceService = ClassRegistry::init('PostResourceService');
                        $PostResourceService->updatePostIdByPostDraftId($post['id'], $postDraft['id']);
                        $PostDraft->delete($postDraft['id']);

                        // Copy data to post_files
                        // When the draft_data is created, post_resources is already saved.
                        // But post_files is not created yet.
                        $PostResourceService->copyResourceToPostFiles($post['id']);
                    } else {
                        $post = $PostService->addNormalFromPostDraft($postDraft);
                    }
                    if (false === $post) {
                        // failed post from draft post
                        GoalousLog::error('Failed posting from draft post', [
                            'post_drafts.id' => $postDraft['id'],
                        ]);
                        // Description of this case
                        // - Transcode is completed, but posting from draft post is failed
                        // - This is our internal server
                        // - This is an external API executed from other system(e.g. AWS SNS)
                        //    - So we do not need return status 500, we returning 200.
                    } else {
                        // succeed
                        $this->sendNotification($post['id'], $postDraft);
                        $this->notifyTranscodeCompleteAndDraftPublished($post['id'], $post['user_id'], $post['team_id']);
                    }
                }
            } else if ($updatedVideoStreamProgress->equals(Enum\Model\Video\VideoTranscodeStatus::ERROR())) {
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

    /**
     * Pushing for notifying new posts to circles
     *
     * @param int   $postId
     * @param array $postDraft post_drafts data array
     *
     * @return bool
     */
    public function sendNotification(int $postId, array $postDraft)
    {
        $teamId = $postDraft['team_id'];
        $userId = $postDraft['user_id'];
        $draftData = json_decode($postDraft['draft_data'], true);
        $socketId = Hash::get($draftData, 'socket_id');
        $shareTargets = explode(',', Hash::get($draftData, 'Post.share'));
        $postType = Hash::get($draftData, 'Post.type');

        $this->NotifyBiz->execSendNotify(NotifySetting::TYPE_FEED_POST, $postId, null, null, $teamId, $userId, AppUtil::fullBaseUrl(ENV_NAME));

        // At least we need socketId and share targets
        if (empty($socketId)) {
            GoalousLog::error('socketId not found on notification', [
                'posts.id'       => $postId,
                'post_drafts.id' => $postDraft['id'],
            ]);
            return false;
        }
        if (empty($shareTargets)) {
            GoalousLog::error('Post.share not found on notification', [
                'posts.id'       => $postId,
                'post_drafts.id' => $postDraft['id'],
            ]);
            return false;
        }

        if ($postType != Post::TYPE_MESSAGE) {
            $optionalPushValues = [
                'post_draft_id'  => $postDraft['id'],
                'url_post'       => sprintf('/post_permanent/%d', $postId),
            ];
            // Pushing to Pusher
            // If containing 'Team All Circle', sharing to that circle only
            if (in_array('public', $shareTargets)) {
                $this->NotifyBiz->push($socketId, 'public', $teamId, $optionalPushValues);
            } else {
                // otherwise, pushing to each circles
                foreach ($shareTargets as $shareTarget) {
                    $this->NotifyBiz->push($socketId, $shareTarget, $teamId, $optionalPushValues);
                }
            }

            // Push for updating circle list
            $this->NotifyBiz->pushUpdateCircleList($socketId, $shareTargets, $teamId);
        }
    }

    /**
     * Change Config.language to passed users.id language
     *
     * @param int $userId
     */
    private function changeToUsersLanguage(int $userId)
    {
        /** @var User $User */
        $User = ClassRegistry::init('User');
        $user = $User->getById($userId);
        Configure::write('Config.language', $user['language']);
    }

    private function notifyTranscodeCompleteAndDraftPublished(int $postId, int $userId, int $teamId)
    {
        // this is need for relative url
        // if we don't have this line, the url will be "http://localhost"
        Router::fullBaseUrl('');
        $this->changeToUsersLanguage($userId);
        $this->NotifyBiz->sendNotify(NotifySetting::TYPE_TRANSCODE_COMPLETED_AND_PUBLISHED, $postId, null, null, $userId, $teamId);
    }

    private function notifyTranscodeFailed(int $userId, int $teamId)
    {
        Router::fullBaseUrl('');
        $this->changeToUsersLanguage($userId);
        $this->NotifyBiz->sendNotify(NotifySetting::TYPE_TRANSCODE_FAILED, null, null, null, $userId, $teamId);
    }
}