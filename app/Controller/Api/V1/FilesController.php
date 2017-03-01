<?php
App::uses('ApiController', 'Controller/Api');
App::uses('TimeExHelper', 'View/Helper');
App::uses('UploadHelper', 'View/Helper');
App::import('Service', 'AttachedFileService');

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
