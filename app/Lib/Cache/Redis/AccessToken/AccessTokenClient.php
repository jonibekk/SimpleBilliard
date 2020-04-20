<?php
App::uses('AccessTokenData', 'Lib/Cache/Redis/AccessToken');
App::uses('AccessTokenKey', 'Lib/Cache/Redis/AccessToken');
App::uses('BaseRedisClient', 'Lib/Cache/Redis');
App::uses('InterfaceRedisClient', 'Lib/Cache/Redis');

class AccessTokenClient extends BaseRedisClient implements InterfaceRedisClient
{
    /**
     * @param AccessTokenKey $key
     *
     * @return AccessTokenData|null
     */
    public function read(AccessTokenKey $key)
    {
        $readData = $this->getRedis()->get($key->get());
        if (false === $readData) {
            return null;
        }
        $data = msgpack_unpack($readData);

        return AccessTokenData::parseFromArray($data);
    }

    /**
     * @param AccessTokenKey  $key
     * @param AccessTokenData $data
     *
     * @return bool
     */
    public function write(AccessTokenKey $key, AccessTokenData $data): bool
    {
        $cacheValue = msgpack_pack($data->toArray());
        return $this->getRedis()->set($key->get(), $cacheValue, $data->getTimeToLive());
    }

    /**
     * Return the existing key by array
     *
     * @param AccessTokenKey $key
     *
     * @return array
     */
    public function keys(AccessTokenKey $key): array
    {
        return $this->getRedis()->keys($key->get());
    }

    /**
     * @param AccessTokenKey $key
     *
     * @return int existing key counts
     */
    public function count(AccessTokenKey $key): int
    {
        return count($this->keys($key));
    }

    /**
     * Delete the specified keys
     *
     * @param AccessTokenKey $key
     *
     * @return int deleted caches
     */
    public function del(AccessTokenKey $key): int
    {
        return $this->getRedis()->del(iterator_to_array($this->keysWithOutPrefix($key)));
    }

    /**
     * Return the array of "existing key removed prefix"
     *
     * @param AccessTokenKey $key
     *
     * @return Generator
     */
    private function keysWithOutPrefix(AccessTokenKey $key): Generator
    {
        foreach ($this->getRedis()->keys($key->get()) as $keyWithPrefix) {
            yield $this->removePrefixFromKey($keyWithPrefix);
        }
    }
}
