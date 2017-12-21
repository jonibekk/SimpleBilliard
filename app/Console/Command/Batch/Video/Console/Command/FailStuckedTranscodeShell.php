<?php
App::uses('AppModel', 'Model');
App::uses('VideoStream', 'Model');

use Goalous\Model\Enum as Enum;

class FailStuckedTranscodeShell extends AppShell
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
                'help'    => 'no progress for passed second decided to be trasncode stopped',
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
        $stuckedVideoStreams = $VideoStream->getNoProgressBeforeTimestamp(
            $targetDateTime->getTimestamp()
        );

        GoalousLog::info('fetched stucked video stream', [
            'count' => count($stuckedVideoStreams),
        ]);

        foreach ($stuckedVideoStreams as $videoStream) {
            GoalousLog::info('stucked video stream change to error', [
                'id' => $videoStream['id'],
                'status_transcode'   => $videoStream['status_transcode'],
                'elapse time from modified'   => GoalousDateTime::now()->diffInSeconds(
                    GoalousDateTime::createFromTimestamp($videoStream['modified'])
                ),
            ]);
            $videoStream['status_transcode'] = Enum\Video\VideoTranscodeStatus::ERROR();
            $transcodeInfo = $VideoStream->getTranscodeInfo($videoStream);
            $transcodeInfo->setTranscodeNoProgress(true);
            $videoStream['transcode_info'] = $transcodeInfo->toJson();
            $VideoStream->save($videoStream);
        }
    }

}
