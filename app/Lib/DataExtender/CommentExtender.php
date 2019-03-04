<?php
App::import('Lib/DataExtender', 'BaseExtender');
App::import('Lib/DataExtender/Extension', "UserExtension");
App::import('Lib/DataExtender/Extension', "CommentLikeExtension");
App::import('Lib/DataExtender/Extension', "CommentReadDataExtension");
App::import('Lib/DataExtender/Extension', "MentionsToMeExtension");
App::import('Service', 'CommentService');

class CommentExtender extends BaseExtender
{
    const EXTEND_ALL = "ext:comment:all";
    const EXTEND_USER = "ext:comment:user";
    const EXTEND_LIKE = "ext:comment:like";
    const EXTEND_READ = "ext:comment:read";
    const EXTEND_MENTIONS_TO_ME_IN_BODY = "ext:comment:mentions_to_me_in_body";
    const EXTEND_ATTACHED_FILES = "ext:comment:attached_files";

    public function extend(array $data, int $userId, int $teamId, array $extensions = []): array
    {
        if ($this->includeExt($extensions, self::EXTEND_USER)) {
            /** @var UserExtension $UserExtension */
            $UserExtension = ClassRegistry::init('UserExtension');
            $data = $UserExtension->extend($data, "user_id");
        }
        if ($this->includeExt($extensions, self::EXTEND_LIKE)) {
            /** @var CommentLikeExtension $CommentLikeExtension */
            $CommentLikeExtension = ClassRegistry::init('CommentLikeExtension');
            $CommentLikeExtension->setUserId($userId);
            $data = $CommentLikeExtension->extend($data, "id", "comment_id");
        }
        if ($this->includeExt($extensions, self::EXTEND_READ)) {
            /** @var CommentReadDataExtension $CommentReadExtension */
            $CommentReadExtension = ClassRegistry::init('CommentReadDataExtension');
            $CommentReadExtension->setUserId($userId);
            $data = $CommentReadExtension->extend($data, "id", "comment_id");
        }
        if ($this->includeExt($extensions, self::EXTEND_ATTACHED_FILES)) {
            $data = $this->extendAttachedFiles($data);
        }
        if ($this->includeExt($extensions, self::EXTEND_MENTIONS_TO_ME_IN_BODY)) {
            /** @var MentionsToMeExtension $MentionsToMeExtension */
            $MentionsToMeExtension = ClassRegistry::init('MentionsToMeExtension');
            $MentionsToMeExtension->setUserId($userId);
            $MentionsToMeExtension->setTeamId($teamId);
            $MentionsToMeExtension->setMap([$data['id'] => $data]);
            $data = $MentionsToMeExtension->extend($data, "id");
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
        if ($this->includeExt($extensions, self::EXTEND_ATTACHED_FILES)) {
            foreach ($data as $index => $entry) {
                $data[$index] = $this->extendAttachedFiles($entry);
            }
        }
        if ($this->includeExt($extensions, self::EXTEND_MENTIONS_TO_ME_IN_BODY)) {
            /** @var MentionsToMeExtension $MentionsToMeExtension */
            $MentionsToMeExtension = ClassRegistry::init('MentionsToMeExtension');
            $MentionsToMeExtension->setUserId($userId);
            $MentionsToMeExtension->setTeamId($teamId);
            $MentionsToMeExtension->setMap(Hash::combine($data, '{n}.id', '{n}'));
            $data = $MentionsToMeExtension->extendMulti($data, "{n}.id");
        }

        return $data;
    }

    /**
     * @param array $data
     * @return array
     */
    private function extendAttachedFiles(array $data)
    {
        // Set image url each post photo

        /** @var CommentService $CommentService */
        $CommentService = ClassRegistry::init('CommentService');

        $Upload = new UploadHelper(new View());

        /** @var ImageStorageService $ImageStorageService */
        $ImageStorageService = ClassRegistry::init('ImageStorageService');

        $attachedFiles = $CommentService->getAttachedFiles($data['id']);
        $data['attached_files'] = [];
        if (empty($attachedFiles)) {
            return $data;
        }
        /** @var AttachedFileEntity $file */
        foreach ($attachedFiles as $file) {
            $file['file_url'] = '';
            $file['preview_url'] = '';
            // download url is common.
            // TODO: We should consider to preapare new API or using old processe;
//                    $file['download_url'] = '/posts/attached_file_download/file_id:' . $file['id'];
            $file['download_url'] = '';

            if ($file['file_type'] == AttachedFile::TYPE_FILE_IMG) {
                $file['file_url'] = $ImageStorageService->getImgUrlEachSize($file->toArray(), 'AttachedFile',
                    'attached');
                $data['attached_files'][] = $file->toArray();
            } else {
                $file['preview_url'] = $Upload->attachedFileUrl($file->toArray());
                $data['attached_files'][] = $file->toArray();
            }
        }
        return $data;
    }
}
