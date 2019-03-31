<?php
App::uses('HttpSocket', 'Network/Http');
App::import('Lib/Aws', 'AwsClientFactory');

use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;
use Guzzle\Http\EntityBody;

/**
 * This file is a part of UploadPack - a plugin that makes file uploads in CakePHP as easy as possible.
 * UploadBehavior
 * UploadBehavior does all the job of saving files to disk while saving records to database. For more info read UploadPack documentation.
 * joe bartlett's lovingly handcrafted tweaks add several resize modes. see "more on styles" in the documentation.
 *
 * @author Michał Szajbe (michal.szajbe@gmail.com) and joe bartlett (contact@jdbartlett.com)
 * @link   http://github.com/szajbus/uploadpack
 */
class UploadBehavior extends ModelBehavior
{

    private static $__settings = array();

    private $toWrite = array();

    private $toDelete = array();

    private $maxWidthSize = false;

    private $imgExtEachTypes = [
        IMAGETYPE_PNG      => ['png'],
        IMAGETYPE_GIF      => ['gif'],
        IMAGETYPE_JPEG     => ['jpg', 'jpeg'],
        IMAGETYPE_JPEG2000 => ['jpg', 'jpeg'],
    ];

    /**
     * aws s3用オブジェクト
     *
     * @var  S3Client $s3
     */
    private $s3;

    private $supportedExtensions = [
        'jpg',
        'jpeg',
        'png',
        'gif'
    ];

    public function setup(Model $model, $settings = array())
    {
        //Galaxy S7 edgeなどの一部の端末で撮影した画像が処理できない問題(以下Warning)の対応のため、warningを無視する設定
        //Warning (2): imagecreatefromjpeg(): gd-jpeg, libjpeg: recoverable error: Invalid SOS parameters for sequential JPEG
        //公式にphp7.1からデフォルト設定が"1"になっている。-> http://php.net/manual/en/image.configuration.php
        //TODO: 後にchefのレシピで対応するようにする
        ini_set('gd.jpeg_ignore_warning', 1);

        $defaults = array(
            'path'                   => ':webroot/upload/:model/:id/:basename_:style.:extension',
            'styles'                 => array(),
            'resizeToMaxWidth'       => false,
            'quality'                => 75,
            'alpha'                  => false,
            'addFieldNameOnFileName' => true,
        );
        foreach ($settings as $field => $array) {
            self::$__settings[$model->name][$field] = array_merge($defaults, $array);
        }
        $this->_setupS3();
    }

    public function beforeSave(Model $model, $options = array())
    {
        $this->_reset();
        foreach (self::$__settings[$model->name] as $field => $settings) {
            if (!empty($model->data[$model->name][$field]) && is_array($model->data[$model->name][$field]) && file_exists($model->data[$model->name][$field]['tmp_name'])) {
                // エラーが出ているか、ファイルサイズが 0 の場合
                if ((isset($model->data[$model->name][$field]['error']) && $model->data[$model->name][$field]['error']) ||
                    $model->data[$model->name][$field]['size'] == 0
                ) {
                    $log = sprintf("Error: Failed to upload file. uid=%s\n", $model->my_uid);
                    $log .= Debugger::exportVar($model->data) . "\n";
                    $log .= Debugger::trace();
                    $this->log($log);

                    // ファイルアップロードに失敗した場合は、save()自体をエラーにする
                    return false;
                }

                if (!empty($model->id)) {
                    $this->_prepareToDeleteFiles($model, $field, true);
                }
                $this->_prepareToWriteFiles($model, $field, $settings);
                unset($model->data[$model->name][$field]);
                $model->data[$model->name][$field . '_file_name'] = $this->toWrite[$field]['name'];
                $model->data[$model->name][$field . '_file_size'] = $this->toWrite[$field]['size'];
                $model->data[$model->name][$field . '_content_type'] = $this->toWrite[$field]['type'];
            } elseif (array_key_exists($field,
                    $model->data[$model->name]) && $model->data[$model->name][$field] === null
            ) {
                if (!empty($model->id)) {
                    $this->_prepareToDeleteFiles($model, $field, true);
                }
                unset($model->data[$model->name][$field]);
                $model->data[$model->name][$field . '_file_name'] = null;
                $model->data[$model->name][$field . '_file_size'] = null;
                $model->data[$model->name][$field . '_content_type'] = null;
            }
        }
        return true;
    }

