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
     *
     * @return array
     */
    public function preUploadFile(array $postData): array
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

        $resValidation = $this->preUploadValidation($fileInfo);

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
     *
     * @return array
     */
    function preUploadValidation(array $fileInfo): array
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

        //ファイルの画素数チェック(画像の場合のみ)
        if (strpos($fileInfo['type'], 'image') !== false) {
            list($imgWidth, $imgHeight) = getimagesize($fileInfo['tmp_name']);
            if ($imgWidth * $imgHeight > AttachedFile::ATTACHABLE_MAX_PIXEL) {
                $ret['msg'] = __("%s pixel is the limit.", AttachedFile::ATTACHABLE_MAX_PIXEL);
                return $ret;
            }
        }

        $ret['error'] = false;
        return $ret;
    }

}
