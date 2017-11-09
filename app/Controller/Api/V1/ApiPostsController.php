<?php
App::uses('ApiController', 'Controller/Api');
App::import('Service', 'PostDraftService');
App::import('Service', 'PostDraftService');

// TODO: ApiPostsController この名前をどうにかしたい
class ApiPostsController extends ApiController
{

    public $components = [
        'Notification',
    ];

    /**
     * 作成
     */
    public function post_video()
    {
        CakeLog::info(sprintf("file log: %s", AppUtil::jsonOneLine([
            'video' => $this->params['form']['video'],
        ])));

        return $this->_getResponseSuccess([
            "code" => "success",
        ]);
        // TODO: check the video duration, resolution, aspect ratio
        // TODO: create draft post
        $user = $this->Auth->user()['user'];
        $teamId = $this->current_team_id;
        $uploadFIle = new SplFileInfo($this->params['form']['video']['tmp_name']);

        /** @var PostDraftService $PostDraftService */
        $PostDraftService = ClassRegistry::init('PostDraftService');
        $postDraft = $PostDraftService->create();
        /** @var VideoService $VideoService */
        $VideoService = ClassRegistry::init('VideoService');
        // ここ、PostService->postWithVideo() にするべきかも
        $result = $VideoService->uploadToDraftPost($uploadFIle, $user, $teamId, $postDraft);
        // TODO: if succeed, create post resource

        return $this->_getResponseSuccess([
            "code" => "success",
        ]);
    }
}
