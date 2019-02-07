<?php
App::import('Lib/DataExtender', 'BaseExtender');
App::import('Lib/DataExtender/Extension', "UserExtension");

class NotificationExtender extends BaseExtender
{
    const EXTEND_ALL = "ext:notification:all";
    const EXTEND_USER = 'ext:notification:user';

    public function extend(array $data, int $userId, int $teamId, array $extensions = []): array
    {
        if ($this->includeExt($extensions, self::EXTEND_USER)) {
            /** @var UserExtension $UserExtension */
            $UserExtension = ClassRegistry::init('UserExtension');
            $data = $UserExtension->extend($data, "user_id");
        }

        return $data;
    }

    public function extendMulti(array $data, int $userId, int $teamId, array $extensions = []): array
    {
        if ($this->includeExt($extensions, self::EXTEND_USER)) {
            /** @var UserExtension $UserExtension */
            $UserExtension = ClassRegistry::init('UserExtension');
            $data = $UserExtension->extendMulti($data, "{n}.user_id");
        }

        return $data;
    }


}
