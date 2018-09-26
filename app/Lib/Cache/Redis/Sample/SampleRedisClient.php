<?php
App::uses('SampleRedisData', 'Lib/Cache/Redis/Sample');
App::uses('SampleRedisKey', 'Lib/Cache/Redis/Sample');
App::uses('BaseRedisClient', 'Lib/Cache/Redis');
App::uses('InterfaceRedisClient', 'Lib/Cache/Redis');

/**
 * Class SampleRedisClient
 *
 * This is a sample code of redis client.
 * Do not use this in a product code.
 *
 * Referrer this SampleRedisClient/SampleRedisKey/SampleRedisData when creating new Redis caching.
 * Also please referrer the SampleRedisClientTest for the usage.
 */
class SampleRedisClient extends BaseRedisClient implements InterfaceRedisClient
{
    /**
     * Read data from the Redis
     * null return if $key is not exist
     *
     * @param SampleRedisKey $key
     *
     * @return SampleRedisData|null
     */
    public function read(SampleRedisKey $key)
    {
        $readData = $this->getRedis()->get($key->get());
        if (false === $readData) {
            return null;
        }
        // UnSerialize the data when returning
        // (Do not do in *Data class, hide the write/save process for user to keep code simple)
        return new SampleRedisData(msgpack_unpack($readData));
    }

    /**
     * Store data into the Redis
     *
     * @param SampleRedisKey  $key
     * @param SampleRedisData $data
     *
     * @return bool
     */
    public function write(SampleRedisKey $key, SampleRedisData $data): bool
    {
        return $this->getRedis()->set(
            $key->get(),
            // Serialize the data when writing
            // (Do not do in *Data class, hide the write/save process for user to keep code simple)
            msgpack_pack($data->getStringData())
        );
    }

    /**
     * Return the existing key by array
     *
     * @param SampleRedisKey $key
     *
     * @return array
     */
    public function keys(SampleRedisKey $key): array
    {
        return $this->getRedis()->keys($key->get());
    }

    /**
     * Count the existing key
     *
     * Please see the test case of testWildCard
     * @see Test/Lib/Cache/Redis/Sample/SampleRedisClientTest
     *
     * @param SampleRedisKey $key
     *
     * @return int existing key counts
     */
    public function count(SampleRedisKey $key): int
    {
        return count($this->keys($key));
    }

    /**
     * Delete the specified keys
     *
     * @param SampleRedisKey $key
     *
     * @return int deleted caches
     */
    public function del(SampleRedisKey $key): int
    {
        return $this->getRedis()->del(iterator_to_array($this->keysWithOutPrefix($key)));
    }

    /**
     * This function will be a replacing of \Cache::remember()
     *
     * Return cached data if key exists in Redis.
     * If not, run $closure and store in the Redis and return the value
     *
     * Please referrer the test case for usage
     * @see Test/Lib/Cache/Redis/Sample/SampleRedisClientTest
     *
     * @param SampleRedisKey $key
     * @param Closure        $closure
     *
     * @return null|SampleRedisData
     */
    public function remember(SampleRedisKey $key, Closure $closure)
    {
        $data = $this->read($key);
        if (!is_null($data)) {
            return $data;
        }
        $data = $closure();
        $this->write($key, $data);
        return $data;
    }

    /**
     * Return the array of "existing key removed prefix"
     *
     * @param SampleRedisKey $key
     *
     * @return Generator
     */
    private function keysWithOutPrefix(SampleRedisKey $key): Generator
    {
        foreach ($this->getRedis()->keys($key->get()) as $keyWithPrefix) {
            yield $this->removePrefixFromKey($keyWithPrefix);
        }
    }
}
