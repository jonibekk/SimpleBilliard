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
App::uses('UploadHelper', 'View/Helper');

/**
 * Class AttachedFileService
 */

use Goalous\Enum\Model\AttachedFile\AttachedFileType as AttachedFileType;
use Goalous\Enum\Model\AttachedFile\AttachedModelType as AttachedModelType;

class AttachedFileService extends AppService
{
    // アップロード種別
    const UPLOAD_TYPE_ALL = 1;
    const UPLOAD_TYPE_IMG = 2;

    /** add here if there is aother Media file Extension you want to treat as DOC */
    const NON_MEDIA_EXT = [
        'psd'
    ];

    // アップロード可能な画像種類
    public $supportedImgTypes = [
        IMAGETYPE_PNG,
        IMAGETYPE_GIF,
        IMAGETYPE_JPEG,
        IMAGETYPE_JPEG2000,
    ];

    /**
     * Get single file
     *
     * @param       $id
     *
     * @return array
     */
    public function get(int $id): array
    {
        $data = $this->_getWithCache($id, 'AttachedFile');
        return $data;
    }


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
    public function preUploadFile(array $postData, int $type = self::UPLOAD_TYPE_ALL): array
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
    function preUploadValidation(array $fileInfo, int $type = self::UPLOAD_TYPE_ALL): array
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
                $ret['msg'] = __("%s pixels is the limit.", number_format(AttachedFile::ATTACHABLE_MAX_PIXEL));
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
        if ($type == self::UPLOAD_TYPE_IMG) {
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

    /**
     * Add a new attached file
     *
     * @param int               $userId
     * @param int               $teamId
     * @param UploadedFile      $file
     * @param AttachedModelType $modelType
     * @param bool              $displayFileList
     * @param bool              $removable
     *
     * @return AttachedFileEntity
     * @throws Exception
     */
    public function add(
        int $userId,
        int $teamId,
        UploadedFile $file,
        AttachedModelType $modelType,
        bool $displayFileList = true,
        bool $removable = true
    ): AttachedFileEntity
    {
        /** @var AttachedFile $AttachedFile */
        $AttachedFile = ClassRegistry::init('AttachedFile');

        $fileType = $this->getFileMimeType($file);

        $newData = [
            'user_id'               => $userId,
            'team_id'               => $teamId,
            'attached_file_name'    => $file->getFileName(),
            'file_type'             => $fileType->getValue(),
            'file_ext'              => $file->getFileExt(),
            'file_size'             => $file->getFileSize(),
            'model_type'            => $modelType->getValue(),
            'display_file_list_flg' => $displayFileList,
            'removable_flg'         => $removable,
            'created'               => GoalousDateTime::now()->getTimestamp()
        ];

        try {
            $this->TransactionManager->begin();
            $AttachedFile->create();
            $result = $AttachedFile->useType()->useEntity()->save($newData, false);
            $this->TransactionManager->commit();
        } catch (Exception $exception) {
            $this->TransactionManager->rollback();
            GoalousLog::error($errorMessage = 'Failed saving attached files', [
                'user.id'  => $userId,
                'team.id'  => $teamId,
                'filename' => $file->getFileName(),
            ]);
            throw new RuntimeException('Error on adding attached file: ' . $errorMessage);
        }

        return $result;
    }

    /**
     * get file's Mime-type
     *
     * @param UploadedFile      $file
     *
     * @return AttachedFileType
     */
    public function getFileMimeType(UploadedFile $file): AttachedFileType
    {
        if(in_array($file->getFileExt(), self::NON_MEDIA_EXT, true)){
            return AttachedFileType::TYPE_FILE_DOC();
        }
        switch ($file->getFileType()) {
            case "image" :
                return AttachedFileType::TYPE_FILE_IMG();
            case "video" :
                return AttachedFileType::TYPE_FILE_VIDEO();
            default:
                return AttachedFileType::TYPE_FILE_DOC();
        }

    }
    /**
     * check file is an image or not
     *
     * @param UploadedFile      $file
     *
     * @return bool
     */
    public function isImg(UploadedFile $file): bool
    {
        return $this->getFileMimeType($file)->getValue() === AttachedFileType::TYPE_FILE_IMG;
    }

    /**
     * Get file url
     *
     * @param int  $fileId
     * @param bool $isViewer
     *
     * @return bool|null|string
     */
    public function getFileUrl(int $fileId, bool $isViewer = false)
    {
        $file = $this->get($fileId);
        if (empty($file)) {
            return false;
        }

        $upload = new UploadHelper(new View());
        $type = $isViewer ? 'viewer' : 'download';
        return $upload->attachedFileUrl($file, $type);
    }
}
