<?php
App::uses('HttpSocket', 'Network/Http');
use Aws\Common\Aws;
use Aws\S3;
use Aws\S3\S3Client;
use Aws\Common\Enum\Region;
use Guzzle\Http\EntityBody;
use Aws\S3\Enum\CannedAcl;
use Aws\S3\Exception\S3Exception;

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
        $defaults = array(
            'path'             => ':webroot/upload/:model/:id/:basename_:style.:extension',
            'styles'           => array(),
            'resizeToMaxWidth' => false,
            'quality'          => 75,
            'alpha'            => false
        );
        foreach ($settings as $field => $array) {
            self::$__settings[$model->name][$field] = array_merge($defaults, $array);
        }
        if (PUBLIC_ENV) {
            $this->_setupS3();
        }

    }

    public function beforeSave(Model $model, $options = array())
    {
        $this->_reset();
        foreach (self::$__settings[$model->name] as $field => $settings) {
            if (!empty($model->data[$model->name][$field]) && is_array($model->data[$model->name][$field]) && file_exists($model->data[$model->name][$field]['tmp_name'])) {
                if (!empty($model->id)) {
                    $this->_prepareToDeleteFiles($model, $field, true);
                }
                $this->_prepareToWriteFiles($model, $field);
                unset($model->data[$model->name][$field]);
                $model->data[$model->name][$field . '_file_name'] = $this->toWrite[$field]['name'];
                $model->data[$model->name][$field . '_file_size'] = $this->toWrite[$field]['size'];
                $model->data[$model->name][$field . '_content_type'] = $this->toWrite[$field]['type'];
            }
            elseif (array_key_exists($field, $model->data[$model->name]) && $model->data[$model->name][$field] === null
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
                }
                elseif (!is_array($data)) {
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
        $data = array('remote' => true);
        $urlExplodedBySlash = explode('/', $url);
        $data['name'] = end($urlExplodedBySlash);
        $urlExplodedByDot = explode('.', $url);
        //サポートしている拡張子かチェック
        if (in_array($urlExplodedByDot, $this->supportedExtensions)) {
            $data['tmp_name'] = tempnam(sys_get_temp_dir(), $data['name']) . '.' . end($urlExplodedByDot);
        }
        else {
            $data['name'] .= '.' . self::_getImgExtensionFromUrl($url);
            $data['tmp_name'] = tempnam(sys_get_temp_dir(), $data['name']) . '.' . self::_getImgExtensionFromUrl($url);
        }

        $config = [
            'ssl_verify_host' => false,
        ];
        $httpSocket = new HttpSocket($config);
        $raw = $httpSocket->get($url);
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
        return $data;
    }

    static private function _getImgExtensionFromUrl($url)
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

    private function _prepareToWriteFiles(&$model, $field)
    {
        //ファイル名を変更(ファイル名の後にフィールド名を追加)
        $file_name = $model->data[$model->name][$field]['name'];
        $file_name = substr($file_name, 0, strrpos($file_name, '.')) . // filename
            "_" . $field .
            substr($file_name, strrpos($file_name, '.') //extension
            );
        $model->data[$model->name][$field]['name'] = $file_name;

        $this->toWrite[$field] = $model->data[$model->name][$field];
        // make filename URL friendly by using Cake's Inflector
        $this->toWrite[$field]['name'] =
            Inflector::slug(substr($this->toWrite[$field]['name'], 0,
                                   strrpos($this->toWrite[$field]['name'], '.'))) . // filename
            substr($this->toWrite[$field]['name'], strrpos($this->toWrite[$field]['name'], '.')); // extension
    }

    private function _writeFiles(&$model)
    {
        if (!empty($this->toWrite)) {
            foreach ($this->toWrite as $field => $toWrite) {
                $settings = $this->_interpolate($model, $field, $toWrite['name'], 'original');
                $destDir = dirname($settings['path']);
                if (!file_exists($destDir)) {
                    @mkdir($destDir, 0777, true);
                    @chmod($destDir, 0777);
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
        }
        else {
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
                                 array('conditions' => array($model->alias . '.' . $model->primaryKey => $model->id), 'fields' => $fields, 'callbacks' => false));
        }
        else {
            $data = $model->data;
        }
        if (is_array($this->toDelete)) {
            $this->toDelete = array_merge($this->toDelete, $data[$model->alias]);
        }
        else {
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

    static public function interpolate($modelName, $modelId, $field, $filename, $style = 'original', $defaults = array())
    {
        $pathinfo = UploadBehavior::_pathinfo($filename);
        $interpolations = array_merge(array(
                                          'app'        => preg_replace('/\/$/', '', APP),
                                          'webroot'    => preg_replace('/\/$/', '', WWW_ROOT),
                                          'model'      => Inflector::tableize($modelName),
                                          'basename'   => !empty($filename) ? $pathinfo['filename'] : null,
                                          'extension'  => !empty($filename) ? $pathinfo['extension'] : null,
                                          'id'         => $modelId,
                                          'style'      => $style,
                                          'attachment' => Inflector::pluralize($field),
                                          'hash'       => md5((!empty($filename) ? $pathinfo['filename'] : "") . Configure::read('Security.salt'))
                                      ), $defaults);
        $settings = self::$__settings[$modelName][$field];
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
        $pathinfo = pathinfo($filename);
        // PHP < 5.2.0 doesn't include 'filename' key in pathinfo. Let's try to fix this.
        if (empty($pathinfo['filename'])) {
            $suffix = !empty($pathinfo['extension']) ? '.' . $pathinfo['extension'] : '';
            $pathinfo['filename'] = basename($pathinfo['basename'], $suffix);
        }
        return $pathinfo;
    }

    private function _resize($srcFile, $destFile, $geometry, $quality = 75, $alpha = false, $type)
    {
        copy($srcFile, $destFile);
        @chmod($destFile, 0777);
        $pathinfo = UploadBehavior::_pathinfo($srcFile);
        $src = null;
        $createHandler = null;
        $outputHandler = null;
        switch (strtolower($pathinfo['extension'])) {
            case 'gif':
                $createHandler = 'imagecreatefromgif';
                $outputHandler = 'imagegif';
                break;
            case 'jpg':
            case 'jpeg':
                $createHandler = 'imagecreatefromjpeg';
                $outputHandler = 'imagejpeg';
                break;
            case 'png':
                $createHandler = 'imagecreatefrompng';
                //pngはjpegに変換
                $outputHandler = 'imagejpeg';
                break;
            default:
                return false;
        }
        if ($src = $createHandler($destFile)) {
            $srcW = imagesx($src);
            $srcH = imagesy($src);

            // determine destination dimensions and resize mode from provided geometry
            if (preg_match('/^\\[[\\d]+x[\\d]+\\]$/', $geometry)) {
                // resize with banding
                list($destW, $destH) = explode('x', substr($geometry, 1, strlen($geometry) - 2));
                $resizeMode = 'band';
            }
            elseif (preg_match('/^[\\d]+x[\\d]+$/', $geometry)) {
                // cropped resize (best fit)
                list($destW, $destH) = explode('x', $geometry);
                $resizeMode = 'best';
            }
            elseif (preg_match('/^[\\d]+w$/', $geometry)) {
                // calculate heigh according to aspect ratio
                $destW = (int)$geometry - 1;
                $resizeMode = false;
            }
            elseif (preg_match('/^[\\d]+h$/', $geometry)) {
                // calculate width according to aspect ratio
                $destH = (int)$geometry - 1;
                $resizeMode = false;
            }
            elseif (preg_match('/^[\\d]+l$/', $geometry)) {
                // calculate shortest side according to aspect ratio
                if ($srcW > $srcH) {
                    $destW = (int)$geometry - 1;
                }
                else {
                    $destH = (int)$geometry - 1;
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
                    }
                    else {
                        $ratio = $destH / $srcH;
                    }
                }
                else {
                    if ($srcH / $destH < $srcW / $destW) {
                        $ratio = $destH / $srcH;
                    }
                    else {
                        $ratio = $destW / $srcW;
                    }
                }
                $resizeW = $srcW * $ratio;
                $resizeH = $srcH * $ratio;
            }
            elseif ($resizeMode == 'band') {
                // "banding" mode
                if ($srcW > $srcH) {
                    $ratio = $destW / $srcW;
                }
                else {
                    $ratio = $destH / $srcH;
                }
                $resizeW = $srcW * $ratio;
                $resizeH = $srcH * $ratio;
            }
            else {
                // no resize ratio
                $resizeW = $destW;
                $resizeH = $destH;
            }

            $img = imagecreatetruecolor($destW, $destH);

            if ($alpha === true) {
                switch (strtolower($pathinfo['extension'])) {
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
            }
            else {
                imagefill($img, 0, 0, imagecolorallocate($img, 255, 255, 255));
            }
            imagecopyresampled($img, $src, ($destW - $resizeW) / 2, ($destH - $resizeH) / 2, 0, 0, $resizeW, $resizeH,
                               $srcW, $srcH);
            $outputHandler($img, $destFile, $quality);

            $this->s3Upload($destFile, $type);

            return true;
        }
        return false;
    }

    public function attachmentMinSize(/** @noinspection PhpUnusedParameterInspection */
        Model $model, $value, $min)
    {
        $value = array_shift($value);
        if (!empty($value['tmp_name'])) {
            return (int)$min <= (int)$value['size'];
        }
        return true;
    }

    public function attachmentMaxSize(/** @noinspection PhpUnusedParameterInspection */
        Model $model, $value, $max)
    {
        $value = array_shift($value);
        if (!empty($value['tmp_name'])) {
            return (int)$value['size'] <= (int)$max;
        }
        return true;
    }

    public function attachmentContentType(/** @noinspection PhpUnusedParameterInspection */
        Model $model, $value, $contentTypes)
    {
        $value = array_shift($value);
        if (!is_array($contentTypes)) {
            $contentTypes = array($contentTypes);
        }
        if (!empty($value['tmp_name'])) {
            foreach ($contentTypes as $contentType) {
                if (substr($contentType, 0, 1) == '/') {
                    if (preg_match($contentType, $value['type'])) {
                        return true;
                    }
                }
                elseif ($contentType == $value['type']) {
                    return true;
                }
            }
            return false;
        }
        return true;
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
            }
            elseif (!isset($model->data[$model->alias][$field . '_file_name'])) {
                $existingFile = $model->field($field . '_file_name', array($model->primaryKey => $model->id));
                if (!empty($existingFile)) {
                    return true;
                }
            }
        }
        return false;
    }

    public function minWidth(/** @noinspection PhpUnusedParameterInspection */
        Model $model, $value, $minWidth)
    {
        return $this->_validateDimension($value, 'min', 'x', $minWidth);
    }

    public function minHeight(/** @noinspection PhpUnusedParameterInspection */
        Model $model, $value, $minHeight)
    {
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
        }
        else {
            return $this->_validateDimension($value, 'max', 'x', $maxWidth);
        }
    }

    public function maxHeight(/** @noinspection PhpUnusedParameterInspection */
        Model $model, $value, $maxHeight)
    {
        return $this->_validateDimension($value, 'max', 'y', $maxHeight);
    }

    private function _validateDimension($upload, $mode, $axis, $value)
    {
        $upload = array_shift($upload);
        $func = 'images' . $axis;
        if (!empty($upload['tmp_name'])) {
            $createHandler = null;
            if ($upload['type'] == 'image/jpeg') {
                $createHandler = 'imagecreatefromjpeg';
            }
            else {
                if ($upload['type'] == 'image/gif') {
                    $createHandler = 'imagecreatefromgif';
                }
                else {
                    if ($upload['type'] == 'image/png') {
                        $createHandler = 'imagecreatefrompng';
                    }
                    else {
                        return false;
                    }
                }
            }

            if ($img = $createHandler($upload['tmp_name'])) {
                switch ($mode) {
                    case 'min':
                        return $func($img) >= $value;
                        break;
                    case 'max':
                        return $func($img) <= $value;
                        break;
                }
            }
        }
        return false;
    }

    public function phpUploadError(/** @noinspection PhpUnusedParameterInspection */
        Model $model, $value, $uploadErrors = array('UPLOAD_ERR_INI_SIZE', 'UPLOAD_ERR_FORM_SIZE', 'UPLOAD_ERR_PARTIAL', 'UPLOAD_ERR_NO_FILE', 'UPLOAD_ERR_NO_TMP_DIR', 'UPLOAD_ERR_CANT_WRITE', 'UPLOAD_ERR_EXTENSION'))
    {
        $value = array_shift($value);
        if (!is_array($uploadErrors)) {
            $uploadErrors = array($uploadErrors);
        }
        if (!empty($value['error'])) {
            return !in_array($value['error'], $uploadErrors);
        }
        return true;
    }

    function saveRotatedFile($file_path)
    {
        $degrees = $this->getDegrees($file_path);
        //回転の必要ない場合は何もしない
        if ($degrees === 0) {
            return null;
        }

        $src = null;
        $createHandler = null;
        $outputHandler = null;

        $image_type = exif_imagetype($file_path);
        switch ($image_type) {
            case IMAGETYPE_GIF:
                $createHandler = 'imagecreatefromgif';
                $outputHandler = 'imagegif';
                break;
            case IMAGETYPE_JPEG:
            case IMAGETYPE_JPEG2000:
                $createHandler = 'imagecreatefromjpeg';
                $outputHandler = 'imagejpeg';
                break;
            case IMAGETYPE_PNG:
                $createHandler = 'imagecreatefrompng';
                $outputHandler = 'imagepng';
                $quality = null;
                break;
            default:
                return false;
        }
        if ($src = $createHandler($file_path)) {
            // 回転
            $rotate = imagerotate($src, $degrees, 0);
            //保存
            $outputHandler($rotate, $file_path);
        }
        return null;
    }

    function getDegrees($file_path)
    {
        //exifをサポートしているのはjpegとtiffのみ。それ以外は0をreturn
        $image_type = exif_imagetype($file_path);
        if ($image_type != IMAGETYPE_JPEG && $image_type != IMAGETYPE_JPEG2000) {
            return 0;
        }
        $exif = @exif_read_data($file_path); //　Exif情報読み込み
        if (isset($exif['Orientation'])) {
            $r_data = $exif['Orientation']; //　画像の向き情報を取り出す
        }
        else {
            return 0;
        }
        switch ($r_data) {
            case 1: //通常
                $degrees = 0;
                return $degrees;
                break;

            case 2: //左右反転
                $degrees = 0;
                return $degrees;
                break;

            case 3: //180°回転
                $degrees = 180;
                return $degrees;
                break;

            case 4: //上下反転
                $degrees = 0;
                return $degrees;
                break;

            case 5: //反時計回りに90°回転 上下反転
                $degrees = 270;
                return $degrees;
                break;

            case 6: //反時計回りに90°回転
                $degrees = 270;
                return $degrees;
                break;

            case 7: //　時計回りに90°回転 上下反転
                $degrees = 90;
                return $degrees;
                break;

            case 8: //時計回りに90°回転
                $degrees = 90;
                return $degrees;
                break;

        }
        return null;
    }

    function s3Upload($from_path, $type)
    {
        //公開環境じゃない場合は処理しない
        if (!PUBLIC_ENV) {
            return false;
        }

        $img_path_exp = explode(S3_TRIM_PATH, $from_path);
        $to_path = $img_path_exp[1];

        try {
            $response = $this->s3
                ->putObject(
                    array(
                        'Bucket'               => S3_ASSETS_BUCKET,
                        'Key'                  => $to_path,
                        'Body'                 => EntityBody::factory(fopen($from_path, 'r')),
                        'ContentType'          => $type,
                        'StorageClass'         => 'STANDARD',
                        'ServerSideEncryption' => 'AES256',
                        'ACL'                  => CannedAcl::AUTHENTICATED_READ
                    ));
            return $response;

        } catch (S3Exception $e) {
        }
        return null;
    }

    function s3Delete($from_path)
    {
        //公開環境じゃない場合は処理しない
        if (!PUBLIC_ENV) {
            return false;
        }
        $img_path_exp = explode(S3_TRIM_PATH, $from_path);
        $to_path = $img_path_exp[1];
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
        // S3を操作するためのオブジェクトを生成（リージョンは東京）
        $this->s3 = Aws::factory(
            array(
                'key'    => AWS_ACCESS_KEY,
                'secret' => AWS_SECRET_KEY,
                'region' => Region::AP_NORTHEAST_1
            ))
                       ->get('s3');
    }
}
