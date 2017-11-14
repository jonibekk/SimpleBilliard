<?php

App::import('Service', 'AppService');
App::uses('PostResource', 'Model');

use Goalous\Model\Enum as Enum;

/**
 * Class PostResourceService
 */
class PostResourceService extends AppService
{
    public function createPostResource(int $postDraftId, Enum\Post\PostResourceType $postResourceType, int $resourceId)
    {
        /** @var PostResource $PostResource */
        $PostResource = ClassRegistry::init("PostResource");

        $PostResource->create([
            'post_id' => null,
            'post_draft_id' => $postDraftId,
            'resource_type' => $postResourceType->getValue(),
            'resource_id' => $resourceId,
        ]);
        $result = $PostResource->save();
        return reset($result);
    }
}
