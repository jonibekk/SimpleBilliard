<?php
App::uses('AppModel', 'Model');
App::uses('VideoStream', 'Model');

use Goalous\Model\Enum as Enum;

/**
 * Class PostResource
 */
class PostResource extends AppModel
{
    function getResourcesByPostDraftId(int $postDraftId): array
    {
        $options = [
            'fields'     => [
                '*'
            ],
            'conditions' => [
                'post_draft_id' => $postDraftId,
            ],
        ];
        $postResources = Hash::extract($this->find('all', $options), '{n}.PostResource');

        /** @var VideoStream $VideoStream */
        $VideoStream = ClassRegistry::init('VideoStream');

        return array_map(function($postResource) use ($VideoStream) {
            $resourceType = new Enum\Post\PostResourceType(intval($postResource['resource_type']));
            switch ($resourceType->getValue()) {
                case Enum\Post\PostResourceType::VIDEO_STREAM:
                    return $VideoStream->getById($postResource['resource_id']);
            }
        }, $postResources);
    }

    private function getResourceVideoStream(int $videoStreamId): array
    {

    }
}
