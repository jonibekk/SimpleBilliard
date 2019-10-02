<?php
App::import('Lib/DataExtender', 'BaseExtender');
App::import('Lib/DataExtender/Extension', "MessageExtension");
App::import('Lib/DataExtender/Extension', "UserExtension");

/**
 * Created by PhpStorm.
 * User: Stephen Raharja
 * Date: 12/18/2018
 * Time: 3:34 PM
 */
class MessageExtender extends BaseExtender
{
    const EXTEND_ALL = "ext:comment_read:all";
    const EXTEND_SENDER = "ext:comment_read:sender";

    public function extend(array $data, int $userId, int $teamId, array $extensions = []): array
    {
        if ($this->includeExt($extensions, self::EXTEND_SENDER)) {
            /** @var UserExtension $UserExtension */
            $UserExtension = ClassRegistry::init('UserExtension');
            $data = $UserExtension->extend($data, "sender_user_id", 'id', 'sender');
        }

        return $data;
    }

    public function extendMulti(array $data, int $userId, int $teamId, array $extensions = []): array
    {
        if ($this->includeExt($extensions, self::EXTEND_SENDER)) {
            /** @var UserExtension $UserExtension */
            $UserExtension = ClassRegistry::init('UserExtension');
            $data = $UserExtension->extendMulti($data, "{n}.sender_user_id", 'id', 'sender');
        }

        return $data;
    }

}