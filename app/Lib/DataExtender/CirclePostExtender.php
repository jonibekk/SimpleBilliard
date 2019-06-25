<?php
App::import('Lib/DataExtender', 'BaseExtender');
App::import('Service', 'ImageStorageService');
App::import('Lib/DataExtender/Extension', 'UserExtension');
App::import('Lib/DataExtender/Extension', 'CircleExtension');
App::import('Lib/DataExtender/Extension', 'PostLikeExtension');
App::import('Lib/DataExtender/Extension', 'PostSavedExtension');
App::import('Lib/DataExtender/Extension', 'PostReadExtension');
App::import('Service/Paging', 'CommentPagingService');
App::import('Service', 'PostService');
App::import('Service', 'TeamMemberService');
App::uses('TeamTranslationLanguage', 'Model');
App::uses('TeamTranslationStatus', 'Model');


class CirclePostExtender extends BaseExtender
{
    const EXTEND_ALL = "ext:circle_post:all";
    const EXTEND_USER = "ext:circle_post:user";
    const EXTEND_CIRCLE = "ext:circle_post:circle";
    const EXTEND_COMMENTS = "ext:circle_post:comments";
    const EXTEND_POST_SHARE_CIRCLE = "ext:circle_post:share_circle";
    const EXTEND_POST_SHARE_USER = "ext:circle_post:share_user";
    const EXTEND_POST_FILE = "ext:circle_post:file";
    const EXTEND_LIKE = "ext:circle_post:like";
    const EXTEND_SAVED = "ext:circle_post:saved";
    const EXTEND_READ = "ext:circle_post:read";
    const EXTEND_TRANSLATION_LANGUAGE = "ext:circle_post:translation_language";

    const DEFAULT_COMMENT_COUNT = 3;


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
        if ($this->includeExt($extensions, self::EXTEND_CIRCLE)) {
            /** @var CircleExtension $CircleExtension */
            $CircleExtension = ClassRegistry::init('CircleExtension');
            $data = $CircleExtension->extendMulti($data, "{n}.id");
        }
        if ($this->includeExt($extensions, self::EXTEND_COMMENTS)) {
            /** @var CommentPagingService $CommentPagingService */
            $CommentPagingService = ClassRegistry::init('CommentPagingService');

            foreach ($data as &$result) {
                $commentPagingRequest = new PagingRequest();
                $commentPagingRequest->setResourceId(Hash::get($result, 'id'));
                $commentPagingRequest->setCurrentUserId($userId);
                $commentPagingRequest->setCurrentTeamId($teamId);

                $comments = $CommentPagingService->getDataWithPaging($commentPagingRequest, self::DEFAULT_COMMENT_COUNT,
                    CommentExtender::EXTEND_ALL);

                $result['comments'] = $comments;
            }
        }
        if ($this->includeExt($extensions, self::EXTEND_POST_FILE)) {
            // Set image url each post photo
            /** @var ImageStorageService $ImageStorageService */
            $ImageStorageService = ClassRegistry::init('ImageStorageService');

            /** @var PostService $PostService */
            $PostService = ClassRegistry::init('PostService');

            $Upload = new UploadHelper(new View());

            foreach ($data as $index => $entry) {
                $attachedFile = $PostService->getNormalAttachedFiles($entry['id']);
                $data[$index]['attached_files'] = [];
                if (empty($attachedFile)) {
                    continue;
                }
                /** @var AttachedFileEntity $file */
                foreach ($attachedFile as $file) {
                    $file['file_url'] = '';
                    $file['preview_url'] = '';
                    // download url is common.
                    // TODO: We should consider to preapare new API or using old processe;
//                    $file['download_url'] = '/posts/attached_file_download/file_id:' . $file['id'];
                    $file['download_url'] = '';

                    if ($file['file_type'] == AttachedFile::TYPE_FILE_IMG) {
                        $file['file_url'] = $ImageStorageService->getImgUrlEachSize($file->toArray(), 'AttachedFile',
                            'attached');
                        $data[$index]['attached_files'][] = $file->toArray();
                    } else {
                        $file['preview_url'] = $Upload->attachedFileUrl($file->toArray());
                        $data[$index]['attached_files'][] = $file->toArray();
                    }
                }
            }
        }
        if ($this->includeExt($extensions, self::EXTEND_LIKE)) {
            /** @var PostLikeExtension $PostLikeExtension */
            $PostLikeExtension = ClassRegistry::init('PostLikeExtension');
            $PostLikeExtension->setUserId($userId);
            $data = $PostLikeExtension->extendMulti($data, "{n}.id", "post_id");
        }
        if ($this->includeExt($extensions, self::EXTEND_SAVED)) {
            /** @var PostSavedExtension $PostSavedExtension */
            $PostSavedExtension = ClassRegistry::init('PostSavedExtension');
            $PostSavedExtension->setUserId($userId);
            $data = $PostSavedExtension->extendMulti($data, "{n}.id", "post_id");
        }
        if ($this->includeExt($extensions, self::EXTEND_READ)) {
            /** @var PostSavedExtension $PostSavedExtension */
            $PostReadExtension = ClassRegistry::init('PostReadExtension');
            $PostReadExtension->setUserId($userId);
            $data = $PostReadExtension->extendMulti($data, "{n}.id", "post_id");
        }
        if ($this->includeExt($extensions, self::EXTEND_TRANSLATION_LANGUAGE)) {

            /** @var TeamTranslationLanguage $TeamTranslationLanguage */
            $TeamTranslationLanguage = ClassRegistry::init('TeamTranslationLanguage');

            if ($TeamTranslationLanguage->canTranslate($teamId)) {

                /** @var TeamTranslationStatus $TeamTranslationStatus */
                $TeamTranslationStatus = ClassRegistry::init('TeamTranslationStatus');

                if ($TeamTranslationStatus->isLimitReached($teamId)) {
                    foreach ($data as $entry) {
                        $entry['translation_limit_reached'] = true;
                        $entry['translation_languages'] = [];
                    }
                } else {
                    /** @var TeamMemberService $TeamMemberService */
                    $TeamMemberService = ClassRegistry::init('TeamMemberService');

                    $userDefaultLanguage = $TeamMemberService->getDefaultTranslationLanguage($teamId, $userId);

                    foreach ($data as $entry) {

                        $postLanguage = Hash::get($entry, 'language');

                        $entry['translation_limit_reached'] = false;
                        $entry['translation_languages'] = [];

                        if ($userDefaultLanguage !== $postLanguage) {

                            $availableLanguages = $TeamTranslationLanguage->getLanguagesByTeam($teamId);

                            foreach ($availableLanguages as $availableLanguage) {
                                if ($postLanguage === $availableLanguage['language']) {
                                    continue;
                                }
                                $entry['translation_languages'][] = $availableLanguage->languageToArray();
                            }
                        }
                    }
                }
            }
        }

        return $data;
    }
}
