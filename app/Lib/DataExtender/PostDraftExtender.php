<?php
App::import('Lib/DataExtender', 'BaseExtender');
App::import('Lib/DataExtender/Extension', 'PostExtension');
App::import('Lib/DataExtender/Extension', 'UserExtension');

/**
 * Created by PhpStorm.
 * User: stephen
 * Date: 19/01/23
 * Time: 23:32
 */
class PostDraftExtender extends BaseExtender
{
    const EXTEND_ALL = 'ext:post_draft:all';
    const EXTEND_POST = 'ext:post_draft:post';
    const EXTEND_USER = 'ext:post_draft:user';

    public function extend(array $data, int $userId, int $teamId, array $extensions = []): array
    {
        if ($this->includeExt($extensions, self::EXTEND_POST)) {
            /** @var PostExtension $PostExtension */
            $PostExtension = ClassRegistry::init('PostExtension');
            $data = $PostExtension->extend($data, "{n}.post_id");

        }
        if ($this->includeExt($extensions, self::EXTEND_USER)) {
            /** @var UserExtension $UserExtension */
            $UserExtension = ClassRegistry::init('UserExtension');
            $data = $UserExtension->extend($data, "{n}.user_id");
        }

        return $data;
    }

    public function extendMulti(array $data, int $userId, int $teamId, array $extensions = []): array
    {
        if ($this->includeExt($extensions, self::EXTEND_POST)) {
            /** @var PostExtension $PostExtension */
            $PostExtension = ClassRegistry::init('PostExtension');
            $data = $PostExtension->extendMulti($data, "{n}.post_id");

        }
        if ($this->includeExt($extensions, self::EXTEND_USER)) {
            /** @var UserExtension $UserExtension */
            $UserExtension = ClassRegistry::init('UserExtension');
            $data = $UserExtension->extendMulti($data, "{n}.user_id");
        }

        return $data;
    }


}