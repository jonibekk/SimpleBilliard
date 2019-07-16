<?php
App::import('Lib/DataExtender', 'BaseExtender');
App::import('Service', 'ImageStorageService');
App::import('Service', 'TeamMemberService');
App::import('Lib/DataExtender/Extension', 'UserExtension');
App::import('Lib/DataExtender/Extension', 'CircleExtension');
App::import('Lib/DataExtender/Extension', 'ActionExtension');
App::import('Lib/DataExtender/Extension', 'KeyResultExtension');
App::import('Lib/DataExtender/Extension', 'GoalExtension');
App::import('Lib/DataExtender/Extension', 'PostLikeExtension');
App::import('Lib/DataExtender/Extension', 'PostSavedExtension');
App::import('Lib/DataExtender/Extension', 'PostReadExtension');
App::import('Service/Paging', 'CommentPagingService');
App::import('Service', 'PostService');
App::uses('PagingRequest', 'Lib/Paging');
App::uses('Team', 'Model');
App::uses('TeamTranslationLanguage', 'Model');
App::uses('TeamTranslationStatus', 'Model');
App::uses('TranslationLanguage', 'Model');

class PostExtender extends BaseExtender
{
    const EXTEND_ALL = "ext:post:all";
    const EXTEND_USER = "ext:post:user";
    const EXTEND_RELATED_TYPE = "ext:post:related_type";
    const EXTEND_COMMENTS = "ext:post:comments";
    const EXTEND_POST_SHARE_CIRCLE = "ext:post:share_circle";
    const EXTEND_POST_SHARE_USER = "ext:post:share_user";
    const EXTEND_POST_FILE = "ext:post:file";
    const EXTEND_LIKE = "ext:post:like";
    const EXTEND_SAVED = "ext:post:saved";
    const EXTEND_READ = "ext:post:read";
    const EXTEND_TRANSLATION_LANGUAGE = "ext:circle_post:translation_language";

    const DEFAULT_COMMENT_COUNT = 3;

