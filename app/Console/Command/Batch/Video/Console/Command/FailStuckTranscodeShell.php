<?php
App::uses('VideoStream', 'Model');

use Goalous\Model\Enum as Enum;

/**
 * This batch shell is for video posting
 *
 * If transcoding is taking too long
 * or we could notify anything about transcode progress,
 * this batch shell decide the video is failed to transcode
 *
 * @uses
 * ./Console/cake Video.fail_stuck_transcode --seconds_to_be_no_progress=3600
 *
 * Class FailStuckTranscodeShell
 */
class FailStuckTranscodeShell extends AppShell
{
    public function startup()
    {
        parent::startup();
    }

    public function getOptionParser()
    {
        $parser = parent::getOptionParser();
        $options = [
            'seconds_to_be_no_progress' => [
                'help'    => 'no progress for passed second decided to be transcode stopped',
                'default' => 3600,
            ],
        ];
        $parser->addOptions($options);
        return $parser;
    }

    public function main()
    {
        $seconds = Hash::get($this->params, 'seconds_to_be_no_progress');
        $targetDateTime = GoalousDateTime::now()->subSecond($seconds);
        GoalousLog::info('target base time to decide transcode stopped', [
            'seconds_to_be_no_progress' => $seconds,
            'timestamp' => $targetDateTime->getTimestamp(),
            'date'      => $targetDateTime->toIso8601String(),
        ]);

        /** @var VideoStream $VideoStream */
        $VideoStream = ClassRegistry::init('VideoStream');
        $stuckVideoStreams = $VideoStream->getNoProgressBeforeTimestamp(
            $targetDateTime->getTimestamp()
        );

        GoalousLog::info('fetched stuck video stream', [
            'count' => count($stuckVideoStreams),
        ]);

        foreach ($stuckVideoStreams as $videoStream) {
            GoalousLog::info('stuck video stream change to error', [
                'video_streams.id' => $videoStream['id'],
                'transcode_status'   => $videoStream['transcode_status'],
                'elapse time from modified'   => GoalousDateTime::now()->diffInSeconds(
                    GoalousDateTime::createFromTimestamp($videoStream['modified'])
                ),
            ]);
            $videoStream['transcode_status'] = Enum\Video\VideoTranscodeStatus::ERROR();

            // 'transcode_info'   => TranscodeInfo::createNew()->toJson(), // TODO: replace using TranscodeInfo to video_transcode_logs
            //$transcodeInfo = $VideoStream->getTranscodeInfo($videoStream);
            //$transcodeInfo->setTranscodeNoProgress(true);
            //$videoStream['transcode_info'] = $transcodeInfo->toJson();
            if (false === $VideoStream->save($videoStream)) {
                GoalousLog::error('save failed on stuck video stream', [
                    'video_streams.id' => $videoStream['id'],
                ]);
            }
        }
    }

}
