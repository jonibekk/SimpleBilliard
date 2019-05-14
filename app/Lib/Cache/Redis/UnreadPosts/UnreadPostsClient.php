<?php
App::uses('BaseRedisClient', 'Lib/Cache/Redis');
App::uses('InterfaceRedisClient', 'Lib/Cache/Redis');
App::uses('UnreadPostsKey', 'Lib/Cache/Redis/UnreadPosts');
App::uses('UnreadPostsData', 'Lib/Cache/Redis/UnreadPosts');

class UnreadPostsClient extends BaseRedisClient implements InterfaceRedisClient
{
    /**
     * Read data from the Redis
     *
     * @param UnreadPostsKey $key
     *
     * @return UnreadPostsData r
     */
    public function read(UnreadPostsKey $key): UnreadPostsData
    {
        $readData = $this->getRedis()->get($key->get());
        if (empty($readData)) {
            return new UnreadPostsData();
        }
        $data = msgpack_unpack($readData)['data'] ?: [];

        return new UnreadPostsData($data);
    }

    /**
     * Store data into the Redis
     *
     * @param UnreadPostsKey  $key
     * @param UnreadPostsData $data
     *
     * @return bool
     */
    public function write(UnreadPostsKey $key, UnreadPostsData $data): bool
    {
        $redisLoad = msgpack_pack(['data' => $data->get()]);
        return $this->getRedis()->set(
            $key->get(), $redisLoad
        );
    }

    /**
     * Delete the specified keys
     *
     * @param SampleRedisKey $key
     *
     * @return int deleted caches
     */
    public function del(UnreadPostsKey $key): int
    {
        return $this->getRedis()->del($key->get());
    }
}