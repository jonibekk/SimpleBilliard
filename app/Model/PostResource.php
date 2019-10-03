<?php
App::uses('TeamStatus', 'Lib/Status');
App::uses('AppModel', 'Model');
App::uses('VideoStream', 'Model');
App::uses('TranscodeOutputVersionDefinition', 'Model/Video/Transcode');
App::import('Model/Entity', 'PostResourceEntity');

use Goalous\Enum as Enum;
use Goalous\Enum\DataType\DataType as DataType;

/**
 * Class PostResource
 */
class PostResource extends AppModel
{
    const COLUMN_POST = 'post_id';
    const COLUMN_POST_DRAFT = 'post_draft_id';

    protected $modelConversionTable = [
        'post_id'        => DataType::INT,
        'post_draft_id'  => DataType::INT,
        'resource_type'  => DataType::INT,
        'resource_id'    => DataType::INT,
        'resource_order' => DataType::INT
    ];

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
            $resourceType = new Enum\Model\Post\PostResourceType(intval($postResource['resource_type']));

            $hashKeyResource = sprintf('%s.%s', Enum\Model\Post\PostResourceType::VIDEO_STREAM, $postResource['resource_id']);
            if (Hash::check($resources, $hashKeyResource)) {
                continue;
            }
            if ($this->shouldIgnoreResource($resourceType, $checkTeamStatus)) {
                continue;
            }
            // written in switch case
            // more resource_type will defined in future
            switch ($resourceType->getValue()) {
                case Enum\Model\Post\PostResourceType::VIDEO_STREAM:
                    $resourceVideoStream = $VideoStream->getById($postResource['resource_id']);
                    if (empty($resourceVideoStream)) {
                        break;
                    }
                    $videoStoragePath = $resourceVideoStream['storage_path'];
                    $urlBaseStorage = sprintf('%s/%s/%s', S3_BASE_URL, AWS_S3_BUCKET_VIDEO_TRANSCODED, $videoStoragePath);
                    $transcoderOutputVersion = new Enum\Model\Video\TranscodeOutputVersion(intval($resourceVideoStream['output_version']));
                    $transcodeOutput = TranscodeOutputVersionDefinition::getVersion($transcoderOutputVersion);

                    $resourceVideoStream['video_sources'] = $transcodeOutput->getVideoSources($urlBaseStorage);
                    $resourceVideoStream['thumbnail'] = $transcodeOutput->getThumbnailUrl($urlBaseStorage);
                    $resourceVideoStream['resource_type'] = Enum\Model\Post\PostResourceType::VIDEO_STREAM;
                    $resources = Hash::insert($resources, $hashKeyResource, $resourceVideoStream);
            }
        }

        $results = [];
        // create $results:array = [
        //      (posts.id | post_draft_id):int => [
        //          resource data:array,
        //          ...
        //      ]
        // ]
        foreach ($postResources as $postResource) {
            $hashKeyResource = '';
            $resourceType = new Enum\Model\Post\PostResourceType(intval($postResource['resource_type']));
            if ($this->shouldIgnoreResource($resourceType, $checkTeamStatus)) {
                continue;
            }
            switch ($resourceType->getValue()) {
                case Enum\Model\Post\PostResourceType::VIDEO_STREAM:
                    $hashKeyResource = sprintf('%s.%s', Enum\Model\Post\PostResourceType::VIDEO_STREAM, $postResource['resource_id']);
                    $targetId = $postResource[$postOrDraftColumnName];
                    if (empty($results[$targetId])) {
                        $results[$targetId] = [];
                    }
                    $results[$targetId][] = Hash::get($resources, $hashKeyResource);
                    break;
                default:
                    // Currently not returning image/file/video file resource.
                    break;
            }
        }
        return $results;
    }

    /**
     * Return bool if team is not need to see target resource
     *
     * @param Enum\Model\Post\PostResourceType $postResourceType
     * @param bool                             $checkTeamStatus
     *
     * @return bool
     */
    private function shouldIgnoreResource(Enum\Model\Post\PostResourceType $postResourceType, bool $checkTeamStatus): bool
    {
        switch ($postResourceType->getValue()) {
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
     * @param Enum\Model\Post\PostResourceType $resourceType
     * @param int                              $resourceId
     *
     * @return int|null
     */
    function getPostDraftIdByResourceTypeAndResourceId(Enum\Model\Post\PostResourceType $resourceType, int $resourceId)/*: ?int */
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

    /**
     * Find post resources that does not exist in given array
     *
     * @param int   $postId
     * @param array $resources
     *              [resource_type => [resource_id]]
     *
     * @return array
     */
    public function findDeletedPostResourcesInPost(int $postId, array $resources): array
    {
        $result = [];

        $condition = [
            'conditions' => [
                'post_id' => $postId,
            ],
        ];

        $queried = Hash::extract($this->useType()->find('all', $condition), '{n}.{s}');

        foreach ($queried as $singleQueried) {
            if (empty($resources[$singleQueried['resource_type']]) || !in_array($singleQueried['resource_id'], $resources[$singleQueried['resource_type']])) {
                $result[] = $singleQueried;
            }
        }

        return $result;
    }

    /**
     * Find the highest resource order of a given post id
     *
     * @param int $postId
     *
     * @return int Highest resource order of a post. -1 for not existing
     */
    public function findMaxResourceOrderOfPost(int $postId): int
    {
        $condition = [
            'conditions' => [
                'post_id' => $postId,
                'del_flg' => false
            ],
            'fields'     => [
                'MAX(resource_order) as max_order'
            ]
        ];

        return $this->find('first', $condition)[0]['max_order'] ?: -1;
    }

    /**
     * Find all post_resources of a given post id
     *
     * @param int $postId
     *
     * @return array
     */
    public function getAllPostResources(int $postId): array
    {
        $condition = [
            'conditions' => [
                'post_id' => $postId,
            ],
        ];
        $result = $this->useType()->find('all', $condition);

        return Hash::extract($result, '{n}.{s}');
    }

    /**
     * Check whether video stream is only used by a single post
     *
     * @param int $videoStreamId
     *
     * @return bool
     */
    public function isVideoStreamUnique(int $videoStreamId): bool
    {
        $condition = [
            'conditions' => [
                'resource_id'   => $videoStreamId,
                'resource_type' => Enum\Model\Post\PostResourceType::VIDEO_STREAM,
                'del_flg'       => false
            ]
        ];

        return $this->find('count', $condition) == 1;
    }

    /**
     * Get PostResource ID
     *
     * @param int                              $resourceId
     * @param Enum\Model\Post\PostResourceType $type
     *
     * @return int[] PostResource.id
     */
    public function getPostResourceId(int $resourceId, Enum\Model\Post\PostResourceType $type): array {

        $condition = [
            'conditions' => [
                'resource_id'   => $resourceId,
                'resource_type' => $type->getValue(),
                'del_flg'       => false
            ],
            'fields' => [
                'id'
            ]
        ];

        return Hash::extract($this->find('all', $condition), '{n}.{s}.id') ?: [];
    }
}
