<?php

App::uses('ConnectionManager', 'Model');

/**
 * Class BaseRedisClient
 *
 * WARNING:
 *     Redis key name will automatically added the env name
 *     see __construct() in the app/Config/database.php
 *     redis['prefix'] is the part of it
 */
abstract class BaseRedisClient
{
    /**
     * @var RedisSource
     */
    private static $redis;

    private static $CONFIG_NAME = 'redis';

    /**
     * @return Redis
     */
    public function getRedis(): RedisSource
    {
        if (is_null(self::$redis)) {
            self::$redis = $this->getRedisConnection();
        }
        return self::$redis;
    }

    protected function getPrefix(): string
    {
        return $this->getRedis()->getOption(Redis::OPT_PREFIX);
    }

    private function getRedisConnection(): RedisSource
    {
        return ConnectionManager::getDataSource(self::$CONFIG_NAME);
    }

    protected function removePrefixFromKey(string $key): string
    {
        $prefix = $this->getPrefix();

        if (0 === strpos($key, $prefix)) {
            return substr($key, strlen($prefix));
        }
        return $key;
    }

    public static function setRedisConnection(string $connectionName)
    {
        self::$CONFIG_NAME = $connectionName;
    }
}