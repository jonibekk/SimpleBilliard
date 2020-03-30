<?php
App::import('Lib/DataExtender', 'BaseExtender');
App::import('Service', 'ImageStorageService');
App::import('Lib/DataExtender/Extension', 'UserExtension');
App::import('Lib/DataExtender/Extension', 'CircleIdExtension');
App::import('Lib/DataExtender/Extension', 'CircleExtension');
App::import('Lib/DataExtender/Extension', 'PostLikeExtension');
App::import('Lib/DataExtender/Extension', 'PostSavedExtension');
App::import('Lib/DataExtender/Extension', 'PostReadExtension');
App::import('Service/Paging', 'CommentPagingService');
App::import('Service', 'PostService');
App::import('Service', 'TeamMemberService');
App::uses('Team', 'Model');
App::uses('TeamTranslationLanguage', 'Model');
App::uses('TeamTranslationStatus', 'Model');
App::uses('TranslationLanguage', 'Model');
App::import('Service', 'VideoStreamService');

use Goalous\Enum as Enum;

class CirclePostExtender extends BaseExtender
{
    const EXTEND_ALL = "ext:circle_post:all";
    const EXTEND_USER = "ext:circle_post:user";
    const EXTEND_CIRCLE_ID = "ext:circle_post:circle_id";
    const EXTEND_CIRCLE = "ext:circle_post:circle";
    const EXTEND_COMMENTS = "ext:circle_post:comments";
    const EXTEND_POST_SHARE_CIRCLE = "ext:circle_post:share_circle";
    const EXTEND_POST_SHARE_USER = "ext:circle_post:share_user";
    const EXTEND_RESOURCES = "ext:circle_post:resources";
    const EXTEND_LIKE = "ext:circle_post:like";
    const EXTEND_SAVED = "ext:circle_post:saved";
    const EXTEND_READ = "ext:circle_post:read";
    const EXTEND_TRANSLATION_LANGUAGE = "ext:circle_post:translation_language";
    const EXTEND_RELATED_TYPE = "ext:circle_post:related_type";
    const EXTEND_SITE_INFO = "ext:circle_post:site_info";

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
        if ($this->includeExt($extensions, self::EXTEND_CIRCLE_ID)) {
            /** @var CircleIdExtension $CircleIdExtension */
            $CircleIdExtension = ClassRegistry::init('CircleIdExtension');
            $data = $CircleIdExtension->extendMulti($data, '');
        }
        if ($this->includeExt($extensions, self::EXTEND_CIRCLE)) {
            /** @var CircleExtension $CircleExtension */
            $CircleExtension = ClassRegistry::init('CircleExtension');
            $CircleExtension->setUserId($userId);
            $data = $CircleExtension->extendMulti($data, "{n}.circle_id");
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
        if ($this->includeExt($extensions, self::EXTEND_RELATED_TYPE)) {

            foreach ($data as $i => $entry) {
                switch ((int)$entry['type']) {
                    case Post::TYPE_NORMAL:
                        // TODO: depends on spec
                        break;
                    case Post::TYPE_CREATE_CIRCLE:
                        /** @var CircleExtension $CircleExtension */
                        $CircleExtension = ClassRegistry::init('CircleExtension');
                        $data[$i] = $CircleExtension->extend($entry, "circle_id");
                        break;
                    case Post::TYPE_ACTION:
                    case Post::TYPE_KR_COMPLETE:
                    case Post::TYPE_CREATE_GOAL:
                    case Post::TYPE_GOAL_COMPLETE:
                        /** @var ActionExtension $ActionExtension */
                        $ActionExtension = ClassRegistry::init('ActionExtension');
                        $data[$i] = $ActionExtension->extend($entry, "action_result_id");

                        /** @var KeyResultExtension $KeyResultExtension */
                        $KeyResultExtension = ClassRegistry::init('KeyResultExtension');
                        $data[$i] = $KeyResultExtension->extend($entry, "action_result.key_result_id");

                        /** @var GoalExtension $GoalExtension */
                        $GoalExtension = ClassRegistry::init('GoalExtension');
                        $data[$i] = $GoalExtension->extend($entry, "action_result.goal_id");
                        break;
                }
            }
        }
        if ($this->includeExt($extensions, self::EXTEND_RESOURCES)) {

            foreach ($data as $index => $entry) {
                $data[$index]['resources'] = [];

                /** @var PostService $PostService */
                $PostService = ClassRegistry::init('PostService');

                /** @var ImageStorageService $ImageStorageService */
                $ImageStorageService = ClassRegistry::init('ImageStorageService');
                /** @var VideoStreamService $VideoStreamService */
                $VideoStreamService = ClassRegistry::init('VideoStreamService');

                $Upload = new UploadHelper(new View());

                $resources = $PostService->getResourcesByPostId($entry['id']);
                foreach ($resources as $resource) {
                    /** @var PostResourceEntity $resource */
                    $postResource = $resource->offsetGet('PostResource');
                    $attachedFile = $resource->offsetGet('AttachedFile');

                    // Joined table does not cast types even if using useEntity()
                    $attachedFile['file_type'] = (int)$attachedFile['file_type'];

                    // Fetch data from attached_files
                    if (in_array($postResource['resource_type'], [
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

                        if ($attachedFile['file_type'] == AttachedFile::TYPE_FILE_IMG) {
                            $attachedFile['file_url'] = $ImageStorageService->getImgUrlEachSize($attachedFile, 'AttachedFile',
                                'attached');
                            $attachedFile['resource_type'] = Enum\Model\Post\PostResourceType::IMAGE;
                            $data[$index]['resources'][] = $attachedFile;
                        } else {
                            $attachedFile['preview_url'] = $Upload->attachedFileUrl($attachedFile);
                            $attachedFile['resource_type'] = Enum\Model\Post\PostResourceType::FILE;
                            $data[$index]['resources'][] = $attachedFile;
                        }
                        continue;
                    };

                    // Fetch data from video stream
                    if ((int)$postResource['resource_type'] === Enum\Model\Post\PostResourceType::VIDEO_STREAM) {
                        $isUserAgentSupportManifestRedirect = $VideoStreamService->isBrowserSupportManifestRedirects();
                        $resourceVideoStream = $VideoStreamService->getVideoStreamForPlayer($postResource['resource_id'], !$isUserAgentSupportManifestRedirect);
                        $data[$index]['resources'][] = $resourceVideoStream;
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
                                $entry['translation_languages'][] = $TranslationLanguage->getLanguageByCode($availableLanguage['language'])->toLanguageArray();
                            }
                        }
                    }
                }
            }
        }

        if ($this->includeExt($extensions, self::EXTEND_SITE_INFO)) {
            foreach ($data as &$entry) {
                $entry['site_info'] = json_decode($entry['site_info'], true);
            }
        }

        return $data;
    }
}
