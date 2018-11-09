<?php
App::import('Lib/DataExtender', 'BaseExtender');
App::import('Lib/DataExtender/Extension', "UserExtension");
App::import('Lib/DataExtender/Extension', "CommentLikeExtension");
App::import('Lib/DataExtender/Extension', "CommentReadDataExtension");

class CommentExtender extends BaseExtender
{
    const EXTEND_ALL = "ext:comment:all";
    const EXTEND_USER = "ext:comment:user";
    const EXTEND_LIKE = "ext:comment:like";
    const EXTEND_READ = "ext:comment:read";

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
        if ($this->includeExt($extensions, self::EXTEND_LIKE)) {
            /** @var CommentLikeExtension $CommentLikeExtension */
            $CommentLikeExtension = ClassRegistry::init('CommentLikeExtension');
            $CommentLikeExtension->setUserId($userId);
            $data = $CommentLikeExtension->extendMulti($data, "{n}.id", "comment_id");
        }
        if ($this->includeExt($extensions, self::EXTEND_READ)) {
            /** @var CommentReadDataExtension $CommentReadExtension */
            $CommentReadExtension = ClassRegistry::init('CommentReadDataExtension');
            $CommentReadExtension->setUserId($userId);
            $data = $CommentReadExtension->extendMulti($data, "{n}.id", "comment_id");
        }

        return $data;
    }
}
