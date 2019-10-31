<?php
App::uses('AppHelper', 'View/Helper');
App::uses('UploadBehavior', 'Model/Behavior');
App::import('Utility', 'Security');

/**
 * This file is a part of UploadPack - a plugin that makes file uploads in CakePHP as easy as possible.
 * UploadHelper
 * UploadHelper provides fine access to files uploaded with UploadBehavior. It generates url for those files and can
 * display image tags of uploaded images. For more info read UploadPack documentation.
 *
 * @author Michał Szajbe (michal.szajbe@gmail.com)
 * @link   http://github.com/szajbus/uploadpack
 * @property HtmlHelper $Html
 */
class UploadHelper extends AppHelper
{

    public $cache = [];
    public $helpers = array('Html');

    private $ext_settings = [
        'xls'  => [
            'viewer'     => 'office_viewer',
            'icon_class' => 'fa-file-excel-o file-excel-icon',
        ],
        'doc'  => [
            'viewer'     => 'office_viewer',
            'icon_class' => 'fa-file-word-o file-word-icon',
        ],
        'ppt'  => [
            'viewer'     => 'office_viewer',
            'icon_class' => 'fa-file-powerpoint-o file-powerpoint-icon',
        ],
        'xlsx' => [
            'viewer'     => 'office_viewer',
            'icon_class' => 'fa-file-excel-o file-excel-icon',
        ],
        'docx' => [
            'viewer'     => 'office_viewer',
            'icon_class' => 'fa-file-word-o file-word-icon',
        ],
        'pptx' => [
            'viewer'     => 'office_viewer',
            'icon_class' => 'fa-file-powerpoint-o file-powerpoint-icon',
        ],
        'pdf'  => [
            'viewer'     => 'normal',
            'icon_class' => 'fa-file-pdf-o file-other-icon',
        ],
        'jpeg' => [
            'viewer' => 'lightbox',
        ],
        'jpg'  => [
            'viewer' => 'lightbox',
        ],
        'png'  => [
            'viewer' => 'lightbox',
        ],
        'gif'  => [
            'viewer' => 'lightbox',
        ],
        'txt'  => [
            'viewer' => 'normal',
        ],
    ];

    /**
     * It's for caching expires timestamp.
     *
     * @var null|int
     */
    private $s3Expires = null;

    public function uploadImage($data, $path, $options = array(), $htmlOptions = array())
    {
        $options += array('urlize' => false);
        /** @noinspection PhpDeprecationInspection */
        return $this->output($this->Html->image($this->uploadUrl($data, $path, $options), $htmlOptions));
    }

    public function uploadLink($title, $data, $field, $urlOptions = array(), $htmlOptions = array())
    {
        $urlOptions += array('style' => 'original', 'urlize' => true);
        return $this->Html->link($title, $this->uploadUrl($data, $field, $urlOptions), $htmlOptions);
    }

    /**
     * $open_type: "viewer" or "download"
     *
     * @param array  $data
     * @param string $open_type
     *
     * @return null|string
     */
    public function attachedFileUrl($data, $open_type = "viewer")
    {
        if (isset($data['AttachedFile'])) {
            $data = $data['AttachedFile'];
        }

        $url = $this->uploadUrl($data, 'AttachedFile.attached');
        //officeファイルの場合は、office viewerのリンクに変換
        if ($open_type == "viewer" && viaIsSet($this->ext_settings[$data['file_ext']]['viewer']) == 'office_viewer') {
            $url = OOV_BASE_URL . urlencode($url);
        }
        return $url;
    }

    public function getAttachedFileName($data, $exclude_ext = true)
    {
        if (isset($data['AttachedFile'])) {
            $data = $data['AttachedFile'];
        }
        if (!$exclude_ext) {
            return $data['attached_file_name'];
        }
        // 日本語ファイル名に対応するためロケールを一時的に変更する
        $orig_locale = setlocale(LC_CTYPE, 0);
        setlocale(LC_CTYPE, 'C');
        $file_name = pathinfo($data['attached_file_name']);
        setlocale(LC_CTYPE, $orig_locale);
        return h($file_name['filename']);
    }

    public function getCssOfFileIcon($data)
    {
        if (isset($data['AttachedFile'])) {
            $data = $data['AttachedFile'];
        }
        $ext = $data['file_ext'];
        if ($class = Hash::get($this->ext_settings, "$ext.icon_class")) {
            return $class;
        }
        return 'fa-file-o file-other-icon';
    }

    public function isCanPreview($data)
    {
        if (isset($data['AttachedFile'])) {
            $data = $data['AttachedFile'];
        }
        $ext = $data['file_ext'];
        if (Hash::get($this->ext_settings, "$ext.viewer")) {
            return true;
        }
        return false;
    }

