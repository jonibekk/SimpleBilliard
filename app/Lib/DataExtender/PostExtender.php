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
App::import('Lib/DataExtender/Extension', 'PostShareCircleExtension');
App::import('Service/Paging', 'CommentPagingService');
App::import('Service/Paging', 'CommentAllService');
App::import('Service', 'PostService');
App::uses('PagingRequest', 'Lib/Paging');
App::uses('Team', 'Model');
App::uses('TeamTranslationLanguage', 'Model');
App::uses('TeamTranslationStatus', 'Model');
App::uses('TranslationLanguage', 'Model');
App::import('Service', 'PostService');
App::import('Service', 'VideoStreamService');

use Goalous\Enum as Enum;

class PostExtender extends BaseExtender
{
    const EXTEND_ALL = "ext:post:all";
    const EXTEND_USER = "ext:post:user";
    const EXTEND_RELATED_TYPE = "ext:post:related_type";
    const EXTEND_COMMENTS = "ext:post:comments";
    const EXTEND_COMMENTS_ALL = "ext:post:comments:all";
    const EXTEND_POST_SHARE_CIRCLE = "ext:post:share_circle";
    const EXTEND_POST_SHARE_USER = "ext:post:share_user";
    const EXTEND_POST_RESOURCES = "ext:post:resources";
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
            $postType = (int)$data['type'];
            switch ($postType) {
                case Post::TYPE_NORMAL:
                    // TODO: depends on spec
                    break;
                case Post::TYPE_CREATE_CIRCLE:
                    /** @var CircleExtension $CircleExtension */
                    $CircleExtension = ClassRegistry::init('CircleExtension');
                    $data = $CircleExtension->extend($data, "circle_id");
                    break;
                case Post::TYPE_ACTION:
                case Post::TYPE_CREATE_GOAL:
                    /** @var ActionExtension $ActionExtension */
                    $ActionExtension = ClassRegistry::init('ActionExtension');
                    $data = $ActionExtension->extend($data, "action_result_id");
                    /** @var GoalExtension $GoalExtension */
                    $GoalExtension = ClassRegistry::init('GoalExtension');
                    $data = $GoalExtension->extend($data, "goal_id");

                    if ($postType === Post::TYPE_ACTION) {
                        /** @var KrProgressLog $KrProgressLog */
                        $KrProgressLog = ClassRegistry::init('KrProgressLog');
                        $krProgressLog = $KrProgressLog->getByActionResultId($data['action_result_id']);
                        $data['kr_progress_log'] = !empty($krProgressLog) ? $KrProgressLog->getByActionResultId($data['action_result_id'])
                            ->toArray() : null;

                        /** @var AttachedFile $AttachedFile */
                        $AttachedFile = ClassRegistry::init('AttachedFile');
                        $attachedFiles = $AttachedFile->getActionResultResources($data['action_result_id']);
                        /** @var ImageStorageService $ImageStorageService */
                        $ImageStorageService = ClassRegistry::init('ImageStorageService');
                        $data['action_img_url'] = $ImageStorageService->getImgUrlEachSize($attachedFiles[0]->toArray(),
                            'AttachedFile',
                            'attached');
                        /** @var KeyResultExtension $KeyResultExtension */
                        $KeyResultExtension = ClassRegistry::init('KeyResultExtension');
                        $KeyResultExtension->setUserId($userId);
                        $KeyResultExtension->setTeamId($teamId);
                        $data = $KeyResultExtension->extend($data, "action_result.key_result_id");
                    }

                    if ($postType == Enum\Model\Post\Type::CREATE_GOAL) {
                        /** @var KeyResult $KeyResult */
                        $KeyResult = ClassRegistry::init('KeyResult');
                        $topKr = $KeyResult->getTkrWithTyped($data['goal']['id']);
                        $data['key_result'] = $topKr['KeyResult'];

                        /** @var GoalMember $GoalMember */
                        $GoalMember = ClassRegistry::init('GoalMember');
                        $goalMember = $GoalMember->getUnique($userId, $data['goal_id']);

                        $isLeader = !empty($goalMember) && $goalMember['GoalMember']['type'] == GoalMember::TYPE_OWNER;
                        $isCollaborating = !empty($goalMember);

                        $startDate = GoalousDateTime::createFromFormat('Y-m-d', $data['goal']['start_date']);
                        $endDate = GoalousDateTime::createFromFormat('Y-m-d', $data['goal']['end_date']);

                        $data['is_leader'] = $isLeader;
                        //If now is within goal's period and goal is not made by current user, current user can collaborate
                        $inThisTerm = GoalousDateTime::now()->between($startDate, $endDate);
                        $data['can_collaborate'] = !$isLeader && !$isCollaborating && $inThisTerm;
                        $data['is_goal_current_term'] = $inThisTerm;
                        $data['is_collaborating'] = $isCollaborating;

                        /** @var Follower $Follower */
                        $Follower = ClassRegistry::init('Follower');
                        $data['is_following'] = !empty($Follower->isExists($data['goal']['id'], $userId, $teamId));
                    }
                    break;
                    // These post types are not implemented yet
//                case Post::TYPE_KR_COMPLETE:
//                case Post::TYPE_GOAL_COMPLETE:
//                    break;
            }
        }
        $isExtendingAllComment = in_array(self::EXTEND_COMMENTS_ALL, $extensions);
        if ($isExtendingAllComment) {
            /** @var CommentAllService $CommentAllService */
            $CommentAllService = ClassRegistry::init('CommentAllService');

            $commentPagingRequest = new PagingRequest();
            $commentPagingRequest->setResourceId(Hash::get($data, 'id'));
            $commentPagingRequest->setCurrentUserId($userId);
            $commentPagingRequest->setCurrentTeamId($teamId);

            $comments = $CommentAllService->getAllData(
                $commentPagingRequest,
                CommentExtender::EXTEND_ALL);

            $data['comments'] = $comments;
        } elseif ($this->includeExt($extensions, self::EXTEND_COMMENTS)) {
            /** @var CommentPagingService $CommentPagingService */
        $CommentPagingService = ClassRegistry::init('CommentPagingService');

        $commentPagingRequest = new PagingRequest();
        $commentPagingRequest->setResourceId(Hash::get($data, 'id'));
        $commentPagingRequest->setCurrentUserId($userId);
        $commentPagingRequest->setCurrentTeamId($teamId);

        $comments = $CommentPagingService->getDataWithPaging(
            $commentPagingRequest,
            self::DEFAULT_COMMENT_COUNT,
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
                // $file['download_url'] = '/posts/attached_file_download/file_id:' . $file['id'];
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
        if ($this->includeExt($extensions, self::EXTEND_POST_RESOURCES)) {
            if ($data['type'] == Enum\Model\Post\Type::ACTION) {
                $data = $this->extendResourceFromAttachedFiles($data);
            } else {
                $data = $this->extendResource($data);
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
            /** @var PostReadExtension $PostReadExtension */
            $PostReadExtension = ClassRegistry::init('PostReadExtension');
            $PostReadExtension->setUserId($userId);
            $data = $PostReadExtension->extend($data, "id", "post_id");
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

        if ($this->includeExt($extensions, self::EXTEND_POST_SHARE_CIRCLE)) {
            /** @var PostShareCircleExtension $PostShareCircleExtension */
            $PostShareCircleExtension = ClassRegistry::init('PostShareCircleExtension');
            $PostShareCircleExtension->setUserId($userId);
            $PostShareCircleExtension->setTeamId($teamId);
            $data = $PostShareCircleExtension->extend($data, "id", "post_id");
        }

        return $data;
    }

    public function extendMulti(array $data, int $userId, int $teamId, array $extensions = []): array
    {
        throw new RuntimeException("Please implement " . __METHOD__);
    }

    private function extendResourceFromAttachedFiles(array $data): array
    {
        $Upload = new UploadHelper(new View());
        /** @var ImageStorageService $ImageStorageService */
        $ImageStorageService = ClassRegistry::init('ImageStorageService');

        $data['resources'] = [];

        for ($fileIndex = 1; $fileIndex < count($data['attached_files']); $fileIndex++) {
            $attachedFile = $data['attached_files'][$fileIndex];
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
                    $data['resources'][] = $attachedFile;
                } else {
                    $attachedFile['preview_url'] = $Upload->attachedFileUrl($attachedFile);
                    $attachedFile['resource_type'] = Enum\Model\Post\PostResourceType::FILE;
                    $data['resources'][] = $attachedFile;
                }
                continue;
            }
        }
        return $data;
    }

    private function extendResource(array $data): array
    {
        $data['resources'] = [];

        /** @var PostService $PostService */
        $PostService = ClassRegistry::init('PostService');

        /** @var ImageStorageService $ImageStorageService */
        $ImageStorageService = ClassRegistry::init('ImageStorageService');
        /** @var VideoStreamService $VideoStreamService */
        $VideoStreamService = ClassRegistry::init('VideoStreamService');

        $Upload = new UploadHelper(new View());

        $resources = $PostService->getResourcesByPostId($data['id']);

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
                    $data['resources'][] = $attachedFile;
                } else {
                    $attachedFile['preview_url'] = $Upload->attachedFileUrl($attachedFile);
                    $attachedFile['resource_type'] = Enum\Model\Post\PostResourceType::FILE;
                    $data['resources'][] = $attachedFile;
                }
                continue;
            };

            // Fetch data from video stream
            if ((int)$postResource['resource_type'] === Enum\Model\Post\PostResourceType::VIDEO_STREAM) {
                $isUserAgentSupportManifestRedirect = $VideoStreamService->isBrowserSupportManifestRedirects();
                $resourceVideoStream = $VideoStreamService->getVideoStreamForPlayer($postResource['resource_id'], !$isUserAgentSupportManifestRedirect);
                $data['resources'][] = $resourceVideoStream;
            }

        }
        return $data;
    }
}
