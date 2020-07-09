<?php

use MyCLabs\Enum\Enum;
use \Goalous\Enum\DataType\DataType as DataType;

/**
 * @method BANNER_ALERT_SERVICE_USE_STATUS_HIDE() static
 * @method GOAL_CREATE_GUIDE_HIDE() static
 * Class TeamUserPayloadType
 */
class TeamUserPayloadType extends Enum
{
    const BANNER_ALERT_SERVICE_USE_STATUS_HIDE = 'BANNER_ALERT_SERVICE_USE_STATUS_HIDE';
    const GOAL_CREATE_GUIDE_HIDE = 'GOAL_CREATE_GUIDE_HIDE';

    public static $keySettings = [
        self::BANNER_ALERT_SERVICE_USE_STATUS_HIDE => [
            'type' => DataType::BOOL,
            'ttl'  => 86400 * 3,
        ],
        self::GOAL_CREATE_GUIDE_HIDE => [
            'type' => DataType::BOOL,
            'ttl'  => 86400 * 7,
        ],
    ];

    public static function keysLowerCase(): array
    {
        return array_map(function (string $key) {
            return strtolower($key);
        }, parent::keys());
    }

    public function getKeyLowerCase()
    {
        return strtolower(parent::getKey());
    }

    public static function getKeySetting(self $key): array
    {
        return self::$keySettings[$key->getValue()];
    }

    public static function castByKey(self $key, $value)
    {
        $type = self::$keySettings[$key->getValue()]['type'];
        switch ($type) {
            case DataType::BOOL:
                return boolval($value);
            case DataType::INT:
                return intval($value);
            case DataType::FLOAT:
                return floatval($value);
        }
        return strval($value);
    }
}