    public function afterSave(Model $model, $create, $options = array())
    {
        if (!$create) {
            $this->_deleteFiles($model);
        }
        $this->_writeFiles($model);
    }

    public function beforeDelete(Model $model, $cascade = true)
    {
        $this->_reset();
        $this->_prepareToDeleteFiles($model);
        return true;
    }

    public function afterDelete(Model $model)
    {
        $this->_deleteFiles($model);
    }

    public function beforeValidate(Model $model, $options = array())
    {
        foreach (self::$__settings[$model->name] as $field => $settings) {
            if (isset($model->data[$model->name][$field])) {
                $data = $model->data[$model->name][$field];

                if ((empty($data) || is_array($data) && empty($data['tmp_name'])) && !empty($settings['urlField']) && !empty($model->data[$model->name][$settings['urlField']])) {
                    $data = $model->data[$model->name][$settings['urlField']];
                }

                if ($data != null && !is_array($data)) {
                    $model->data[$model->name][$field] = $this->_fetchFromUrl($data);
                } elseif (!is_array($data)) {
                    $model->data[$model->name][$field] = null;
                }
            }
        }
        return true;
    }

    private function _reset()
    {
        $this->toWrite = null;
        $this->toDelete = null;
    }

    /** @noinspection PhpUndefinedClassInspection */
    private function _fetchFromUrl($url)
    {
        try {
            $data = array('remote' => true);
            $urlExplodedBySlash = explode('/', $url);
            $data['name'] = end($urlExplodedBySlash);
            $urlExplodedByDot = explode('.', $url);
            //サポートしている拡張子かチェック
            if (in_array($urlExplodedByDot, $this->supportedExtensions)) {
                $data['tmp_name'] = tempnam(sys_get_temp_dir(), $data['name']) . '.' . end($urlExplodedByDot);
            } else {
                $data['name'] .= '.' . self::getImgExtensionFromUrl($url);
                $data['tmp_name'] = tempnam(sys_get_temp_dir(),
                        $data['name']) . '.' . self::getImgExtensionFromUrl($url);
            }

            $config = [
                'ssl_verify_host' => false,
            ];
            $httpSocket = new HttpSocket($config);
            $raw = $httpSocket->get($url, array(), array('redirect' => true));
            /**
             * @var HttpResponse $response
             */
            $response = $httpSocket->response;
            //404ならnull返す
            if ($response->code == "404") {
                return null;
            }
            $data['size'] = strlen($raw);
            if (!isset($response['header']['Content-Type'])) {
                return null;
            }
            $headerContentType = explode(';', $response['header']['Content-Type']);
            $data['type'] = reset($headerContentType);

            file_put_contents($data['tmp_name'], $raw);
        } catch (Exception $e) {
            $this->log(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            return null;
        }

        return $data;
    }

    static public function getImgExtensionFromUrl($url)
    {
        $img_types = [
            IMAGETYPE_PNG      => 'png',
            IMAGETYPE_GIF      => 'gif',
            IMAGETYPE_JPEG     => 'jpg',
            IMAGETYPE_JPEG2000 => 'jpg',
        ];
        $imageInfo = @getimagesize($url);
        if (empty($imageInfo)) {
            return null;
        }
        list(, , $type,) = $imageInfo;
        if (array_key_exists($type, $img_types)) {
            return $img_types[$type];
        }
        return null;
    }

    private function _prepareToWriteFiles(&$model, $field, $settings)
    {
        //ファイル名を変更(ファイル名の後にフィールド名を追加)
        $file_name = $model->data[$model->name][$field]['name'];
        // NFC正規化（Mac ファイル名対応）
        $file_name = Normalizer::normalize($file_name, Normalizer::FORM_C);
        if ($settings['addFieldNameOnFileName']) {
            $file_name = substr($file_name, 0, strrpos($file_name, '.')) . // filename
                "_" . $field .
                substr($file_name, strrpos($file_name, '.') //extension
                );
        }
        $model->data[$model->name][$field]['name'] = $file_name;

        $this->toWrite[$field] = $model->data[$model->name][$field];
    }

    private function _writeFiles(&$model)
    {
        if (!empty($this->toWrite)) {
            foreach ($this->toWrite as $field => $toWrite) {
                $settings = $this->_interpolate($model, $field, $toWrite['name'], 'original');
                $destDir = dirname($settings['path']);
                $parentDir = dirname($destDir);
                if (!file_exists($parentDir)) {
                    @mkdir($parentDir, 0775, true);
                    @chmod($parentDir, 0775);
                }
                if (!file_exists($destDir)) {
                    @mkdir($destDir, 0775, true);
                    @chmod($destDir, 0775);
                }
                if (is_dir($destDir) && is_writable($destDir)) {
                    $move = !empty($toWrite['remote']) ? 'rename' : 'move_uploaded_file';
                    if (@$move($toWrite['tmp_name'], $settings['path'])) {
                        //画像の回転
                        $this->saveRotatedFile($settings['path']);
                        $this->s3Upload($settings['path'], $this->toWrite[$field]['type']);

                        if ($this->maxWidthSize) {
                            $this->_resize($settings['path'], $settings['path'], $this->maxWidthSize . 'w',
                                $settings['quality'], $settings['alpha'], $this->toWrite[$field]['type']);
                        }
                        foreach ($settings['styles'] as $style => $geometry) {
                            $newSettings = $this->_interpolate($model, $field, $toWrite['name'], $style);
                            $this->_resize($settings['path'], $newSettings['path'], $geometry, $settings['quality'],
                                $settings['alpha'], $this->toWrite[$field]['type']);
                        }
                    }
                }
            }
        }
    }

    private function _prepareToDeleteFiles(Model &$model, $field = null, $forceRead = false)
    {
        $needToRead = true;
        if ($field === null) {
            $fields = array_keys(self::$__settings[$model->name]);
            foreach ($fields as &$field) {
                /** @noinspection PhpUnusedLocalVariableInspection */
                $field .= '_file_name';
            }
        } else {
            $field .= '_file_name';
            $fields = array($field);
        }

        if (!$forceRead && !empty($model->data[$model->alias])) {
            $needToRead = false;
            foreach ($fields as $field) {
                if (!array_key_exists($field, $model->data[$model->alias])) {
                    $needToRead = true;
                    break;
                }
            }
        }
        if ($needToRead) {
            $data = $model->find('first',
                array(
                    'conditions' => array($model->alias . '.' . $model->primaryKey => $model->id),
                    'fields'     => $fields,
                    'callbacks'  => false
                ));
        } else {
            $data = $model->data;
        }
        if (is_array($this->toDelete)) {
            $this->toDelete = array_merge($this->toDelete, $data[$model->alias]);
        } else {
            $this->toDelete = $data[$model->alias];
        }
        $this->toDelete['id'] = $model->id;
    }

    private function _deleteFiles(&$model)
    {
        foreach (self::$__settings[$model->name] as $field => $settings) {
            if (!empty($this->toDelete[$field . '_file_name'])) {
                $styles = array_keys($settings['styles']);
                $styles[] = 'original';
                foreach ($styles as $style) {
                    $settings = $this->_interpolate($model, $field, $this->toDelete[$field . '_file_name'], $style);
                    if (file_exists($settings['path'])) {
                        @unlink($settings['path']);
                    }
                    $this->s3Delete($settings['path']);
                }
            }
        }
    }

    private function _interpolate(&$model, $field, $filename, $style)
    {
        return self::interpolate($model->name, $model->id, $field, $filename, $style);
    }

    static public function interpolate(
        $modelName,
        $modelId,
        $field,
        $filename,
        $style = 'original',
        $defaults = array()
    ) {
        $pathinfo = UploadBehavior::_pathinfo($filename);
        $interpolations = array_merge(array(
            'app'        => preg_replace('/\/$/', '', APP),
            'webroot'    => preg_replace('/\/$/', '', WWW_ROOT),
            'model'      => Inflector::tableize($modelName),
            'basename'   => !empty($filename) ? $pathinfo['filename'] : null,
            'extension'  => !empty($pathinfo['extension']) ? $pathinfo['extension'] : null,
            'id'         => $modelId,
            'style'      => $style,
            'attachment' => Inflector::pluralize($field),
            'hash'       => md5((!empty($filename) ? $pathinfo['filename'] : "") . Configure::read('Security.salt'))
        ), $defaults);
            $settings = self::$__settings[$modelName][$field] ??
                [
                    'path'                   => ':webroot/upload/:model/:id/:hash_:style.:extension',
                    'styles'                 => [],
                    'resizeToMaxWidth'       => false,
                    'quality'                => 75,
                    'alpha'                  => false,
                    'addFieldNameOnFileName' => true,
                ];
        $keys = array('path', 'url', 'default_url');
        foreach ($interpolations as $k => $v) {
            foreach ($keys as $key) {
                if (isset($settings[$key])) {
                    $settings[$key] = preg_replace('/\/{2,}/', '/', str_replace(":$k", $v, $settings[$key]));
                }
            }
        }
        return $settings;
    }

    static private function _pathinfo($filename)
    {
        // TODO: For researching PHP Notice. See -> https://jira.goalous.com/browse/GL-5973
        if (is_array($filename)) {
            CakeLog::error(sprintf('[%s] Array to string conversion. $filename: %s, session user_id: %s, session current_team_id: %s, backtrace: %s',
                __CLASS__ . "::" . __METHOD__,
                AppUtil::varExportOneLine($filename),
                CakeSession::read('Auth.User.id'),
                CakeSession::read('current_team_id'),
                Debugger::trace()
            ));
            $pathinfo = pathinfo("");
        } else {
            $orig_locale = setlocale(LC_CTYPE, 0);
            setlocale(LC_CTYPE, 'ja_JP.UTF-8');
            $pathinfo = pathinfo($filename);
            setlocale(LC_CTYPE, $orig_locale);
        }
        // PHP < 5.2.0 doesn't include 'filename' key in pathinfo. Let's try to fix this.
        if (empty($pathinfo['filename'])) {
            $suffix = !empty($pathinfo['extension']) ? '.' . $pathinfo['extension'] : '';
            $pathinfo['filename'] = basename($pathinfo['basename'], $suffix);
        }
        return $pathinfo;
    }

    private function _resize($srcFile, $destFile, $geometry, $quality = 75, $alpha = false, $type)
    {
        $imgMimeType = $this->getImageMimeSubType($srcFile);
        if ($imgMimeType === false) {
            return false;
        }
        copy($srcFile, $destFile);
        @chmod($destFile, 0777);

        $createHandler = $this->getCreateHandler($imgMimeType);
        $outputHandler = $this->getOutputHandler($imgMimeType);
        if (!$createHandler || !$outputHandler) {
            return false;
        }

        $src = $this->_getImgSource($createHandler, $destFile);

        if (!$src) {
            $this->log(sprintf('creating img object was failed.'));
            $this->log(Debugger::trace());
            $this->_backupFailedImgFile(basename($srcFile), $srcFile);
            return false;
        }

        $srcW = imagesx($src);
        $srcH = imagesy($src);

        // determine destination dimensions and resize mode from provided geometry
        if (preg_match('/^\\[[\\d]+x[\\d]+\\]$/', $geometry)) {
            // resize with banding
            list($destW, $destH) = explode('x', substr($geometry, 1, strlen($geometry) - 2));
            $resizeMode = 'band';
        } elseif (preg_match("/^f\[(\d+)x(\d+)\]$/", $geometry, $match)) {
            // resize with force
            $destW = $match[1];
            $destH = $match[2];
            $resizeMode = 'force';
        } elseif (preg_match('/^[\\d]+x[\\d]+$/', $geometry)) {
            // cropped resize (best fit)
            list($destW, $destH) = explode('x', $geometry);
            $resizeMode = 'best';
        } elseif (preg_match('/^[\\d]+w$/', $geometry)) {
            // calculate heigh according to aspect ratio
            $destW = (int)$geometry - 1;
            $resizeMode = false;
        } //wの追加ルールで指定サイズより実際のサイズが小さい場合はリサイズしない
        elseif (preg_match('/^[\\d]+W$/', $geometry)) {
            // calculate heigh according to aspect ratio
            $destW = (int)$geometry - 1;
            if ($destW > $srcW) {
                $destW = $srcW;
            }
            $resizeMode = false;
        } elseif (preg_match('/^[\\d]+h$/', $geometry)) {
            // calculate width according to aspect ratio
            $destH = (int)$geometry - 1;
            $resizeMode = false;
        } //hの追加ルールで指定サイズより実際のサイズが小さい場合はリサイズしない
        elseif (preg_match('/^[\\d]+H$/', $geometry)) {
            // calculate width according to aspect ratio
            $destH = (int)$geometry - 1;
            if ($destH > $srcH) {
                $destH = $srcH;
            }
            $resizeMode = false;
        } elseif (preg_match('/^[\\d]+l$/', $geometry)) {
            // calculate shortest side according to aspect ratio
            if ($srcW > $srcH) {
                $destW = (int)$geometry - 1;
            } else {
                $destH = (int)$geometry - 1;
            }
            $resizeMode = false;
        } //lの追加ルールで指定サイズより実際のサイズが小さい場合はリサイズしない
        elseif (preg_match('/^[\\d]+L$/', $geometry)) {
            // calculate shortest side according to aspect ratio
            if ($srcW > $srcH) {
                $destW = (int)$geometry - 1;
                if ($destW > $srcW) {
                    $destW = $srcW;
                }
            } else {
                $destH = (int)$geometry - 1;
                if ($destH > $srcH) {
                    $destH = $srcH;
                }
            }
            $resizeMode = false;
        }

        if (!isset($destW)) {
            /** @noinspection PhpUndefinedVariableInspection */
            $destW = ($destH / $srcH) * $srcW;
        }
        if (!isset($destH)) {
            $destH = ($destW / $srcW) * $srcH;
        }

        // determine resize dimensions from appropriate resize mode and ratio
        /** @noinspection PhpUndefinedVariableInspection */
        if ($resizeMode == 'best') {
            // "best fit" mode
            if ($srcW > $srcH) {
                if ($srcH / $destH > $srcW / $destW) {
                    $ratio = $destW / $srcW;
                } else {
                    $ratio = $destH / $srcH;
                }
            } else {
                if ($srcH / $destH < $srcW / $destW) {
                    $ratio = $destH / $srcH;
                } else {
                    $ratio = $destW / $srcW;
                }
            }
            $resizeW = $srcW * $ratio;
            $resizeH = $srcH * $ratio;
        } elseif ($resizeMode == 'band') {
            // "banding" mode
            if ($srcW > $srcH) {
                $ratio = $destW / $srcW;
            } else {
                $ratio = $destH / $srcH;
            }
            $resizeW = $srcW * $ratio;
            $resizeH = $srcH * $ratio;
        } else {
            // no resize ratio
            $resizeW = $destW;
            $resizeH = $destH;
        }

        $img = imagecreatetruecolor($destW, $destH);

        if ($alpha === true) {
            switch (strtolower($imgMimeType)) {
                case 'gif':
                    $alphaColor = imagecolortransparent($src);
                    imagefill($img, 0, 0, $alphaColor);
                    imagecolortransparent($img, $alphaColor);
                    break;
                case 'png':
                    imagealphablending($img, false);
                    imagesavealpha($img, true);
                    break;
                default:
                    imagefill($img, 0, 0, imagecolorallocate($img, 255, 255, 255));
                    break;
            }
        } else {
            imagefill($img, 0, 0, imagecolorallocate($img, 255, 255, 255));
        }
        imagecopyresampled($img, $src, ($destW - $resizeW) / 2, ($destH - $resizeH) / 2, 0, 0, $resizeW, $resizeH,
            $srcW, $srcH);
        $outputHandler($img, $destFile, $quality);

        $this->s3Upload($destFile, $type);

        return true;
    }

    /**
     * 画像の種類を判別する
     *
     * @param string $filePath
     *
     * @return bool|string
     */
    function getImageMimeSubType(string $filePath)
    {
        $imageInfo = getimagesize($filePath);
        if (strpos($imageInfo['mime'], 'jpeg') !== false) {
            $imageType = 'jpeg';
        } elseif (strpos($imageInfo['mime'], 'png') !== false) {
            $imageType = 'png';
        } elseif (strpos($imageInfo['mime'], 'gif') !== false) {
            $imageType = 'gif';
        } else {
            return false;
        }
        return $imageType;
    }

    /**
     * 画像作成ハンドラのメソッド名を取得
     *
     * @param string $mimeType
     *
     * @return bool|string
     */
    function getCreateHandler(string $mimeType)
    {
        switch (strtolower($mimeType)) {
            case 'gif':
                $createHandler = 'imagecreatefromgif';
                break;
            case 'jpg':
            case 'jpeg':
                $createHandler = 'imagecreatefromjpeg';
                break;
            case 'png':
                $createHandler = 'imagecreatefrompng';
                break;
            default:
                return false;
        }
        return $createHandler;
    }

    /**
     * 画像ファイル出力用ハンドラのメソッド名を取得
     *
     * @param string $mimeType
     *
     * @return bool|string
     */
    function getOutputHandler(string $mimeType)
    {
        switch (strtolower($mimeType)) {
            case 'gif':
                $outputHandler = 'imagegif';
                break;
            case 'jpg':
            case 'jpeg':
                $outputHandler = 'imagejpeg';
                break;
            case 'png':
                //pngはjpegに変換。理由はファイル圧縮のため https://github.com/IsaoCorp/goalous/pull/422
                $outputHandler = 'imagejpeg';
                break;
            default:
                return false;
        }
        return $outputHandler;
    }

    public function attachmentMinSize(
        /** @noinspection PhpUnusedParameterInspection */
        Model $model,
        $value,
        $min
    ) {
        $value = array_shift($value);
        if (!empty($value['tmp_name'])) {
            return (int)$min <= (int)$value['size'];
        }
        return true;
    }

    public function attachmentMaxSize(
        /** @noinspection PhpUnusedParameterInspection */
        Model $model,
        $value,
        $max
    ) {
        $value = array_shift($value);
        if (!empty($value['tmp_name'])) {
            return (int)$value['size'] <= (int)$max;
        }
        return true;
    }

    /**
     * 許可している画像かどうかチェック
     * JPEG, GIF, PNGのみ許可
     * 画像以外の場合は、検査スルーする
     *
     * @param Model $model
     * @param array $value file info
     *
     * @return bool
     */
    public function attachmentImageType(
        /** @noinspection PhpUnusedParameterInspection */
        Model $model,
        array $value
    ) {
        $value = array_shift($value);
        // 一時ファイル名が空の場合
        // ※ 保存が任意のユーザー画像もバリデーションを通るためtrueで返す必要がある
        if (empty($value['tmp_name'])) {
            return true;
        }

        // 画像であるか
        $targetImgType = exif_imagetype($value['tmp_name']);
        if ($targetImgType === true) {
            return true;
        }

        // MIMEタイプチェック(JPG,GIF,PNGのみ許可)
        if (!isset($this->imgExtEachTypes[$targetImgType])) {
            return false;
        }

        // 拡張子チェック
        $ext = pathinfo($value['name'], PATHINFO_EXTENSION);
        if (!preg_grep("/{$ext}/i", $this->supportedExtensions)) {
            return false;
        }

        return true;
    }

    /**
     * 画像の加工処理が可能かどうか？
     * バリデーションルール
     *
     * @param Model $model
     * @param array $value
     *
     * @return bool
     */
    public function canProcessImage(
        /** @noinspection PhpUnusedParameterInspection */
        Model $model,
        array $value
    ) {
        $value = array_shift($value);
        $imgTmpFilePath = $value['tmp_name'];
        if (empty($imgTmpFilePath)) {
            return true;
        }

        $imgMimeType = $this->getImageMimeSubType($imgTmpFilePath);
        if ($imgMimeType === false) {
            // 許可している画像以外はスルー
            return true;
        }

        $createHandler = $this->getCreateHandler($imgMimeType);
        $outputHandler = $this->getOutputHandler($imgMimeType);
        if (!$createHandler || !$outputHandler) {
            // 許可している画像以外はスルー
            return true;
        }

        $src = $this->_getImgSource($createHandler, $imgTmpFilePath);

        if (!$src) {
            $this->log(sprintf('canProcessImage validation was failed. uid=%s', $model->my_uid));
            $this->log(Debugger::exportVar($model->data));
            $this->log(sprintf("ImageFileInfo: %s", var_export($value, true)));
            $this->_backupFailedImgFile($value['name'], $imgTmpFilePath);
            return false;
        }

        return true;

    }

    /**
     * 処理失敗した画像ファイルをバックアップ
     * - ログにバックアップしたファイルパスを格納する
     *
     * @param string $userFileName ユーザがアップロードした元のファイル名
     * @param string $srcFilePath  ファイルが置かれている場所
     */
    private function _backupFailedImgFile(string $userFileName, string $srcFilePath)
    {
        $backupFileDir = '/tmp/failedImages';
        if (!file_exists($backupFileDir)) {
            mkdir($backupFileDir, 0775, true);
        }
        $distFilePath = $backupFileDir . '/' . time() . '_' . $userFileName;
        copy($srcFilePath, $distFilePath);
        $this->log(sprintf("BackupFile: %s", $distFilePath));
    }

    public function attachmentPresence(Model $model, $value)
    {
        $keys = array_keys($value);
        $field = $keys[0];
        $value = array_shift($value);

        if (!empty($value['tmp_name'])) {
            return true;
        }

        if (!empty($model->id)) {
            if (!empty($model->data[$model->alias][$field . '_file_name'])) {
                return true;
            } elseif (!isset($model->data[$model->alias][$field . '_file_name'])) {
                $existingFile = $model->field($field . '_file_name', array($model->primaryKey => $model->id));
                if (!empty($existingFile)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * バリデーション
     * 画像が最低の幅・高さを満たしているか
     *
     * @param Model $model
     * @param       $value
     * @param       $minWidth
     * @param       $minHeight
     *
     * @return bool
     */
    public function minWidthHeight(
        /** @noinspection PhpUnusedParameterInspection */
        Model $model,
        $value,
        $minWidth,
        $minHeight
    ) {
        // check upload
        if (!$this->isUpload($value)) {
            return true;
        }
        return $this->_validateDimension($value, 'min', 'x', $minWidth) && $this->_validateDimension($value, 'min', 'y',
                $minHeight);
    }

    /**
     * アップロードされているか判定
     *
     * @param $upload
     *
     * @return bool
     */
    private function isUpload($upload)
    {
        $upload = array_shift($upload);
        if (!empty($upload['tmp_name'])) {
            return true;
        }
        return false;
    }

    public function minWidth(
        /** @noinspection PhpUnusedParameterInspection */
        Model $model,
        $value,
        $minWidth
    ) {
        return $this->_validateDimension($value, 'min', 'x', $minWidth);
    }

    public function minHeight(
        /** @noinspection PhpUnusedParameterInspection */
        Model $model,
        $value,
        $minHeight
    ) {
        return $this->_validateDimension($value, 'min', 'y', $minHeight);
    }

    public function maxWidth(Model $model, $value, $maxWidth)
    {
        $keys = array_keys($value);
        $field = $keys[0];
        $settings = self::$__settings[$model->name][$field];
        if ($settings['resizeToMaxWidth'] && !$this->_validateDimension($value, 'max', 'x', $maxWidth)) {
            $this->maxWidthSize = $maxWidth;
            return true;
        } else {
            return $this->_validateDimension($value, 'max', 'x', $maxWidth);
        }
    }

    public function maxHeight(
        /** @noinspection PhpUnusedParameterInspection */
        Model $model,
        $value,
        $maxHeight
    ) {
        return $this->_validateDimension($value, 'max', 'y', $maxHeight);
    }

    private function _validateDimension($upload, $mode, $axis, $value)
    {
        $upload = array_shift($upload);
        $func = 'images' . $axis;
        if (empty($upload['tmp_name'])) {
            return false;
        }

        if ($upload['type'] == 'image/jpeg') {
            $createHandler = 'imagecreatefromjpeg';
        } elseif ($upload['type'] == 'image/gif') {
            $createHandler = 'imagecreatefromgif';
        } elseif ($upload['type'] == 'image/png') {
            $createHandler = 'imagecreatefrompng';
        } else {
            return false;
        }

        $img = $this->_getImgSource($createHandler, $upload['tmp_name']);

        if (!$img) {
            $this->log(sprintf('_validateDimension validation was failed.'));
            $this->log(sprintf("ImageFileInfo: %s", var_export($upload, true)));
            $this->_backupFailedImgFile($upload['name'], $upload['tmp_name']);
            return false;
        }

        switch ($mode) {
            case 'min':
                return $func($img) >= $value;
                break;
            case 'max':
                return $func($img) <= $value;
                break;
        }

        return false;
    }

    /**
     * 画像のソースを取得する
     *
     * @param string $handler
     * @param string $imgPath
     *
     * @return mixed
     */
    private function _getImgSource(
        string $handler,
        string $imgPath
    ) {
        // 画像によっては問題ない画像でも以下のNoticeが出力される場合がある。
        // Notice (8): imagecreatefromjpeg(): gd-jpeg, libjpeg: recoverable error: Invalid SOS parameters for sequential JPEG
        //ローカルでNoticeが邪魔になる場合は、一時的に Configure::write('debug', 0); を推奨。
        $src = $handler($imgPath);
        return $src;
    }

    public function phpUploadError(
        /** @noinspection PhpUnusedParameterInspection */
        Model $model,
        $value,
        $uploadErrors = array(
            'UPLOAD_ERR_INI_SIZE',
            'UPLOAD_ERR_FORM_SIZE',
            'UPLOAD_ERR_PARTIAL',
            'UPLOAD_ERR_NO_FILE',
            'UPLOAD_ERR_NO_TMP_DIR',
            'UPLOAD_ERR_CANT_WRITE',
            'UPLOAD_ERR_EXTENSION'
        )
    ) {
        $value = array_shift($value);
        if (!is_array($uploadErrors)) {
            $uploadErrors = array($uploadErrors);
        }
        if (!empty($value['error'])) {
            return !in_array($value['error'], $uploadErrors);
        }
        return true;
    }

    function saveRotatedFile($filePath, $outPath = null)
    {
        if (!file_exists($filePath)) {
            return false;
        }

        $flip = false;
        $degrees = $this->getDegrees($filePath, $flip);

        //回転の必要ない場合は何もしない
        if ($degrees === 0 && $flip === false) {
            return true;
        }
        $imgMimeType = $this->getImageMimeSubType($filePath);

        $createHandler = $this->getCreateHandler($imgMimeType);
        if ($createHandler === false) {
            return false;
        }
        $outputHandler = $this->getOutputHandler($imgMimeType);

        $image = $this->_getImgSource($createHandler, $filePath);

        if (!$image) {
            GoalousLog::error("saveRotatedFile", [
                "creating img object was failed.",
                Debugger::trace(),
                $this->_backupFailedImgFile(basename($filePath), $filePath)
            ]);
            return false;
        }

        // Rotation
        $image = imagerotate($image, $degrees, 0);

        // Flipping
        if ($flip && !imageflip($image, IMG_FLIP_HORIZONTAL)) {
            $this->log(sprintf('flipping image object has failed.'));
            $this->log(Debugger::trace());
            $this->_backupFailedImgFile(basename($filePath), $filePath);
            return false;
        }
        // Save
        if (empty($outPath)) {
            $outputHandler($image, $filePath);
        } else {
            $outputHandler($image, $outPath);
        }
        // Destroy
        imagedestroy($image);

        return true;
    }

    function getDegrees($file_path, &$flip)
    {
        $flip = false;
        //exifをサポートしているのはjpegとtiffのみ。それ以外は0をreturn
        $image_type = exif_imagetype($file_path);
        if ($image_type != IMAGETYPE_JPEG && $image_type != IMAGETYPE_JPEG2000) {
            return 0;
        }
        $exif = @exif_read_data($file_path); //　Exif情報読み込み
        $orientation = !empty($exif['Orientation']) ? $exif['Orientation'] : 1;
        switch ($orientation) {
            case 1: //通常
                return 0;
            case 2: //左右反転
                $flip = true;
                return 0;
            case 3: //180°回転
                return 180;
            case 4: //上下反転
                $flip = true;
                return 180;
            case 5: //反時計回りに90°回転 上下反転
                $flip = true;
                return 270;
            case 6: //反時計回りに90°回転
                return 270;
            case 7: //　時計回りに90°回転 上下反転
                $flip = true;
                return 90;
            case 8: //時計回りに90°回転
                return 90;
        }
        return 0;
    }

    function s3Upload($from_path, $type)
    {
        $to_path = $this->processS3UploadPath($from_path);

        try {
            /**
             * @see http://docs.aws.amazon.com/aws-sdk-php/v3/api/api-s3-2006-03-01.html#putobject
             */
            $response = $this->s3
                ->putObject(
                    array(
                        'Bucket'               => S3_ASSETS_BUCKET,
                        'Key'                  => $to_path,
                        'Body'                 => EntityBody::factory(fopen($from_path, 'r')),
                        'ContentType'          => $type,
                        'StorageClass'         => 'STANDARD',
                        'ServerSideEncryption' => 'AES256',
                        'ACL'                  => 'authenticated-read',
                    ));
            return $response;

        } catch (S3Exception $e) {
        }
        return null;
    }

    function s3Delete($from_path)
    {
        $to_path = $this->processS3UploadPath($from_path);
        try {
            $response = $this->s3->deleteObject(['Bucket' => S3_ASSETS_BUCKET, 'Key' => $to_path]);
            return $response;
        } catch (S3Exception $e) {
        }
        return null;
    }

    function _setupS3()
    {
        if ($this->s3) {
            return;
        }
        // S3を操作するためのオブジェクトを生成
        $this->s3 = AwsClientFactory::createS3ClientForFileStorage();
    }

    /**
     * @param string $tmpPath
     * @return string
     */
    private function processS3UploadPath(string $tmpPath): string
    {
        $img_path_exp = explode(S3_TRIM_PATH, $tmpPath);
        $path = $img_path_exp[1];
        if (ENV_NAME === "local") {
            if (empty(AWS_S3_BUCKET_USERNAME)) {
                throw new RuntimeException("Please define AWS_S3_BUCKET_USERNAME");
            }
            $path = AWS_S3_BUCKET_USERNAME . "/" . $path;
        }
        return $path;
    }
}
