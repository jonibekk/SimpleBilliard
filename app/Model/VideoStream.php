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
}
