<?php
App::uses('UploadRedisData', 'Lib/Cache/Redis/Upload');
App::uses('UploadRedisKey', 'Lib/Cache/Redis/Upload');
App::uses('BaseRedisClient', 'Lib/Cache/Redis');
App::uses('InterfaceRedisClient', 'Lib/Cache/Redis');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/07/24
 * Time: 14:25
 */
class UploadRedisClient extends BaseRedisClient implements InterfaceRedisClient
{
    /**
     * @param UploadRedisKey $key
     *
     * @return UploadRedisData|null
     */
    public function read(UploadRedisKey $key)
    {
        $readData = $this->getRedis()->get($key->get());
        if (false === $readData) {
            return null;
        }
        //TODO
        $data = msgpack_unpack($readData);

        return (new UploadRedisData)
            ->withUserAgent($data['user_agent'] ?? '');
    }

    /**
     * @param UploadRedisKey  $key
     * @param UploadRedisData $data
     *
     * @return bool
     */
    public function write(UploadRedisKey $key, UploadRedisData $data): bool
    {
        $cacheValue = msgpack_pack([
            'file_data' => $data->getFile(),
        ]);
        return $this->getRedis()->set($key->get(), $cacheValue, $data->getTimeToLive());
    }

    /**
     * Return the existing key by array
     *
     * @param UploadRedisKey $key
     *
     * @return array
     */
    public function keys(UploadRedisKey $key): array
    {
        return $this->getRedis()->keys($key->get());
    }

    /**
     * @param UploadRedisKey $key
     *
     * @return int existing key counts
     */
    public function count(UploadRedisKey $key): int
    {
        return count($this->keys($key));
    }

    /**
     * Delete the specified keys
     *
     * @param UploadRedisKey $key
     *
     * @return int deleted caches
     */
    public function del(UploadRedisKey $key): int
    {
        return $this->getRedis()->del(iterator_to_array($this->keysWithOutPrefix($key)));
    }

    /**
     * Return the array of "existing key removed prefix"
     *
     * @param UploadRedisKey $key
     *
     * @return Generator
     */
    private function keysWithOutPrefix(UploadRedisKey $key): Generator
    {
        foreach ($this->getRedis()->keys($key->get()) as $keyWithPrefix) {
            yield $this->removePrefixFromKey($keyWithPrefix);
        }
    }

    /**
     * Get list of data from redis with keys containing fragment
     *
     * @param string $fragment Part of the key
     *
     * @return array
     */
    public function search(string $fragment): array
    {
        $keys = $this->getRedis()->keys($fragment);
        return $keys;
    }

}