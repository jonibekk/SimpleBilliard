<?php

App::uses('VideoStream', 'Model');

use Goalous\Model\Enum as Enum;

/**
 * local environment can not receive push(HTTPS POST) from AWS SNS
 * local have to pull from AWS ETS(by AWS API)
 *
 *
 * this batch shell only use in local env
 */
class PullTranscodeProgressShell extends AppShell
{

    var $uses = [
    ];

    function startup()
    {
    }

    public function getOptionParser()
    {
        $parser = parent::getOptionParser();
        $options = [

        ];
        $parser->addOptions($options);
        return $parser;
    }

    function main()
    {
        /** @var VideoStream $VideoStream */
        $VideoStream = ClassRegistry::init('VideoStream');
        $videoStreamsToCheckStatus = $VideoStream->getByStatusTranscode([
            Enum\Video\VideoTranscodeStatus::TRANSCODING,
            Enum\Video\VideoTranscodeStatus::QUEUED,
        ]);

        $videoStreamIds = array_map(function($videoStream) {
            GoalousLog::info($videoStream['id']);
            return $videoStream['id'];
        }, $videoStreamsToCheckStatus);

        GoalousLog::info('video_stream.ids to check', [
            'ids' => $videoStreamIds,
        ]);

        $jobs = [];
        // TODO: end if not ENV is local
        try {
            $client = \Aws\ElasticTranscoder\ElasticTranscoderClient::factory([
                'region'   => 'ap-northeast-1',
                'credentials' => [
                    'key'    => "AKIAJWRB3ISRYGDYHV5A",
                    'secret' => "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",
                ],
            ]);
            $jobs = $client->listJobsByPipeline([
                'PipelineId' => '1509328826229-a6j5yu',
                'Ascending'  => 'false',// this need to be string
            ]);
            $jobs = $jobs->toArray();
        } catch (Exception $e) {
            GoalousLog::error('error while getting transcoding statuses', [
                'message' => $e->getMessage(),
            ]);
        }


        foreach ($jobs['Jobs'] as $job) {
            if (
                !isset($job['UserMetadata']['video_streams.id'])
                || !is_numeric($job['UserMetadata']['video_streams.id'])) {
                GoalousLog::info('job does not have meta-data video_streams.id', [
                    'job_id' => $job['Id'],
                ]);
                continue;
            }
            if (!in_array($job['UserMetadata']['video_streams.id'], $videoStreamIds)) {
                continue;
            }
            // remove video_streams.id from $videoStreamIds to notify the recent transcode job at once
            unset($videoStreamIds[array_search($job['UserMetadata']['video_streams.id'], $videoStreamIds)]);
            try {
                $notificationJson = $this->createTranscodeNotificationJson($job);

                GoalousLog::info('SNS notification from local', [
                    'job_id' => $job['Id'],
                    'video_streams.id' => $job['UserMetadata']['video_streams.id'],
                    'status' => $job['Status'],
                ]);
                $client = new \GuzzleHttp\Client();
                $request = $client->post('http://localhost/api/v1/api_posts/video/callback', [
                    GuzzleHttp\RequestOptions::BODY => $notificationJson,
                ]);
            } catch (Exception $e) {
                GoalousLog::error('error', [
                    'message' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * @see here for json format
     * - [SNS] AWS SNS notification json format
     *   - http://docs.aws.amazon.com/sns/latest/dg/json-formats.html#http-notification-json
     * - [ETS] elastic transcoder notification json
     *   - http://docs.aws.amazon.com/elastictranscoder/latest/developerguide/notifications.html
     *
     * @param array $transcodeJob
     *
     * @return string
     */
    private function createTranscodeNotificationJson(array $transcodeJob): string
    {
        $status = $transcodeJob['Status'];

        /**
         * @see
         * transcode job format from API
         * http://docs.aws.amazon.com/ja_jp/elastictranscoder/latest/developerguide/get-job.html
         *
         */
        $jobState = null;
        switch ($status) {
            case 'Progressing':
                $jobState = 'PROGRESSING';
                break;
            case 'Complete':
                $jobState = 'COMPLETED';
                break;
            case 'Error':
                $jobState = 'ERROR';
                break;
            case 'Submitted':
            case 'Canceled':
            default:
                throw new RuntimeException("current transcode status does not notify: " . $status);
                break;
        }

        $transcodeJob['Input'] = self::changeKeyCaseToLower($transcodeJob['Input'], CASE_LOWER);
        $transcodeJob['Outputs'] = self::changeKeyCaseToLower($transcodeJob['Outputs'], CASE_LOWER);
        $transcodeJob['Playlists'] = self::changeKeyCaseToLower($transcodeJob['Playlists'], CASE_LOWER);
        return json_encode([
            // [SNS] part format
            "Type" => "Notification",
            "MessageId" => "22b80b92-fdea-4c2c-8f9d-example",
            "TopicArn" => "arn:aws:sns:us-west-2:123456789012:MyTopic",
            "Subject" => "message for transcoder",
            "Message" => json_encode(
                    // [ETS] part format
                    [
                        'state' => $jobState,
                        'errorCode' => 'any error message that occurred',
                        'messageDetails' => "the notification message",
                        'version' => '2012-09-25',
                        'jobId' => $transcodeJob['Id'],
                        'pipelineId' => $transcodeJob['PipelineId'],
                        'input' => $transcodeJob['Input'],
                        'outputKeyPrefix' => $transcodeJob['OutputKeyPrefix'],
                        'outputs' => $transcodeJob['Outputs'],
                        'playlists' => $transcodeJob['Playlists'],
                        'userMetadata' => $transcodeJob['UserMetadata'],
                    ]),
            "Timestamp" => GoalousDateTime::now()->toIso8601String(),
            "SignatureVersion" => "1",
            "Signature" => "EXAMPLE_EXAMPLE_EXAMPLE_EXAMPLE_EXAMPLE_EXAMPLE_EXAMPLE_EXAMPLE_EXAMPLE_EXAMPLE_EXAMPLE_EXAMPLE_EXAMPLE_EXAMPLE_EXAMPLE_EXAMPLE_EXAMPLE_EXAMPLE_EXAMPLE_EXAMPLE_EXAMPLE_=",
            "SigningCertURL" => "https://sns.us-west-2.amazonaws.com/SimpleNotificationService-test.pem",
            "UnsubscribeURL" => "https://sns.us-west-2.amazonaws.com/?Action=Unsubscribe",
        ]);
    }

    private static function changeKeyCaseToLower(array $arr): array
    {
        $ret = [];
        foreach ($arr as $key => $item) {
            $key = strtolower($key);
            if (is_array($item)) {
                $item = self::changeKeyCaseToLower($item);
            }
            $ret[$key] = $item;
        }
        return $ret;
    }
}
