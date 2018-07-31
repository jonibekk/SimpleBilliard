<?php
App::import('Lib/Upload/Uploader', 'BaseUploader');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/07/30
 * Time: 18:16
 */
class UploaderFactory
{
    /**
     * Automatically generate uploader class based on environment setting
     *
     * @param int    $teamId
     * @param int    $userId
     * @param string $webroot
     *
     * @return Uploader
     */
    public static function generate(int $teamId, int $userId, string $webroot = ""): Uploader
    {
        if (empty($teamId) || empty($userId)) {
            throw new InvalidArgumentException();
        }
        if (empty($webroot)) {
            $webroot = preg_replace('/\/$/', '', WWW_ROOT);
        }
        if (PUBLIC_ENV) {
            return new S3Uploader($teamId, $userId, $webroot);
        } else {
            return new LocalUploader($teamId, $userId, $webroot);
        }
    }

}