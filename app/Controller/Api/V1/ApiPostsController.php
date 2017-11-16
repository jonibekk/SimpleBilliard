<?php
App::uses('ApiController', 'Controller/Api');
App::import('Service', 'VideoService');
App::import('Service', 'PostDraftService');
App::import('Service', 'PostResourceService');
App::uses('PostDraft', 'Model');
App::uses('Circle', 'Model');
App::uses('GoalousDateTime', 'DateTime');

use Goalous\Model\Enum as Enum;

// TODO: ApiPostsController この名前をどうにかしたい
class ApiPostsController extends ApiController
{

    public $components = [
        'Notification',
    ];

    public function beforeFilter() {
        parent::beforeFilter();
        // TODO: remove these security disables
        $this->Security->validatePost = false;
        $this->Security->csrfCheck = false;
    }

    public function callback() {

        $jsonBody = $this->request->input('json_decode');
        $headers = iterator_to_array($this->getRequestHeaders());
        CakeLog::info(sprintf('log video callback: %s', AppUtil::jsonOneLine([
            'headers' => $headers,
            'jsonBody' => $jsonBody,
            'message' => json_decode($jsonBody->Message),
        ])));
        $result = [
            'meta' => [
                'status' => '200',
                'message' => 'ok',
            ],
            'data' => [
                'header' => $headers,
                'body'   => $jsonBody,
                'message' => json_decode($jsonBody->Message),
            ],
        ];
        $this->viewClass = 'Json';
        $this->set(compact('result'));
        $this->set('_serialize', 'result');

        return $this->_getResponseSuccess([
            "code" => "success",
        ]);
    }

    /**
     * make video, video_stream
     * return video_stream id
     */
    public function post_video()
    {
        try {
            CakeLog::info(sprintf("file log: %s", AppUtil::jsonOneLine([
                'video' => $this->params['form']['video'],
            ])));

            // TODO: check the video duration, resolution, aspect ratio
            // TODO: create draft post

            $user = $this->User->getById($this->Auth->user('id'));
            $teamId = $this->current_team_id;
            $uploadFile = new SplFileInfo($this->params['form']['video']['tmp_name']);

            /** @var VideoService $VideoService */
            $VideoService = ClassRegistry::init('VideoService');
            // ここ、PostService->postWithVideo() にするべきかも
            // TODO: is here ok about returning VideoStream array
            $videoStream = $VideoService->upload($uploadFile, $user, $teamId);

            // #############################################################################
            // TODO: the video post is done (for the test, assume to Post is done that user input text and press POST button)
            // making draft posts
            // ユーザーが文章を入力してPOSTを押したと仮定し、下書きpostを作成する
            /** @var PostDraftService $PostDraftService */
            $PostDraftService = ClassRegistry::init('PostDraftService');
            /** @var PostDraft $PostDraft */
            $PostDraft = ClassRegistry::init('PostDraft');
            /** @var PostResourceService $PostResourceService */
            $PostResourceService = ClassRegistry::init('PostResourceService');

            /** @var Circle $Circle */
            $Circle = ClassRegistry::init('Circle');
            $firstCircleId = array_keys($Circle->getPublicCircleList())[0];

            $postDraft = $PostDraftService->createPostDraft($user['id'], $teamId);
            $postDraft['draft_data'] = json_encode([
                'body'             => GoalousDateTime::now()->format("Y-m-d H:i:s") . PHP_EOL . 'i post the movie!'.PHP_EOL.'(動画を投稿しました!)',
                'type'             => 1,
                'goal_id'          => null,
                'circle_id'        => null,
                'action_result_id' => null,
                'key_result_id'    => null,
                'share_circle_ids'    => [$firstCircleId],
            ]);
            $PostDraft->save($postDraft);
            $postResource = $PostResourceService->createPostResource($postDraft['id'], Enum\Post\PostResourceType::VIDEO_STREAM(), $videoStream['id']);
            // #############################################################################

            return $this->_getResponseSuccess([
                "code" => "success",
                'video_stream_id' => $videoStream['id'],
            ]);
        } catch (Exception $e) {
            CakeLog::error(sprintf("error: %s", AppUtil::jsonOneLine([
                'type' => gettype($e),
                'message' => $e->getMessage(),
            ])));
            return $this->_getResponseBadFail($e->getMessage());
        }
    }
}
