<?php
App::uses('BaseRedisClient', 'Lib/Cache/Redis');
App::uses('InterfaceRedisClient', 'Lib/Cache/Redis');

class PaymentFlagClient extends BaseRedisClient implements InterfaceRedisClient
{
    /**
     * Read data whether the given flag exists
     *
     * @param NotificationFlagKey $key
     *
     * @return bool
     */
    public function read(PaymentFlagKey $key): string
    {
        $returnValue = $this->getRedis()->get($key->toRedisKey());

        return $returnValue ?? -1;
    }

    /**
     * Write data whether the given flag exists
     *
     * @param NotificationFlagKey $key
     *
     * @return bool TRUE on successful write
     */
    public function write(PaymentFlagKey $key, int $value): bool
    {
        return $this->getRedis()->set($key->toRedisKey(), $value);
    }

    /**
     * Read data set
     *
     * @param NotificationFlagKey $key
     *
     * @return bool
     */
    public function readSet(PaymentFlagKey $key): array
    {
        $returnValue = $this->getRedis()->smembers($key->toRedisKey());

        return $returnValue ?? -1;
    }

    /**
     * Write data set
     *
     * @param NotificationFlagKey $key
     *
     * @return bool TRUE on successful write
     */
    public function writeSet(PaymentFlagKey $key, array $value): bool
    {
        return $this->getRedis()->sadd($key->toRedisKey(), ...$value);
    }

    /**
     * delete data set
     *
     * @param NotificationFlagKey $key
     *
     * @return bool TRUE on successful write
     */
    public function deleteSet(PaymentFlagKey $key, array $value): int
    {
        return $this->getRedis()->srem($key->toRedisKey(), ...$value);
    }

    /**
     * Delete given flag
     *
     * @param NotificationFlagKey $key
     *
     * @return int
     */
    public function del(PaymentFlagKey $key): int
    {
        return $this->getRedis()->del($key->toRedisKey());
    }
    /**
     * check if now is in the peroid for charge on signup
     * can only be called from app with request->current_team_id
     *
     * @param $baseDay team payment base day, $startDate period start date
     *
     * @return bool
     */
    public function is_in_period(int $baseDay, string $startDate, int $teamId): bool
    {
        /** @var Team $Team */
        $Team = ClassRegistry::init("Team");
        $timezone = $Team->findById($teamId)['Team']['timezone'];
        $date = GoalousDateTime::now()->setTimeZoneByHour($timezone)->format('Ymd');

        $res = False;
        $startDateDay = date('d', strtotime($startDate));
        $startDateDate = date('Ymd', strtotime($startDate));
        $startDateMonth = date('Ym01', strtotime($startDate));
        if ($startDateDay == $baseDay)
        {
            $res = True;
            $endDay = $startDateDate;
        } elseif (intval($startDateDay) < $baseDay)
        {
            $endDay = date('Ymd', strtotime($startDateMonth . "+" .($baseDay-1). " days"));

        }else {
            $endDay = date('Ymd', strtotime($startDateMonth . "+" .($baseDay - 1). " days"));
            $endDay = date('Ymd', strtotime($endDay . "+1 month"));
        }
        if ($endDay <= $date){
            $res = True;
        }

        return $res;
    }
}
