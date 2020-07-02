<?php
App::uses('TeamUserPayloadRedisKey', 'Lib/Cache/Redis/TeamUserPayload');
App::uses('TeamUserPayload', 'Lib/Cache/Redis/TeamUserPayload');
App::uses('TeamUserPayloadType', 'Lib/Cache/Redis/TeamUserPayload');
App::uses('BaseRedisClient', 'Lib/Cache/Redis');
App::uses('InterfaceRedisClient', 'Lib/Cache/Redis');

use \Goalous\Enum\DataType\DataType as DataType;

class TeamUserPayloadClient extends BaseRedisClient implements InterfaceRedisClient
{
    public function read(TeamUserPayloadRedisKey $baseKey): TeamUserPayload
    {
        $mgetKeys = [];
        $dataTypes = TeamUserPayloadType::values();
        foreach ($dataTypes as $dataType) {
            array_push($mgetKeys, $this->getFullKey($baseKey, $dataType));
        }

        // Use mget() to get the multiple cached values in Redis for performance reason.
        $readData = $this->getRedis()->mget($mgetKeys);

        $payloads = [];
        $i = 0;
        foreach ($dataTypes as $dataType) {
            $value = $readData[$i];
            $i++;
            if (false === $value) {
                continue;
            }
            $payloads[$dataType->getKeyLowerCase()] = TeamUserPayloadType::castByKey($dataType, $value);
        }

        $data = new TeamUserPayload($payloads);
        return $data;
    }

    public function write(TeamUserPayloadRedisKey $baseKey, TeamUserPayloadType $teamUserPayloadType, $value): bool
    {
        $setting = TeamUserPayloadType::getKeySetting($teamUserPayloadType);
        $dataType = $setting['type'];
        $ttl = $setting['ttl'];
        $valueCasted = $value;
        switch ($dataType) {
            case DataType::BOOL:
            case DataType::INT:
                $valueCasted = intval($value);
                break;
            case DataType::FLOAT:
                $valueCasted = floatval($value);
                break;
        }
        return $this->getRedis()->set($this->getFullKey($baseKey, $teamUserPayloadType), $valueCasted, $ttl);
    }

    private function getFullKey(TeamUserPayloadRedisKey $key, TeamUserPayloadType $teamUserPayloadType): string
    {
        return sprintf('%s:%s', $key->get(), $teamUserPayloadType->getKeyLowerCase());
    }
}
