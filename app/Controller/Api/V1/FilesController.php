<?php
App::uses('ApiController', 'Controller/Api');
App::uses('TimeExHelper', 'View/Helper');
App::uses('UploadHelper', 'View/Helper');
App::import('Service', 'AttachedFileService');
App::import('Service', 'VideoStreamService');
App::uses('TeamStatus', 'Lib/Status');

/**
 * Class FilesController
 */
class FilesController extends ApiController
{

    /**
     * ファイルアップロード
     */
    public function post_upload()
    {
        $form = Hash::get($this->request->params, 'form');
        if (empty($form)) {
            return $this->_getResponseBadFail(__('Failed to upload.'));
        }

        $isVideo = $this->isVideo($form);

        if ($isVideo && TeamStatus::getCurrentTeam()->canVideoPostTranscode()) {
            return $this->processVideoUpload($form);
        }

        // 正常にファイルが送信されたかチェック
        // 参考:https://www.softel.co.jp/blogs/tech/archives/1824
        if (Hash::get($form, 'file.error') !== UPLOAD_ERR_OK) {
            $this->log(sprintf("[%s]Failed to upload. err_code:%s", __METHOD__, Hash::get($form, 'file.error')));
            return $this->_getResponseBadFail(__('Failed to upload.'));
        }

        /** @var AttachedFileService $AttachedFileService */
        $AttachedFileService = ClassRegistry::init('AttachedFileService');
        $ret = $AttachedFileService->preUploadFile($form);
        if ($ret['error']) {
            return $this->_getResponseBadFail($ret['msg']);
        }
        return $this->_getResponseSuccess($ret);
    }

    /**
     * 画像アップロード
     */
    public function post_upload_image()
    {
        $form = Hash::get($this->request->params, 'form');
        if (empty($form)) {
            return $this->_getResponseBadFail(__('Failed to upload.'));
        }

        // 正常にファイルが送信されたかチェック
        // 参考:https://www.softel.co.jp/blogs/tech/archives/1824
        if (Hash::get($form, 'file.error') !== UPLOAD_ERR_OK) {
            $this->log(sprintf("[%s]Failed to upload. err_code:%s", __METHOD__, Hash::get($form, 'file.error')));
            return $this->_getResponseBadFail(__('Failed to upload.'));
        }

        /** @var AttachedFileService $AttachedFileService */
        $AttachedFileService = ClassRegistry::init('AttachedFileService');
        $ret = $AttachedFileService->preUploadFile($form, $AttachedFileService::UPLOAD_TYPE_IMG);
        if ($ret['error']) {
            return $this->_getResponseBadFail($ret['msg']);
        }
        return $this->_getResponseSuccess($ret);
    }

    /**
     * Decide the posted file is video file or not
     *
     * @param array $requestFileUpload
     * Posted file data array from 'multipart/form-data'
     * $requestFileUpload should be the
     * value get from Hash::get($this->request->params, 'form');
     *
     * @return bool
     */
    public function isVideo(array $requestFileUpload): bool
    {
        // TODO: MUST FIX HERE
        // php uploaded ['file']['type'] is decided by just only file extension
        //     e.g. image.gif -> rename to -> image.mp4 -> upload -> ['file']['type'] is "video/mp4"
        // @see https://www.iana.org/assignments/media-types/media-types.xhtml#video
        // for approved video mime-types
        return false !== strpos($requestFileUpload['file']['type'], 'video');
    }

    /**
     * Upload single video file for transcoding
     *
     * @param array $requestFileUpload
     * Posted file data array from 'multipart/form-data'
     * $requestFileUpload should be the
     * value get from Hash::get($this->request->params, 'form');
     *
     * @return CakeResponse
     */
    public function processVideoUpload(array $requestFileUpload): CakeResponse
    {
        GoalousLog::info('file uploaded', $requestFileUpload);

        $userId = $this->Auth->user('id');
        $teamId = $this->current_team_id;
        /** @var VideoStreamService $VideoStreamService */
        $VideoStreamService = ClassRegistry::init('VideoStreamService');
        try {
            $videoStream = $VideoStreamService->uploadVideoStream($requestFileUpload['file'], $userId, $teamId);
        } catch (Exception $e) {
            GoalousLog::error('upload new video stream failed', [
                'message' => $e->getMessage(),
                'users.id' => $userId,
                'teams.id' => $teamId,
            ]);
            GoalousLog::error($e->getTraceAsString());
            return $this->_getResponseBadFail(__('Failed uploading video'));
        }
        GoalousLog::info('video uploaded stream', [
            'video_streams.id' => $videoStream['id'],
        ]);

        return $this->_getResponseSuccess([
            'error' => false,
            'msg' => '',
            'is_video' => true,
            'video_stream_id' => $videoStream['id'],
        ]);
    }
}