    /**
     * Get uploaded file URl
     *
     * @param       $data
     * @param       $field
     * @param array $options
     * @param bool  $isDefImgFromCloud
     * Until Angular renewal, default image path refer goalous repo assets.
     * (e.g. <img src="/img/no-image-circle.jpg">)
     * But from renewal, default images will be placed in s3, not assets.
     * If old Goalous, set false to $isDefImgFromCloud,
     * else if from renewal (API v2 later), set true to it.
     *
     * @return mixed|null|string
     */
    public function uploadUrl($data, $field, $options = array(), $isDefImgFromCloud = false)
    {
        $options += array('style' => 'original', 'urlize' => true);
        list($model, $field) = explode('.', $field);
        $id = null;
        $filename = null;
        if (is_array($data)) {
            if (isset($data[$model])) {
                if (isset($data[$model]['id'])) {
                    $id = $data[$model]['id'];
                    $filename = $data[$model][$field . '_file_name'];
                }
            } elseif (isset($data['id'])) {
                $id = $data['id'];
                $filename = $data[$field . '_file_name'];
            }
        }

        $hash = Security::hash($model . $id . $field . $filename . $options['style'] . $options['urlize']);
        if (isset($this->cache[$hash])) {
            return $this->cache[$hash];
        }

        $defaultImgKey = $isDefImgFromCloud ? 's3_default_url' : 'default_url';
        if ($id && !empty($filename)) {
            $settings = UploadBehavior::interpolate($model, $id, $field, $filename, $options['style'],
                array('webroot' => ''));
            $url = isset($settings['url']) ? $settings['url'] : $settings['path'];
        }
        if (empty($url)) {
            $settings = UploadBehavior::interpolate($model, null, $field, null, $options['style'],
                array('webroot' => ''));
            $url = isset($settings[$defaultImgKey]) ? $settings[$defaultImgKey] : null;
        }

        $url = $this->substrS3Url($url);

        $this->cache[$hash] = $url;
        return $url;
    }

    /**
     * Get default image URLs
     *
     * @param       $field
     * @param array $options
     * @param bool  $isDefImgFromCloud
     *
     * @return string|null
     */
    public function getDefaultUrl($field, $options = array(), $isDefImgFromCloud = false)
    {
        $options += array('style' => 'original', 'urlize' => true);

        list($model, $field) = explode('.', $field);

        $defaultImgKey = $isDefImgFromCloud ? 's3_default_url' : 'default_url';

        if (empty($url)) {
            $settings = UploadBehavior::interpolate($model, null, $field, null, $options['style'],
                array('webroot' => ''));
            $url = isset($settings[$defaultImgKey]) ? $settings[$defaultImgKey] : null;
        }

        $url = $this->substrS3Url($url);

        return $url;
    }

