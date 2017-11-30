<?php
App::uses('ApiController', 'Controller/Api');
App::uses('TimeExHelper', 'View/Helper');
App::uses('UploadHelper', 'View/Helper');
App::import('Service', 'AttachedFileService');
App::import('Service', 'VideoStreamService');

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

        // TODO: is this ok about deciding file type "video"
        $isVideo = false !== strpos($form['file']['type'], 'video');

        if ($isVideo) {
            // TODO: /tmp/ ファイルの削除をしないといけないかも？要確認
            CakeLog::info(sprintf('file uploaded: %s', AppUtil::jsonOneLine([
                'form' => $form,
                'isVideo' => $isVideo,
            ])));

            $user = $this->User->getById($this->Auth->user('id'));
            $teamId = $this->current_team_id;
            /** @var VideoStreamService $VideoStreamService */
            $VideoStreamService = ClassRegistry::init('VideoStreamService');
            $videoStream = $VideoStreamService->uploadNewVideoStream($form['file'], $user, $teamId);

            CakeLog::info(sprintf('new video_stream created %s', AppUtil::jsonOneLine([
                'video_streams.id' => $videoStream['id'],
            ])));
            return $this->_getResponseSuccess([
                'error' => false,
                'msg' => '',
                'is_video' => true,
                'video_stream_id' => $videoStream['id'],
            ]);
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
}
