<?php
App::import('Lib/DataExtender', 'BaseExtender');
App::import('Lib/DataExtender/Extension', "UserExtension");
App::import('Lib/DataExtender/Extension', "CommentLikeExtension");
App::import('Lib/DataExtender/Extension', "CommentReadDataExtension");
App::import('Lib/DataExtender/Extension', "MentionsToMeExtension");
App::import('Service', 'CommentService');
App::import('Service', 'TeamMemberService');
App::uses('Team', 'Model');
App::uses('TeamTranslationLanguage', 'Model');
App::uses('TeamTranslationStatus', 'Model');
App::uses('TranslationLanguage', 'Model');

class CommentExtender extends BaseExtender
{
    const EXTEND_ALL = "ext:comment:all";
    const EXTEND_USER = "ext:comment:user";
    const EXTEND_LIKE = "ext:comment:like";
    const EXTEND_READ = "ext:comment:read";
    const EXTEND_MENTIONS_TO_ME_IN_BODY = "ext:comment:mentions_to_me_in_body";
    const EXTEND_ATTACHED_FILES = "ext:comment:attached_files";
    const EXTEND_TRANSLATION_LANGUAGE = "ext:comment:translation_language";

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

        if ($this->includeExt($extensions, self::EXTEND_TRANSLATION_LANGUAGE)) {

            /** @var Team $Team */
            $Team = ClassRegistry::init('Team');
            /** @var TeamTranslationLanguage $TeamTranslationLanguage */
            $TeamTranslationLanguage = ClassRegistry::init('TeamTranslationLanguage');
            /** @var TeamTranslationStatus $TeamTranslationStatus */
            $TeamTranslationStatus = ClassRegistry::init('TeamTranslationStatus');

            if ($TeamTranslationLanguage->hasLanguage($teamId) &&
                $TeamTranslationStatus->hasEntry($teamId) &&
                ($Team->isFreeTrial($teamId) || $Team->isPaidPlan($teamId))) {

                $limitReached = true;
                $translationLanguages = [];

                if (!$TeamTranslationStatus->isLimitReached($teamId)) {

                    /** @var TeamMemberService $TeamMemberService */
                    $TeamMemberService = ClassRegistry::init('TeamMemberService');
                    /** @var TranslationLanguage $TranslationLanguage */
                    $TranslationLanguage = ClassRegistry::init('TranslationLanguage');

                    $limitReached = false;

                    $commentLanguage = Hash::get($data, 'language');

                    $userDefaultLanguage = $TeamMemberService->getDefaultTranslationLanguageCode($teamId, $userId);

                    if ($userDefaultLanguage !== $commentLanguage) {

                        $availableLanguages = $TeamTranslationLanguage->getLanguagesByTeam($teamId);

                        foreach ($availableLanguages as $availableLanguage) {
                            if ($commentLanguage === $availableLanguage['language']) {
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

        if ($this->includeExt($extensions, self::EXTEND_TRANSLATION_LANGUAGE)) {

            /** @var Team $Team */
            $Team = ClassRegistry::init('Team');
            /** @var TeamTranslationLanguage $TeamTranslationLanguage */
            $TeamTranslationLanguage = ClassRegistry::init('TeamTranslationLanguage');
            /** @var TeamTranslationStatus $TeamTranslationStatus */
            $TeamTranslationStatus = ClassRegistry::init('TeamTranslationStatus');

            if ($TeamTranslationLanguage->hasLanguage($teamId) &&
                $TeamTranslationStatus->hasEntry($teamId) &&
                ($Team->isFreeTrial($teamId) || $Team->isPaidPlan($teamId))) {

                if ($TeamTranslationStatus->isLimitReached($teamId)) {
                    foreach ($data as &$entry) {
                        $entry['translation_limit_reached'] = true;
                        $entry['translation_languages'] = [];
                    }
                } else {
                    /** @var TeamMemberService $TeamMemberService */
                    $TeamMemberService = ClassRegistry::init('TeamMemberService');
                    /** @var TranslationLanguage $TranslationLanguage */
                    $TranslationLanguage = ClassRegistry::init('TranslationLanguage');

                    $userDefaultLanguage = $TeamMemberService->getDefaultTranslationLanguageCode($teamId, $userId);

                    foreach ($data as &$entry) {

                        $commentLanguage = Hash::get($entry, 'language');

                        $entry['translation_limit_reached'] = false;
                        $entry['translation_languages'] = [];

                        if ($userDefaultLanguage !== $commentLanguage) {

                            $availableLanguages = $TeamTranslationLanguage->getLanguagesByTeam($teamId);

                            foreach ($availableLanguages as $availableLanguage) {
                                if ($commentLanguage === $availableLanguage['language']) {
                                    continue;
                                }
                                $entry['translation_languages'][] = $TranslationLanguage->getLanguageByCode($availableLanguage['language'])->toLanguageArray();
                            }
                        }
                    }
                }
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
     *
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
