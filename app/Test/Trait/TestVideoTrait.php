<?php
App::uses('VideoStream', 'Model');
App::uses('Video', 'Model');

use Goalous\Model\Enum as Enum;

trait TestVideoTrait
{
    /**
     * @var VideoStream
     */
    public $VideoStream;

    /**
     * @var Video
     */
    public $Video;

    /**
     * Create video and video_stream
     * returning video and video_stream data array.
     *
     * @param int                             $userId
     * @param int                             $teamId
     * @param string                          $hash
     * @param Enum\Video\VideoTranscodeStatus $status
     *
     * @return array list($video, $videoStream)
     */
    protected function createVideoSet(int $userId, int $teamId, string $hash, Enum\Video\VideoTranscodeStatus $status): array
    {
        $this->Video->create();
        $video = $this->Video->save([
            'user_id'       => $userId,
            'team_id'       => $teamId,
            'duration'      => 60,
            'width'         => 640,
            'height'        => 360,
            'hash'          => $hash,
            'file_size'     => 1024,
            'file_name'     => "video.mp4",
            'resource_path' => "uploads/{$userId}/{$teamId}/{$hash}/original",
        ]);
        $isTranscodeCompleted = $status->equals(Enum\Video\VideoTranscodeStatus::TRANSCODE_COMPLETE());

        $this->VideoStream->create();
        $videoStream = $this->VideoStream->save([
            'video_id'             => $video['Video']['id'],
            'duration'             => $isTranscodeCompleted ? 60 : null,
            'aspect_ratio'         => $isTranscodeCompleted ? (640 / 360) : null,
            'storage_path'         => $isTranscodeCompleted ? "streams/{$userId}/{$teamId}/{$hash}/" : null,
            'output_version'       => Enum\Video\TranscodeOutputVersion::V1,
            'transcode_status'     => $status->getValue(),
        ]);
        return [reset($video), reset($videoStream)];
    }
}