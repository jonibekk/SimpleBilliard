<?php
App::import('Lib/DataExtender', 'BaseExtender');
App::import('Lib/DataExtender/Extension', "UserExtension");

class PostLikeExtender extends BaseExtender
{
    const EXTEND_ALL = "ext:post_like:all";
    const EXTEND_USER = "ext:post_like:user";

    public function extend(array $data, int $userId, int $teamId, array $extensions = []): array
    {
        throw new RuntimeException("Please implement " . __METHOD__);
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