    public function extend(array $data, int $userId, int $teamId, array $extensions = []): array
    {
        if ($this->includeExt($extensions, self::EXTEND_USER)) {
            /** @var UserExtension $UserExtension */
            $UserExtension = ClassRegistry::init('UserExtension');
            $data = $UserExtension->extend($data, "user_id");
        }
        if ($this->includeExt($extensions, self::EXTEND_RELATED_TYPE)) {
            switch ((int)$data['type']) {
                case Post::TYPE_NORMAL:
                    // TODO: depends on spec
                    break;
                case Post::TYPE_CREATE_CIRCLE:
                    /** @var CircleExtension $CircleExtension */
                    $CircleExtension = ClassRegistry::init('CircleExtension');
                    $data = $CircleExtension->extend($data, "circle_id");
                    break;
                case Post::TYPE_ACTION:
                case Post::TYPE_KR_COMPLETE:
                case Post::TYPE_CREATE_GOAL:
                case Post::TYPE_GOAL_COMPLETE:
                    /** @var ActionExtension $ActionExtension */
                    $ActionExtension = ClassRegistry::init('ActionExtension');
                    $data = $ActionExtension->extend($data, "action_result_id");

                    /** @var KeyResultExtension $KeyResultExtension */
                    $KeyResultExtension = ClassRegistry::init('KeyResultExtension');
                    $data = $KeyResultExtension->extend($data, "action_result.key_result_id");

                    /** @var GoalExtension $GoalExtension */
                    $GoalExtension = ClassRegistry::init('GoalExtension');
                    $data = $GoalExtension->extend($data, "action_result.goal_id");
                    break;
            }
        }
        if ($this->includeExt($extensions, self::EXTEND_COMMENTS)) {
            /** @var CommentPagingService $CommentPagingService */
            $CommentPagingService = ClassRegistry::init('CommentPagingService');

            $commentPagingRequest = new PagingRequest();
            $commentPagingRequest->setResourceId(Hash::get($data, 'id'));
            $commentPagingRequest->setCurrentUserId($userId);
            $commentPagingRequest->setCurrentTeamId($teamId);

            $comments = $CommentPagingService->getDataWithPaging($commentPagingRequest, self::DEFAULT_COMMENT_COUNT,
                CommentExtender::EXTEND_ALL);

            $data['comments'] = $comments;
        }
        if ($this->includeExt($extensions, self::EXTEND_POST_FILE)) {
            // Set image url each post photo
            /** @var ImageStorageService $ImageStorageService */
            $ImageStorageService = ClassRegistry::init('ImageStorageService');

            /** @var PostService $PostService */
            $PostService = ClassRegistry::init('PostService');

            $Upload = new UploadHelper(new View());

            $attachedFile = $PostService->getAttachedFiles($data['id']);
            $data['attached_files'] = [];
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
                    $data['attached_files'][] = $file->toArray();
                } else {
                    $file['preview_url'] = $Upload->attachedFileUrl($file->toArray());
                    $data['attached_files'][] = $file->toArray();
                }
            }
        }
        if ($this->includeExt($extensions, self::EXTEND_LIKE)) {
            /** @var PostLikeExtension $PostLikeExtension */
            $PostLikeExtension = ClassRegistry::init('PostLikeExtension');
            $PostLikeExtension->setUserId($userId);
            $data = $PostLikeExtension->extend($data, "id", "post_id");
        }
        if ($this->includeExt($extensions, self::EXTEND_SAVED)) {
            /** @var PostSavedExtension $PostSavedExtension */
            $PostSavedExtension = ClassRegistry::init('PostSavedExtension');
            $PostSavedExtension->setUserId($userId);
            $data = $PostSavedExtension->extend($data, "id", "post_id");
        }
        if ($this->includeExt($extensions, self::EXTEND_READ)) {
            /** @var PostSavedExtension $PostSavedExtension */
            $PostReadExtension = ClassRegistry::init('PostReadExtension');
            $PostReadExtension->setUserId($userId);
            $data = $PostReadExtension->extend($data, "id", "post_id");
        }
        if ($this->includeExt($extensions, self::EXTEND_TRANSLATION_LANGUAGE)) {

            /** @var Team $Team */
            $Team = ClassRegistry::init('Team');
            /** @var TeamTranslationLanguage $TeamTranslationLanguage */
            $TeamTranslationLanguage = ClassRegistry::init('TeamTranslationLanguage');

            if ($TeamTranslationLanguage->canTranslate($teamId) &&
                ($Team->isFreeTrial($teamId) || $Team->isPaidPlan($teamId))) {

                /** @var TeamTranslationStatus $TeamTranslationStatus */
                $TeamTranslationStatus = ClassRegistry::init('TeamTranslationStatus');

                $limitReached = true;
                $translationLanguages = [];

                if (!$TeamTranslationStatus->isLimitReached($teamId)) {

                    /** @var TeamMemberService $TeamMemberService */
                    $TeamMemberService = ClassRegistry::init('TeamMemberService');
                    /** @var TranslationLanguage $TranslationLanguage */
                    $TranslationLanguage = ClassRegistry::init('TranslationLanguage');

                    $limitReached = false;

                    $postLanguage = Hash::get($data, 'language');

                    $userDefaultLanguage = $TeamMemberService->getDefaultTranslationLanguageCode($teamId, $userId);

                    if ($userDefaultLanguage !== $postLanguage) {

                        $availableLanguages = $TeamTranslationLanguage->getLanguagesByTeam($teamId);

                        foreach ($availableLanguages as $availableLanguage) {
                            if ($postLanguage === $availableLanguage['language']) {
                                continue;
                            }
                            $translationLanguages[] = $TranslationLanguage->getLanguageByCode($availableLanguage['language'])->toLanguageArray();
                        }
                    }
                }
                $data['translation_limit_reached'] = $limitReached;
                $data['translation_languages'] = $translationLanguages;
            }
        }

        return $data;
    }

    public function extendMulti(array $data, int $userId, int $teamId, array $extensions = []): array
    {
        throw new RuntimeException("Please implement " . __METHOD__);
    }
}
