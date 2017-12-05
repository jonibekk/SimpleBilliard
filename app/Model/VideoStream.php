<?php
App::uses('AppModel', 'Model');

/**
 * Class VideoStream
 */
class VideoStream extends AppModel
{
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
}
