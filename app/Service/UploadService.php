<?php
App::import('Service', 'AppService');
App::import('Lib/Upload', 'UploadedFile');
App::import('Lib/Upload', 'UploadPreProcess');
App::import('Lib/Cache/Redis/Upload', 'UploadRedisClient');
App::import('Validator/Lib/Upload', 'UploadValidator');
App::import('Validator/Lib/Upload', 'UploadImageValidator');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/07/25
 * Time: 11:59
 */

use Goalous\Exception\Upload as UploadException;

class UploadService extends AppService
{
    /**
     * Add a file into redis
     *
     * @param int    $userId
     * @param int    $teamId
     * @param string $encodedFile
     *
     * @return string
     */
    public function buffer(int $userId, int $teamId, string $encodedFile): string
    {
        $RedisClient = new UploadRedisClient();

        $UploadedFile = new UploadedFile($encodedFile);

        if (!UploadValidator::validate($UploadedFile)) {
            throw new UploadException\UploadFailedException();
        }

        $process = new UploadPreProcess();

        $file = $process->process($UploadedFile);

        $RedisKey = new UploadRedisKey($userId, $teamId, $file->getUUID());

        $RedisData = new UploadRedisData($file);

        if ($RedisClient->write($RedisKey, $RedisData)) {
            return $file->getUUID();
        } else {
            throw new UploadException\Redis\UploadRedisException();
        }
    }

    /**
     * Delete specific buffered file
     *
     * @param int    $userId
     * @param int    $teamId
     * @param string $uuid UUID of the file. if not given, will delete all buffered files of that user
     *
     * @return bool
     */
    public function delete(int $userId, int $teamId, string $uuid = ""): bool
    {
        $RedisClient = new UploadRedisClient();
        $RedisKey = new UploadRedisKey($userId, $teamId, $uuid);

        if (empty($uuid)) {
            $keys = $RedisClient->search($RedisKey->getWithoutID());
            foreach ($keys as $key) {
                $RedisClient->del($key);
            }
        } else {
            $res = $RedisClient->del($RedisKey);
            if (empty($res)) {
                //TODO Change exception
                throw new NotFoundException("Specified buffered file not found");
            }
        }

        return true;
    }

    /**
     * Replace file UUID with actual file name
     *
     * @param int   $userId
     * @param int   $teamId
     * @param array $mainData
     *
     * @return bool
     */
    public static function link(int $userId, int $teamId, array &$mainData): bool
    {
        $RedisClient = new UploadRedisClient();

        foreach ($mainData as $key => &$value) {
            if (preg_match("/FILE [0-9a-fA-F]+/", $value)) {

            }
        }
    }

    /**
     * Write the file to server
     *
     * @param int    $userId
     * @param int    $teamId
     * @param string $file
     *
     * @return bool
     */
    private function save(int $userId, int $teamId, string $file): bool
    {

    }
}