    /**
     * Returns appropriate extension for given mimetype.
     *
     * @param null $mimeType
     *
     * @return void
     * @internal param string $mime Mimetype
     * @author   Bjorn Post
     */
    public function extension($mimeType = null)
    {
        $knownMimeTypes = array(
            'ai'      => 'application/postscript',
            'bcpio'   => 'application/x-bcpio',
            'bin'     => 'application/octet-stream',
            'ccad'    => 'application/clariscad',
            'cdf'     => 'application/x-netcdf',
            'class'   => 'application/octet-stream',
            'cpio'    => 'application/x-cpio',
            'cpt'     => 'application/mac-compactpro',
            'csh'     => 'application/x-csh',
            'csv'     => 'application/csv',
            'dcr'     => 'application/x-director',
            'dir'     => 'application/x-director',
            'dms'     => 'application/octet-stream',
            'doc'     => 'application/msword',
            'drw'     => 'application/drafting',
            'dvi'     => 'application/x-dvi',
            'dwg'     => 'application/acad',
            'dxf'     => 'application/dxf',
            'dxr'     => 'application/x-director',
            'eps'     => 'application/postscript',
            'exe'     => 'application/octet-stream',
            'ez'      => 'application/andrew-inset',
            'flv'     => 'video/x-flv',
            'gtar'    => 'application/x-gtar',
            'gz'      => 'application/x-gzip',
            'bz2'     => 'application/x-bzip',
            '7z'      => 'application/x-7z-compressed',
            'hdf'     => 'application/x-hdf',
            'hqx'     => 'application/mac-binhex40',
            'ips'     => 'application/x-ipscript',
            'ipx'     => 'application/x-ipix',
            'js'      => 'application/x-javascript',
            'latex'   => 'application/x-latex',
            'lha'     => 'application/octet-stream',
            'lsp'     => 'application/x-lisp',
            'lzh'     => 'application/octet-stream',
            'man'     => 'application/x-troff-man',
            'me'      => 'application/x-troff-me',
            'mif'     => 'application/vnd.mif',
            'ms'      => 'application/x-troff-ms',
            'nc'      => 'application/x-netcdf',
            'oda'     => 'application/oda',
            'pdf'     => 'application/pdf',
            'pgn'     => 'application/x-chess-pgn',
            'pot'     => 'application/mspowerpoint',
            'pps'     => 'application/mspowerpoint',
            'ppt'     => 'application/mspowerpoint',
            'ppz'     => 'application/mspowerpoint',
            'pre'     => 'application/x-freelance',
            'prt'     => 'application/pro_eng',
            'ps'      => 'application/postscript',
            'roff'    => 'application/x-troff',
            'scm'     => 'application/x-lotusscreencam',
            'set'     => 'application/set',
            'sh'      => 'application/x-sh',
            'shar'    => 'application/x-shar',
            'sit'     => 'application/x-stuffit',
            'skd'     => 'application/x-koan',
            'skm'     => 'application/x-koan',
            'skp'     => 'application/x-koan',
            'skt'     => 'application/x-koan',
            'smi'     => 'application/smil',
            'smil'    => 'application/smil',
            'sol'     => 'application/solids',
            'spl'     => 'application/x-futuresplash',
            'src'     => 'application/x-wais-source',
            'step'    => 'application/STEP',
            'stl'     => 'application/SLA',
            'stp'     => 'application/STEP',
            'sv4cpio' => 'application/x-sv4cpio',
            'sv4crc'  => 'application/x-sv4crc',
            'svg'     => 'image/svg+xml',
            'svgz'    => 'image/svg+xml',
            'swf'     => 'application/x-shockwave-flash',
            't'       => 'application/x-troff',
            'tar'     => 'application/x-tar',
            'tcl'     => 'application/x-tcl',
            'tex'     => 'application/x-tex',
            'texi'    => 'application/x-texinfo',
            'texinfo' => 'application/x-texinfo',
            'tr'      => 'application/x-troff',
            'tsp'     => 'application/dsptype',
            'unv'     => 'application/i-deas',
            'ustar'   => 'application/x-ustar',
            'vcd'     => 'application/x-cdlink',
            'vda'     => 'application/vda',
            'xlc'     => 'application/vnd.ms-excel',
            'xll'     => 'application/vnd.ms-excel',
            'xlm'     => 'application/vnd.ms-excel',
            'xls'     => 'application/vnd.ms-excel',
            'xlw'     => 'application/vnd.ms-excel',
            'zip'     => 'application/zip',
            'aif'     => 'audio/x-aiff',
            'aifc'    => 'audio/x-aiff',
            'aiff'    => 'audio/x-aiff',
            'au'      => 'audio/basic',
            'kar'     => 'audio/midi',
            'mid'     => 'audio/midi',
            'midi'    => 'audio/midi',
            'mp2'     => 'audio/mpeg',
            'mp3'     => 'audio/mpeg',
            'mpga'    => 'audio/mpeg',
            'ra'      => 'audio/x-realaudio',
            'ram'     => 'audio/x-pn-realaudio',
            'rm'      => 'audio/x-pn-realaudio',
            'rpm'     => 'audio/x-pn-realaudio-plugin',
            'snd'     => 'audio/basic',
            'tsi'     => 'audio/TSP-audio',
            'wav'     => 'audio/x-wav',
            'asc'     => 'text/plain',
            'c'       => 'text/plain',
            'cc'      => 'text/plain',
            'css'     => 'text/css',
            'etx'     => 'text/x-setext',
            'f'       => 'text/plain',
            'f90'     => 'text/plain',
            'h'       => 'text/plain',
            'hh'      => 'text/plain',
            'htm'     => 'text/html',
            'html'    => 'text/html',
            'm'       => 'text/plain',
            'rtf'     => 'text/rtf',
            'rtx'     => 'text/richtext',
            'sgm'     => 'text/sgml',
            'sgml'    => 'text/sgml',
            'tsv'     => 'text/tab-separated-values',
            'tpl'     => 'text/template',
            'txt'     => 'text/plain',
            'xml'     => 'text/xml',
            'avi'     => 'video/x-msvideo',
            'fli'     => 'video/x-fli',
            'mov'     => 'video/quicktime',
            'movie'   => 'video/x-sgi-movie',
            'mpe'     => 'video/mpeg',
            'mpeg'    => 'video/mpeg',
            'mpg'     => 'video/mpeg',
            'qt'      => 'video/quicktime',
            'viv'     => 'video/vnd.vivo',
            'vivo'    => 'video/vnd.vivo',
            'gif'     => 'image/gif',
            'ief'     => 'image/ief',
            'jpe'     => 'image/jpeg',
            'jpeg'    => 'image/jpeg',
            'jpg'     => 'image/jpeg',
            'pbm'     => 'image/x-portable-bitmap',
            'pgm'     => 'image/x-portable-graymap',
            'png'     => 'image/png',
            'pnm'     => 'image/x-portable-anymap',
            'ppm'     => 'image/x-portable-pixmap',
            'ras'     => 'image/cmu-raster',
            'rgb'     => 'image/x-rgb',
            'tif'     => 'image/tiff',
            'tiff'    => 'image/tiff',
            'xbm'     => 'image/x-xbitmap',
            'xpm'     => 'image/x-xpixmap',
            'xwd'     => 'image/x-xwindowdump',
            'ice'     => 'x-conference/x-cooltalk',
            'iges'    => 'model/iges',
            'igs'     => 'model/iges',
            'mesh'    => 'model/mesh',
            'msh'     => 'model/mesh',
            'silo'    => 'model/mesh',
            'vrml'    => 'model/vrml',
            'wrl'     => 'model/vrml',
            'mime'    => 'www/mime',
            'pdb'     => 'chemical/x-pdb',
            'xyz'     => 'chemical/x-pdb'
        );

        /** @noinspection PhpInconsistentReturnPointsInspection */
        return array_search($mimeType, $knownMimeTypes);
    }

