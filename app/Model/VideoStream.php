<?php
App::uses('AppModel', 'Model');

use Goalous\Model\Enum as Enum;

/**
 * Class VideoStream
 */
class VideoStream extends AppModel
{
    public function setTranscodeInfo(array $videoStream, string $key, $value)
    {
        $infos = [];
        if (!isset($videoStream['id'])) {
            GoalousLog::warning('video_streams.id is undefined');
        }
        if (!isset($videoStream['transcode_info']) || !is_string($videoStream['transcode_info'])) {
            GoalousLog::notice('video_streams.transcode_info is not set', [
                'video_streams.id' => video_streams['id'],
            ]);
        }
        $currentTranscodeInfo = json_decode($videoStream['transcode_info'], true);
        if (is_null($currentTranscodeInfo)) {
            GoalousLog::notice('video_streams.transcode_info is not json', [
                'video_streams.id' => video_streams['id'],
            ]);
        }
        $currentTranscodeInfo[$key] = $value;
        $this->save([
            'id' => $videoStream['id'],
            'transcode_info' => json_encode($currentTranscodeInfo),
        ]);
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
