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
     * @see FYI: defined mime-types in IANA
     *      https://www.iana.org/assignments/media-types/media-types.xhtml#video
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
        return in_array($fileMimeType, [
            // this video mime-types is referred from iana.org list
            'video/1d-interleaved-parityfec',
            'video/3gpp',
            'video/3gpp2',
            'video/3gpp-tt',
            'video/BMPEG',
            'video/BT656',
            'video/CelB',
            'video/DV',
            'video/encaprtp',
            'video/example',
            'video/H261',
            'video/H263',
            'video/H263-1998',
            'video/H263-2000',
            'video/H264',
            'video/H264-RCDO',
            'video/H264-SVC',
            'video/H265',
            'video/iso.segment',
            'video/JPEG',
            'video/jpeg2000',
            'video/mj2',
            'video/MP1S',
            'video/MP2P',
            'video/MP2T',
            'video/mp4',
            'video/MP4V-ES',
            'video/MPV',
            'video/mpeg4-generic',
            'video/nv',
            'video/ogg',
            'video/pointer',
            'video/quicktime',
            'video/raptorfec',
            'video/rtp-enc-aescm128',
            'video/rtploopback',
            'video/rtx',
            'video/smpte291',
            'video/SMPTE292M',
            'video/ulpfec',
            'video/vc1',
            'video/vnd.CCTV',
            'video/vnd.dece.hd',
            'video/vnd.dece.mobile',
            'video/vnd.dece-mp4',
            'video/vnd.dece.pd',
            'video/vnd.dece.sd',
            'video/vnd.dece.video',
            'video/vnd.directv-mpeg',
            'video/vnd.directv.mpeg-tts',
            'video/vnd.dlna.mpeg-tts',
            'video/vnd.dvb.file',
            'video/vnd.fvt',
            'video/vnd.hns.video',
            'video/vnd.iptvforum.1dparityfec-1010',
            'video/vnd.iptvforum.1dparityfec-2005',
            'video/vnd.iptvforum.2dparityfec-1010',
            'video/vnd.iptvforum.2dparityfec-2005',
            'video/vnd.iptvforum.ttsavc',
            'video/vnd.iptvforum.ttsmpeg2',
            'video/vnd.motorola.video',
            'video/vnd.motorola.videop',
            'video/vnd-mpegurl',
            'video/vnd.ms-playready.media.pyv',
            'video/vnd.nokia.interleaved-multimedia',
            'video/vnd.nokia.mp4vr',
            'video/vnd.nokia.videovoip',
            'video/vnd.objectvideo',
            'video/vnd.radgamettools.bink',
            'video/vnd.radgamettools.smacker',
            'video/vnd.sealed.mpeg1',
            'video/vnd.sealed.mpeg4',
            'video/vnd.sealed-swf',
            'video/vnd.sealedmedia.softseal-mov',
            'video/vnd.uvvu-mp4',
            'video/vnd-vivo',
            'video/VP8',
        ]);
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