    public function substrS3Url($url)
    {
        $trimed_url = str_replace(S3_TRIM_PATH, "", $url);
        //$url = S3_BASE_URL . DS . S3_ASSETS_BUCKET . DS . $trimed_url;
        $url = $this->gs_prepareS3URL($trimed_url, S3_ASSETS_BUCKET);

        return $url;
    }

    function gs_getStringToSign($request_type, $expires, $uri)
    {
        return "$request_type\n\n\n$expires\n$uri";
    }

    function gs_encodeSignature($s, $key)
    {
        $s = utf8_encode($s);
        $s = hash_hmac('sha1', $s, $key, true);
        $s = base64_encode($s);
        return urlencode($s);
    }

    function gs_prepareS3URL($file, $bucket)
    {
        $awsKeyId = AWS_ACCESS_KEY; // this is the non-secret key ID.
        $awsSecretKey = AWS_SECRET_KEY; // this is the SECRET access key!

        $file = rawurlencode($file);
        $file = $this->getLocalPrefix() . '/' . str_replace('%2F', '/', $file);

        $path = $bucket . $file;

        $expires = $this->getS3Expires();
        $stringToSign = $this->gs_getStringToSign('GET', $expires, "/$path");
        $signature = $this->gs_encodeSignature($stringToSign, $awsSecretKey);

        $url = "https://$bucket.s3.amazonaws.com" . $file;

        $url .= '?AWSAccessKeyId=' . $awsKeyId
            . '&Expires=' . $expires
            . '&Signature=' . $signature;

        return $url;
    }

    /**
     * calculating expires about s3.
     * - border hour based.
     * - if border hour is 6 and current time is 09:00 then result will be 12:00
     *
     * @param int $expiresBorderHour It should be 1 to 24
     * @param int $targetTimestamp
     *
     * @return int
     * @throws Exception
     */
    function getS3Expires($expiresBorderHour = 6, $targetTimestamp = REQUEST_TIMESTAMP): int
    {
        if ($expiresBorderHour < 1 || $expiresBorderHour > 24) {
            throw new Exception('Invalid argument: $expiresBorderHour. It received ' . $expiresBorderHour . '. But it should be 1 to 24');
        }
        // using the property as cache
        if ($this->s3Expires) {
            return $this->s3Expires;
        }

        $startTodayTimestamp = strtotime("today", $targetTimestamp);
        $targetExpires = 0;

        // increment hour from start the day, if it's over current time, expires will be that.
        for ($h = $expiresBorderHour; $h <= 24; $h += $expiresBorderHour) {
            $targetExpires = strtotime("+{$h} hours", $startTodayTimestamp);
            if ($targetTimestamp < $targetExpires) {
                break;
            }
        }
        $this->s3Expires = $targetExpires;
        return $this->s3Expires;
    }

    /**
     * Get special prefix when uploading from local
     *
     * @return string
     */
    private function getLocalPrefix(): string
    {
        if (ENV_NAME == "local") {
            if (empty(AWS_S3_BUCKET_USERNAME)) {
                throw new RuntimeException("Please define AWS_S3_BUCKET_USERNAME");
            }
            return "/" . AWS_S3_BUCKET_USERNAME;
        }
        return '';
    }
}
