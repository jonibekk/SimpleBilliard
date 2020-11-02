<?php
App::uses('NotificationFlagKey', 'Lib/Cache/Redis/AccessToken');
App::uses('BaseRedisClient', 'Lib/Cache/Redis');
App::uses('InterfaceRedisClient', 'Lib/Cache/Redis');

class NotificationFlagClient extends BaseRedisClient implements InterfaceRedisClient
{
    /**
     * Read data whether the given flag exists
     *
     * @param NotificationFlagKey $key
     *
     * @return bool
     */
    public function read(NotificationFlagKey $key): bool
    {
        $returnValue = $this->getRedis()->get($key->toRedisKey());

        return $returnValue ?? false;
    }

    /**
     * Write data whether the given flag exists
     *
     * @param NotificationFlagKey $key
     *
     * @return bool TRUE on successful write
     */
    public function write(NotificationFlagKey $key): bool
    {
        return $this->getRedis()->set($key->toRedisKey(), true);
    }

    /**
     * Delete given flag
     *
     * @param NotificationFlagKey $key
     *
     * @return int
     */
    public function del(NotificationFlagKey $key): int
    {
        return $this->getRedis()->del($key->toRedisKey());
    }
}