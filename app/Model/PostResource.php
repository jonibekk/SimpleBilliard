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
     * @param int $postId
     *
     * @return array
     */
    function getResourcesByPostId(int $postId): array
    {
        return $this->getResourcesByPostOrPostDraftId($postId, static::COLUMN_POST);
    }

    /**
     * Return all post_resources of post_drafts.id
     *
     * @param int $postDraftId
     *
     * @return array
     */
    function getResourcesByPostDraftId(int $postDraftId): array
    {
        return $this->getResourcesByPostOrPostDraftId($postDraftId, static::COLUMN_POST_DRAFT);
    }

    /**
     * Return all post_resources of {posts or post_drafts}.id
     * @param int    $id
     * @param string $postOrDraft
     *
     * @return array
     */
    private function getResourcesByPostOrPostDraftId(int $id, string $postOrDraft): array
    {
        $options = [
            'fields'     => [
                '*'
            ],
            'conditions' => [
                $postOrDraft => $id,
            ],
        ];
        $postResources = $this->find('all', $options);
        if (is_null($postResources)) {
            GoalousLog::error('find error on post_resources', [
                'post_or_draft' => $postOrDraft,
                'id'            => $id,
            ]);
            return [];
        }
        $postResources = Hash::extract($postResources, '{n}.PostResource');

        /** @var VideoStream $VideoStream */
        $VideoStream = ClassRegistry::init('VideoStream');

        $results = [];
        foreach ($postResources as $postResource) {
            $resourceType = new Enum\Post\PostResourceType(intval($postResource['resource_type']));
            switch ($resourceType->getValue()) {
                case Enum\Post\PostResourceType::VIDEO_STREAM:
                    if (!TeamStatus::getCurrentTeam()->canVideoPostPlay()) {
                        continue;
                    }
                    $resourceVideoStream = $VideoStream->getById($postResource['resource_id']);
                    if (empty($resourceVideoStream)) {
                        continue;
                    }
                    $videoStoragePath = $resourceVideoStream['storage_path'];
                    $urlBaseStorage = sprintf('%s/%s/%s', S3_BASE_URL, AWS_S3_BUCKET_VIDEO_TRANSCODED, $videoStoragePath);
                    $transcoderOutputVersion = new Enum\Video\TranscodeOutputVersion(intval($resourceVideoStream['output_version']));
                    $transcodeOutput = TranscodeOutputVersionDefinition::getVersion($transcoderOutputVersion);

                    $resourceVideoStream['video_sources'] = $transcodeOutput->getVideoSources($urlBaseStorage);
                    $resourceVideoStream['thumbnail'] = $transcodeOutput->getThumbnailUrl($urlBaseStorage);
                    $results[] = $resourceVideoStream;
            }
        }
        return $results;
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
