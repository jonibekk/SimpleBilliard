<?php
App::uses('AppModel', 'Model');
App::uses('VideoStream', 'Model');

use Goalous\Model\Enum as Enum;

/**
 * Class PostResource
 */
class PostResource extends AppModel
{
    const COLUMN_POST = 'post_id';
    const COLUMN_POST_DRAFT = 'post_draft_id';

    function getResourcesByPostId(int $postDraftId): array
    {
        return $this->getResourcesByPostOrPostDraftId($postDraftId, static::COLUMN_POST);
    }

    function getResourcesByPostDraftId(int $postDraftId): array
    {
        return $this->getResourcesByPostOrPostDraftId($postDraftId, static::COLUMN_POST_DRAFT);
    }

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
        $postResources = Hash::extract($this->find('all', $options), '{n}.PostResource');

        /** @var VideoStream $VideoStream */
        $VideoStream = ClassRegistry::init('VideoStream');

        $results = [];
        foreach ($postResources as $postResource) {
            $resourceType = new Enum\Post\PostResourceType(intval($postResource['resource_type']));
            switch ($resourceType->getValue()) {
                case Enum\Post\PostResourceType::VIDEO_STREAM:
                    $resourceVideoStream = $VideoStream->getById($postResource['resource_id']);
                    if (empty($resourceVideoStream)) {
                        continue;
                    }
                    $playlistPath = $resourceVideoStream['master_playlist_path'];
                    // TODO: define fqdn to extra_define
                    if (ENV_NAME == 'local') {
                        $playlistPath = 'https://s3-ap-northeast-1.amazonaws.com/goalous-local-masuichig-videos/' . $playlistPath;
                    } else if (ENV_NAME == 'dev') {
                        $playlistPath = 'https://s3-ap-northeast-1.amazonaws.com/goalous-dev-videos/' . $playlistPath;
                    }
                    $resourceVideoStream['playlist_path'] = $playlistPath;
                    $results[] = $resourceVideoStream;
            }
        }
        return $results;
    }

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
