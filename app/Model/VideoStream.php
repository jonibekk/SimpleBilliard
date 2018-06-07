<?php
App::uses('AppModel', 'Model');

use Goalous\Enum as Enum;

/**
 * Class VideoStream
 */
class VideoStream extends AppModel
{
    /**
     * Get video_stream from primary-key
     * @param int $videoId
     *
     * @return array
     */
    public function getFirstByVideoId(int $videoId): array
    {
        $options = [
            'fields'     => [
                '*'
            ],
            'conditions' => [
                'video_id' => $videoId,
            ],
        ];

        $result = $this->find('first', $options);
        if (empty($result)) {
            return [];
        }
        return reset($result);
    }

    /**
     * Get multiple video_stream data by transcode status
     * @param array $statuses
     *
     * @return array
     */
    public function getByTranscodeStatus(array $statuses): array
    {
        $options = [
            'conditions' => [
                'transcode_status' => $statuses,
                'del_flg' => false,
            ],
        ];
        $videoStreams = $this->find('all', $options);
        if (is_null($videoStreams)) {
            GoalousLog::error('find error on video_streams', [
                'transcode_status' => $statuses,
            ]);
            return [];
        }

        return Hash::extract($videoStreams, '{n}.VideoStream');
    }

    /**
     * get video_streams that status not changed from passed $timestamp
     * transcode is in progress (QUEUED, TRANSCODING)
     *
     * @param int $timestamp
     *
     * @return array
     */
    public function getNoProgressBeforeTimestamp(int $timestamp): array
    {
        $options = [
            'conditions' => [
                'transcode_status' => [
                    Enum\Video\VideoTranscodeStatus::TRANSCODING,
                    Enum\Video\VideoTranscodeStatus::QUEUED,
                    Enum\Video\VideoTranscodeStatus::UPLOADING,
                    Enum\Video\VideoTranscodeStatus::UPLOAD_COMPLETE,
                ],
                'modified <' => $timestamp,
                'del_flg' => false,
            ],
        ];
        $videoStreams = $this->find('all', $options);
        if (is_null($videoStreams)) {
            GoalousLog::error('find error on video_streams', [
                'modified <' => $timestamp,
            ]);
            return [];
        }

        return Hash::extract($videoStreams, '{n}.VideoStream');
    }
}
