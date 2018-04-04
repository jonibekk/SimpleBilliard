<?php
App::uses('TeamStatus', 'Lib/Status');
App::uses('AppModel', 'Model');
App::uses('VideoStream', 'Model');
App::uses('TranscodeOutputVersionDefinition', 'Model/Video/Transcode');

use Goalous\Model\Enum as Enum;

/**
 * Class PostResource
 */
class PostResource extends AppModel
{
    const COLUMN_POST = 'post_id';
    const COLUMN_POST_DRAFT = 'post_draft_id';

    /**
     * Return all post_resources of posts.id
     *
     * @param int[] $postIds
     * @param bool  $checkTeamStatus
     *
     * @return array
     */
    function getResourcesByPostId(array $postIds, bool $checkTeamStatus = true): array
    {
        return $this->getResourcesByPostOrPostDraftId($postIds, static::COLUMN_POST, $checkTeamStatus);
    }

    /**
     * Return all post_resources of post_drafts.id
     *
     * @param int[] $postDraftIds
     * @param bool  $checkTeamStatus
     *
     * @return array
     */
    function getResourcesByPostDraftId(array $postDraftIds, bool $checkTeamStatus = true): array
    {
        return $this->getResourcesByPostOrPostDraftId($postDraftIds, static::COLUMN_POST_DRAFT, $checkTeamStatus);
    }

    /**
     * Return all post_resources of {posts or post_drafts}.id
     * TODO: https://jira.goalous.com/browse/GL-6601
     *
     * @param int[]  $ids
     * @param string $postOrDraftColumnName
     * @param bool   $checkTeamStatus
     *
     * @return array
     */
    private function getResourcesByPostOrPostDraftId(array $ids, string $postOrDraftColumnName, bool $checkTeamStatus): array
    {
        $options = [
            'fields'     => [
                'PostResource.id',
                'PostResource.post_id',
                'PostResource.post_draft_id',
                'PostResource.resource_type',
                'PostResource.resource_id',
            ],
            'conditions' => [
                $postOrDraftColumnName => $ids,
            ],
        ];
        $postResources = $this->find('all', $options);

        if (is_null($postResources)) {
            GoalousLog::error('find error on post_resources', [
                'post_or_draft' => $postOrDraftColumnName,
                'ids'           => $ids,
            ]);
            return [];
        }
        $postResources = Hash::extract($postResources, '{n}.PostResource');

        /** @var VideoStream $VideoStream */
        $VideoStream = ClassRegistry::init('VideoStream');

        $resources = [];
        // create $resources:array = [
        //      PostResourceType:int => [
        //          resource_id:int => resource data:array,
        //          ...
        //      ]
        // ]
        foreach ($postResources as $postResource) {
            $resourceType = new Enum\Post\PostResourceType(intval($postResource['resource_type']));

            $hashKeyResource = sprintf('%s.%s', Enum\Post\PostResourceType::VIDEO_STREAM, $postResource['resource_id']);
            if (Hash::check($resources, $hashKeyResource)) {
                continue;
            }
            if ($this->shouldIgnoreResource($resourceType, $checkTeamStatus)) {
                continue;
            }
            // written in switch case
            // more resource_type will defined in future
            switch ($resourceType->getValue()) {
                case Enum\Post\PostResourceType::VIDEO_STREAM:
                    $resourceVideoStream = $VideoStream->getById($postResource['resource_id']);
                    if (empty($resourceVideoStream)) {
                        break;
                    }
                    $videoStoragePath = $resourceVideoStream['storage_path'];
                    $urlBaseStorage = sprintf('%s/%s/%s', S3_BASE_URL, AWS_S3_BUCKET_VIDEO_TRANSCODED, $videoStoragePath);
                    $transcoderOutputVersion = new Enum\Video\TranscodeOutputVersion(intval($resourceVideoStream['output_version']));
                    $transcodeOutput = TranscodeOutputVersionDefinition::getVersion($transcoderOutputVersion);

                    $resourceVideoStream['video_sources'] = $transcodeOutput->getVideoSources($urlBaseStorage);
                    $resourceVideoStream['thumbnail'] = $transcodeOutput->getThumbnailUrl($urlBaseStorage);
                    $resourceVideoStream['post_resource_type'] = Enum\Post\PostResourceType::VIDEO_STREAM();
                    $resources = Hash::insert($resources, $hashKeyResource, $resourceVideoStream);
            }
        }

        $results = [];
        // make the empty array of specified id's
        foreach ($ids as $id) {
            $results[$id] = [];
        }
        // create $results:array = [
        //      (posts.id | post_draft_id):int => [
        //          resource data:array,
        //          ...
        //      ]
        // ]
        foreach ($postResources as $postResource) {
            $hashKeyResource = '';
            $resourceType = new Enum\Post\PostResourceType(intval($postResource['resource_type']));
            if ($this->shouldIgnoreResource($resourceType, $checkTeamStatus)) {
                continue;
            }
            switch ($resourceType->getValue()) {
                case Enum\Post\PostResourceType::VIDEO_STREAM:
                    $hashKeyResource = sprintf('%s.%s', Enum\Post\PostResourceType::VIDEO_STREAM, $postResource['resource_id']);
                    break;
                default:
                    GoalousLog::error('resource type not found for post resource', [
                        'resource_type' => sprintf('%s:%s', $resourceType->getValue(), $resourceType->getKey()),
                    ]);
                    break;
            }
            $targetId = $postResource[$postOrDraftColumnName];
            $results[$targetId][] = Hash::get($resources, $hashKeyResource);
        }
        return $results;
    }

    /**
     * Return bool if team is not need to see target resource
     *
     * @param Enum\Post\PostResourceType $postResourceType
     * @param bool                       $checkTeamStatus
     *
     * @return bool
     */
    private function shouldIgnoreResource(Enum\Post\PostResourceType $postResourceType, bool $checkTeamStatus): bool
    {
        switch ($postResourceType->getValue()) {
            case Enum\Post\PostResourceType::VIDEO_STREAM:
                if ($checkTeamStatus && !TeamStatus::getCurrentTeam()->canVideoPostPlay()) {
                    // If team can't play the video
                    return true;
                }
                break;
            default:
                break;
        }
        return false;
    }

    /**
     * Find post_drafts.id by
     *      PostResourceType
     *          (VIDEO_STREAM = 1)
     *      and
     *      resource_id (that stands for PostResourceType)
     *          (PostResourceType = 1 = VIDEO_STREAM = video_streams.id)
     *
     * e.g.
     *  getPostDraftIdByResourceTypeAndResourceId(PostResourceType::VIDEO_STREAM(), 123)
     *      means finding the post_drafts.id that have relation to 'video_streams.id = 123'
     *
     * @param Enum\Post\PostResourceType $resourceType
     * @param int                        $resourceId
     *
     * @return int|null
     */
    function getPostDraftIdByResourceTypeAndResourceId(Enum\Post\PostResourceType $resourceType, int $resourceId)/*: ?int */
    {
        $options = [
            'fields'     => [
                'post_draft_id'
            ],
            'conditions' => [
                'resource_type' => $resourceType->getValue(),
                'resource_id'   => $resourceId,
            ],
        ];
        $r = $this->find('first', $options);
        if (empty($r)) {
            return null;
        }
        return $r['PostResource']['post_draft_id'];
    }
}