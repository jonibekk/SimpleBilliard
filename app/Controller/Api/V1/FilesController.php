<?php
App::uses('ApiController', 'Controller/Api');
App::uses('TimeExHelper', 'View/Helper');
App::uses('UploadHelper', 'View/Helper');
App::import('Service', 'AttachedFileService');
App::import('Service', 'VideoStreamService');
App::uses('TeamStatus', 'Lib/Status');
App::uses('UploadVideoStreamRequest', 'Service/Request/VideoStream');

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
        // Get uploaded file data array
        $form = Hash::get($this->request->params, 'form');
        if (empty($form)) {
            return $this->_getResponseBadFail(__('Failed to upload.'));
        }

        // Get enable_video_transcode
        $enableVideoTranscode = $this->request->data('enable_video_transcode') ?? 0;
        $enableVideoTranscode = 0 < intval($enableVideoTranscode);

        // if $enableVideoTranscode = true from API
        if ($enableVideoTranscode) {
            $isVideo = $this->isVideo($form);
            if ($isVideo && TeamStatus::getCurrentTeam()->canVideoPostTranscode()) {
                return $this->processVideoUpload($form);
            }
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
        // Do not trust the ['file']['type'](= mime-type) value posted from browser
        // ['file']['type'] is resolved from only by file extension in several browser

        // TODO:
        // Investigating more certainty if the file is video or not.
        // We should use ffmpeg/ffprove

        // checking in mime-types in the file for more certain info
        $fileMimeType = mime_content_type($requestFileUpload['file']['tmp_name']);
        $fileMimeType = strtolower($fileMimeType);
        $allowVideoTypes = Configure::read("allow_video_types");
        return in_array($fileMimeType, $allowVideoTypes);
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
            $uploadVideoStreamRequest = new UploadVideoStreamRequest($requestFileUpload['file'], $userId, $teamId);
            $videoStream = $VideoStreamService->uploadVideoStream($uploadVideoStreamRequest);
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
