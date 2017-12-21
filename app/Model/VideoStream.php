<?php
App::uses('AppModel', 'Model');
App::uses('TranscodeInfo', 'Lib/Video/Stream');

use Goalous\Model\Enum as Enum;

/**
 * Class VideoStream
 */
class VideoStream extends AppModel
{
    public function getTranscodeInfo(array $videoStream): TranscodeInfo
    {
        return TranscodeInfo::createFromJson($videoStream['transcode_info']);
    }

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

    public function getByStatusTranscode(array $statuses): array
    {
        $options = [
            'conditions' => [
                'status_transcode' => $statuses,
                'del_flg' => 0,
            ],
        ];

        return Hash::extract($this->find('all', $options), '{n}.VideoStream');
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
                'status_transcode' => [
                    Enum\Video\VideoTranscodeStatus::TRANSCODING,
                    Enum\Video\VideoTranscodeStatus::QUEUED,
                    Enum\Video\VideoTranscodeStatus::UPLOADING,
                    Enum\Video\VideoTranscodeStatus::UPLOAD_COMPLETE,
                ],
                'modified <' => $timestamp,
                'del_flg' => 0,
            ],
        ];
        return Hash::extract($this->find('all', $options), '{n}.VideoStream');
    }
}
