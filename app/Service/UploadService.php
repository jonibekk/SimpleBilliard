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

use Goalous\Exception as GlException;

class UploadService extends AppService
{
    const MAX_BUFFER_COUNT = 20;
    const FILE_LIFESPAN = 1800; //Seconds

    /**
     * Add a file into redis
     *
     * @param int    $userId
     * @param int    $teamId
     * @param string $encodedFile
     *
     * @return string
     */
    public function buffer(int $userId, int $teamId, string $encodedFile, string $fileName): string
    {
        $RedisClient = new UploadRedisClient();

        $UploadedFile = new UploadedFile($encodedFile, $fileName);

        if (!UploadValidator::validate($UploadedFile)) {
            throw new GlException\Upload\UploadFailedException();
        }

        $process = new UploadPreProcess();

        $file = $process->process($UploadedFile);

        $RedisKey = new UploadRedisKey($userId, $teamId, $file->getUUID());

        $RedisData = new UploadRedisData($file);

        $RedisData->withTimeToLive(self::FILE_LIFESPAN);

        if ($RedisClient->write($RedisKey, $RedisData)) {
            $overLimitCount = $this->countBuffer($userId, $teamId) - self::MAX_BUFFER_COUNT;
            while ($overLimitCount-- > 0) {
                $oldestKey = $this->findOldest($userId, $teamId);
                $RedisClient->del($oldestKey);
            }
            return $file->getUUID();
        } else {
            throw new GlException\Upload\Redis\UploadRedisException();
        }
    }

    /**
     * Read data from REDIS
     *
     * @param int $userId
     * @param int $teamId
     * @param     $uuid $key 13 char HEX UUID
     *
     * @return UploadedFile |null
     */
    public function read(int $userId, int $teamId, string $uuid)
    {
        if (preg_match("/[A-Fa-f0-9]{14}.[A-Fa-f0-9]{8}/", $uuid) == 0) {
            throw new InvalidArgumentException(("Invalid FILE UUID"));
        }

        $RedisClient = new UploadRedisClient();
        $RedisKey = new UploadRedisKey($userId, $teamId, $uuid);

        $rawData = $RedisClient->read($RedisKey);

        if (empty($rawData)) {
            return null;
        }

        return $rawData->getFile();
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

        //If no uuid is given, delete all entries from given user in given team
        if (empty($uuid)) {
            $keys = $RedisClient->search($RedisKey->getWithoutID());
            foreach ($keys as $key) {
                $RedisClient->del($key);
            }
        } else {
            $res = $RedisClient->del($RedisKey);
            if (empty($res)) {
                throw new GlException\GoalousNotFoundException("Specified buffered file not found");
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
        //TODO GL-7171
//        $RedisClient = new UploadRedisClient();
//
//        foreach ($mainData as $key => &$value) {
//            if (preg_match("/FILE [0-9a-fA-F]{13}/", $value)) {
//
//            }
//        }
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
        //TODO GL-7171
    }

    /**
     * Check whether number of files in the buffer is under the limit for current user in current team
     *
     * @param int $userId
     * @param int $teamId
     *
     * @return int
     */
    public function countBuffer(int $userId, int $teamId): int
    {
        $RedisClient = new UploadRedisClient();

        $RedisKey = new UploadRedisKey($userId, $teamId, "");

        $keys = $RedisClient->search($RedisKey->getWithoutID());

        return count($keys);
    }

    private function findOldest(int $userId, int $teamId): string
    {
        $RedisClient = new UploadRedisClient();
        $RedisKey = new UploadRedisKey($userId, $teamId, "");

        $keys = $RedisClient->search($RedisKey->getWithoutID());

        $oldestTime = 0;
        $oldestKey = "";

        foreach ($keys as $key) {
            $data = $RedisClient->read($key);
            if (!empty($data)) {
                $ttl = $RedisClient->getTtl($key);
                if (empty($oldestTime) || $ttl < $oldestTime) {
                    $oldestTime = $ttl;
                    $oldestKey = $key;
                }
            }
        }
        return $oldestKey;
    }
}