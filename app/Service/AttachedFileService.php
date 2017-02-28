<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 2016/12/20
 * Time: 10:10
 */

App::import('Service', 'AppService');
App::uses('AttachedFile', 'Model');
App::uses('GlRedis', 'Model');

/**
 * Class AttachedFileService
 */
class AttachedFileService extends AppService
{
    // アップロード種別
    const UPLOAD_ALL = 1;
    const UPLOAD_IMG = 2;

    // アップロード可能な画像種類
    public $supportedImgTypes = [
        IMAGETYPE_PNG,
        IMAGETYPE_GIF,
        IMAGETYPE_JPEG,
        IMAGETYPE_JPEG2000,
    ];

    /**
     * Redisに添付ファイルの仮アップロード
     * 返り値のフォーマット
     * success:
     * [
     * 'error' => false,
     * 'msg'   => "",
     * 'id'    => "hash_value",
     * ];
     * error:
     * [
     * 'error' => true,
     * 'msg'   => "something is wrong..",
     * 'id'    => "",
     * ];
     *
     * @param array $postData
     * @param int   $type
     *
     * @return array
     */
    public function preUploadFile(array $postData, int $type = self::UPLOAD_ALL): array
    {
        $ret = [
            'error' => false,
            'msg'   => "",
            'id'    => "",
        ];
        $fileInfo = Hash::get($postData, 'file');

        if ($fileInfo === null) {
            $this->log(sprintf("[%s] file not exists.", __METHOD__));
            $this->log(sprintf("PostData: %s", var_export($postData, true)));
            $this->log(Debugger::trace());
            $ret['error'] = true;
            $ret['msg'] = __('Failed to upload.');
            return $ret;
        }

        $resValidation = $this->preUploadValidation($fileInfo, $type);
        //本アップロード時も判定できるように。
        $fileInfo['img_type'] = exif_imagetype($fileInfo['tmp_name']);

        if ($resValidation['error']) {
            return array_merge($ret, $resValidation);
        }

        /** @var AttachedFile $AttachedFile */
        $AttachedFile = ClassRegistry::init('AttachedFile');
        /** @var GlRedis $Redis */
        $Redis = ClassRegistry::init('GlRedis');
        $ret['id'] = $Redis->savePreUploadFile($fileInfo, $AttachedFile->current_team_id, $AttachedFile->my_uid);

        return $ret;
    }

    /**
     * 画像のプレアップロードのバリデーション
     * - 不正データチェック(ログ採取)
     * - ファイル上限チェック
     * - ファイルの画素数チェック(画像の場合のみ)
     *
     * @param array $fileInfo
     * @param int   $type
     *
     * @return array
     */
    function preUploadValidation(array $fileInfo, int $type = self::UPLOAD_ALL): array
    {
        //default return values
        $ret = [
            'error' => true,
            'msg'   => "",
        ];

        //不正データ
        if (empty($fileInfo)) {
            $this->log(sprintf("[%s] file is empty", __METHOD__));
            $this->log(Debugger::trace());
            $ret['msg'] = __('Failed to upload.');
            return $ret;
        }

        //ファイル上限チェック
        if ($fileInfo['size'] > AttachedFile::ATTACHABLE_MAX_FILE_SIZE_MB * 1024 * 1024) {
            $ret['msg'] = __("%sMB is the limit.", AttachedFile::ATTACHABLE_MAX_FILE_SIZE_MB);
            return $ret;
        }

        // 画像バリデーション
        if ($this->shouldValidateImg($fileInfo, $type)) {
            //ファイルの画素数チェック(画像の場合のみ)
            list($imgWidth, $imgHeight) = getimagesize($fileInfo['tmp_name']);
            if ($imgWidth * $imgHeight > AttachedFile::ATTACHABLE_MAX_PIXEL) {
                $ret['msg'] = __("%s pixel is the limit.", number_format(AttachedFile::ATTACHABLE_MAX_PIXEL));
                return $ret;
            }
            //ファイルタイプのチェック(gif, jpeg, pngのみ許可)
            $ret = $this->validateImgType($fileInfo);
            if ($ret['error']) {
                return $ret;
            }
        }

        $ret['error'] = false;
        return $ret;
    }

    /**
     * 画像バリデーションを行うかどうか判定
     *
     * @param array $fileInfo
     * @param int   $type
     *
     * @return array|bool
     */
    function shouldValidateImg(array $fileInfo, int $type): bool
    {
        // 画像アップロードの場合
        if ($type == self::UPLOAD_IMG) {
            return true;
        }

        /* 添付ファイルアップロードの場合、許可された画像であればバリデーションを行う */
        $targetImgType = exif_imagetype($fileInfo['tmp_name']);
        if ($targetImgType === false) {
            return false;
        }

        // MIMEタイプチェック(JPG,GIF,PNGのみ許可)
        if (!isset($this->supportedImgTypes[$targetImgType])) {
            return false;
        }

        return true;
    }

    /**
     * @param array $fileInfo
     *
     * @return array
     */
    function validateImgType(array $fileInfo): array
    {
        $ret = [
            'error' => false,
            'msg'   => "",
        ];
        /** @var AttachedFile $AttachedFile */
        $AttachedFile = ClassRegistry::init('AttachedFile');
        //一時的にデータをセット
        $AttachedFile->set(['attached' => $fileInfo]);
        if ($AttachedFile->validates()) {
            return $ret;
        }
        $ret['error'] = true;
        $ret['msg'] = $AttachedFile->validationErrors['attached'][0];
        return $ret;
    }

}
