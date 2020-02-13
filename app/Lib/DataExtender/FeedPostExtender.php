<?php
App::import('Lib/DataExtender', 'BaseExtender');
App::import('Service', 'ImageStorageService');
App::import('Lib/DataExtender/Extension', 'UserExtension');
App::import('Lib/DataExtender/Extension', 'ActionExtension');
App::import('Lib/DataExtender/Extension', 'KeyResultExtension');
App::import('Lib/DataExtender/Extension', 'GoalExtension');
App::import('Lib/DataExtender/Extension', 'PostLikeExtension');
App::import('Lib/DataExtender/Extension', 'PostSavedExtension');
App::import('Lib/DataExtender/Extension', 'PostReadExtension');
App::import('Service/Paging', 'CommentPagingService');
App::import('Service', 'PostService');
App::import('Service', 'TeamMemberService');
App::uses('AttachedFile', 'Model');
App::uses('Team', 'Model');
App::uses('TeamTranslationLanguage', 'Model');
App::uses('TeamTranslationStatus', 'Model');
App::uses('TranslationLanguage', 'Model');

use Goalous\Enum as Enum;

class FeedPostExtender extends BaseExtender
{
    const EXTEND_ALL = "ext:feed_post:all";
    const EXTEND_USER = "ext:feed_post:user";
    const EXTEND_COMMENTS = "ext:feed_post:comment";
    const EXTEND_GKR = "ext:feed_post:gkr";
    const EXTEND_LIKE = "ext:feed_post:like";
    const EXTEND_SAVED = "ext:feed_post:saved";
    const EXTEND_READ = "ext:feed_post:read";
    const EXTEND_TRANSLATION_LANGUAGE = "ext:feed_post:translation_language";

    const DEFAULT_COMMENT_COUNT = 3;

    public function extend(array $data, int $userId, int $teamId, array $extensions = []): array
    {
        // TODO: Implement extend() method.
    }

    public function extendMulti(array $data, int $userId, int $teamId, array $extensions = []): array
    {
        if ($this->includeExt($extensions, self::EXTEND_USER)) {
            /** @var UserExtension $UserExtension */
            $UserExtension = ClassRegistry::init('UserExtension');
            $data = $UserExtension->extendMulti($data, "{n}.user_id");
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
        if ($this->includeExt($extensions, self::EXTEND_GKR)) {
            /** @var ActionExtension $ActionExtension */
            $ActionExtension = ClassRegistry::init('ActionExtension');
            $data = $ActionExtension->extendMulti($data, "{n}.action_result_id");

            /** @var KeyResultExtension $KeyResultExtension */
            $KeyResultExtension = ClassRegistry::init('KeyResultExtension');
            $data = $KeyResultExtension->extendMulti($data, "{n}.action_result.key_result_id");

            /** @var GoalExtension $GoalExtension */
            $GoalExtension = ClassRegistry::init('GoalExtension');
            $data = $GoalExtension->extendMulti($data, "{n}.goal_id");

            /** @var AttachedFile $AttachedFile */
            $AttachedFile = ClassRegistry::init('AttachedFile');

            /** @var ImageStorageService $ImageStorageService */
            $ImageStorageService = ClassRegistry::init('ImageStorageService');

            /** @var KrProgressLog $KrProgressLog */
            $KrProgressLog = ClassRegistry::init('KrProgressLog');

            foreach ($data as $index => $entry) {

                if ($entry['type'] == Enum\Model\Post\Type::CREATE_GOAL) {

                    $startDate = GoalousDateTime::createFromFormat('Y-m-d', $entry['goal']['start_date']);
                    $endDate = GoalousDateTime::createFromFormat('Y-m-d', $entry['goal']['end_date']);

                    //If now is within goal's period, user can collaborate
                    $data[$index]['can_collaborate'] = GoalousDateTime::now()->between($startDate, $endDate);
                }

                if (empty($entry['action_result_id'])) {
                    continue;
                }

                $krProgressLog = $KrProgressLog->getByActionResultId($entry['action_result_id']);

                // Get KR Progress log
                $data[$index]['kr_progress_log'] = !empty($krProgressLog) ? $KrProgressLog->getByActionResultId($entry['action_result_id'])
                                                                                          ->toArray() : null;
                $data[$index]['resources'] = [];

                $Upload = new UploadHelper(new View());

                $attachedFiles = $AttachedFile->getActionResultResources($entry['action_result_id']);

                $data[$index]['action_img_url'] = $ImageStorageService->getImgUrlEachSize($attachedFiles[0]->toArray(),
                    'AttachedFile',
                    'attached');

                //If only action result image, continue
                if (count($attachedFiles) === 1) {
                    continue;
                }

                for ($fileIndex = 1; $fileIndex < count($attachedFiles); $fileIndex++) {

                    $attachedFile = $attachedFiles[$fileIndex]->toArray();

                    // Fetch data from attached_files
                    if (in_array($attachedFile['file_type'], [
                        Enum\Model\Post\PostResourceType::IMAGE,
                        Enum\Model\Post\PostResourceType::FILE,
                        Enum\Model\Post\PostResourceType::FILE_VIDEO,
                    ])) {
                        $attachedFile['file_url'] = '';
                        $attachedFile['preview_url'] = '';
                        // download url is common.
                        // TODO: We should consider to preapare new API or using old processe;
                        //  $file['download_url'] = '/posts/attached_file_download/file_id:' . $file['id'];
                        $attachedFile['download_url'] = '';

                        if ($attachedFile['file_type'] == AttachedFile::TYPE_FILE_IMG && empty($attachedFile['display_file_list_flg'])) {
                            $attachedFile['file_url'] = $ImageStorageService->getImgUrlEachSize($attachedFile,
                                'AttachedFile',
                                'attached');
                            $attachedFile['resource_type'] = Enum\Model\Post\PostResourceType::IMAGE;
                            $data[$index]['resources'][] = $attachedFile;
                        } else {
                            $attachedFile['preview_url'] = $Upload->attachedFileUrl($attachedFile);
                            $attachedFile['resource_type'] = Enum\Model\Post\PostResourceType::FILE;
                            $data[$index]['resources'][] = $attachedFile;
                        }
                        continue;
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
            /** @var PostReadExtension $PostSavedExtension */
            $PostReadExtension = ClassRegistry::init('PostReadExtension');
            $PostReadExtension->setUserId($userId);
            $data = $PostReadExtension->extendMulti($data, "{n}.id", "post_id");
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
                        $postLanguage = Hash::get($entry, 'language');

                        $entry['translation_limit_reached'] = false;
                        $entry['translation_languages'] = [];

                        if ($userDefaultLanguage !== $postLanguage) {

                            $availableLanguages = $TeamTranslationLanguage->getLanguagesByTeam($teamId);

                            foreach ($availableLanguages as $availableLanguage) {
                                if ($postLanguage === $availableLanguage['language']) {
                                    continue;
                                }
                                $entry['translation_languages'][] = $TranslationLanguage->getLanguageByCode($availableLanguage['language'])
                                                                                        ->toLanguageArray();
                            }
                        }
                    }
                }
            }
        }
        return $data;
    }
}
