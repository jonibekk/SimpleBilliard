<?php
App::uses('AppModel', 'Model');

use Goalous\Model\Enum as Enum;

/**
 * Class VideoTranscodeLog
 */
class VideoTranscodeLog extends AppModel
{
    /**
     * Logging the transcode logs
     * Insert new record to video_transcode_logs
     *
     * @param int    $videoStreamId
     * @param string $message
     * @param array  $values
     */
    public function add(int $videoStreamId, string $message, array $values = [])
    {
        $this->create();
        $this->save([
            'video_streams_id'  => $videoStreamId,
            'log'               => self::createJsonLogString($message, $values),
        ]);
    }

    /**
     * Return json string to store in video_transcode_logs.log
     *
     * @param string $message
     * @param array  $values
     *
     * @return string
     */
    private static function createJsonLogString(string $message, array $values): string
    {
        return json_encode([
            'message' => $message,
            'values'  => $values,
        ]);
    }
}
