<?php
App::uses('VideoStream', 'Model');
App::uses('Video', 'Model');

use Goalous\Model\Enum as Enum;

trait TestPostDraftTrait
{
    /**
     * @var PostDraft
     */
    public $PostDraft = null;

    /**
     * @var PostResource
     */
    public $PostResource = null;

    /**
     * Create post_draft and post_resource
     * returning post_draft and post_resource data array.
     *
     * @param int    $userId
     * @param int    $teamId
     * @param array  $videoStream
     * @param string $bodyText
     *
     * @return array list($postDraft, $postResource)
     */
    private function createPostDraftWithVideoStreamResource(int $userId, int $teamId, array $videoStream, string $bodyText): array
    {
        $postDraft = $this->PostDraft->create([
            'user_id'    => $userId,
            'team_id'    => $teamId,
            'post_id'    => null,
            'draft_data' => json_encode([
                "socket_id" => "226623.3380922",
                "Post"      => [
                    "body"          => $bodyText,
                    "site_info_url" => "",
                    "redirect_url"  => "",
                    "share_public"  => "public",
                    "share_secret"  => "",
                    "share_range"   => "public",
                    "share"         => "public",
                ],
                "video_stream_id" => [
                    $videoStream['id'],
                ],
            ]),
        ]);
        $postDraft = $this->PostDraft->save($postDraft);
        $postResource = $this->PostResource->create([
            'post_id'       => null,
            'post_draft_id' => $postDraft['PostDraft']['id'],
            'resource_type' => Enum\Post\PostResourceType::VIDEO_STREAM,
            'resource_id'   => $videoStream['id'],
        ]);
        $postResource = $this->PostResource->save($postResource);
        return [reset($postDraft), reset($postResource)];
    }
}