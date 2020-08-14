<?php
App::uses('BaseRedisClient', 'Lib/Cache/Redis');
App::uses('InterfaceRedisClient', 'Lib/Cache/Redis');
App::uses('TwoFATokenData', 'Lib/Cache/Redis/TwoFAToken');
App::uses('TwoFATokenKey', 'Lib/Cache/Redis/TwoFAToken');

class TwoFATokenClient extends BaseRedisClient implements InterfaceRedisClient
{
    /** @var int Default 2fa token timeout in seconds */
    private const DEFAULT_TIMEOUT = 900;

    /**
     * Read data from the Redis
     * null return if $key is not exist
     *
     * @param TwoFATokenKey $key
     *
     * @return TwoFATokenData|null
     */
    public function read(TwoFATokenKey $key): ?TwoFATokenData
    {
        $readData = $this->getRedis()->get($key->toKey());
        if (false === $readData) {
            return null;
        }
        // UnSerialize the data when returning
        // (Do not do in *Data class, hide the write/save process for user to keep code simple)
        return TwoFATokenData::parseArray(msgpack_unpack($readData));
    }

    /**
     * Store data into the Redis
     *
     * @param TwoFATokenKey  $key
     * @param TwoFATokenData $data
     *
     * @return bool
     */
    public function write(TwoFATokenKey $key, TwoFATokenData $data): bool
    {
        return $this->getRedis()->set(
            $key->toKey(),
            // Serialize the data when writing
            // (Do not do in *Data class, hide the write/save process for user to keep code simple)
            msgpack_pack($data->toArray()),
            self::DEFAULT_TIMEOUT
        );
    }

    /**
     * Delete the specified keys
     *
     * @param TwoFATokenKey $key
     *
     * @return int deleted caches
     */
    public function del(TwoFATokenKey $key): int
    {
        return $this->getRedis()->del($key->toKey());
    }
}
