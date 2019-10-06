<?php
App::import('Service', 'AppService');
App::uses('VideoUploadRequestOnPost', 'Model/Video/Requests');
App::uses('VideoStorageClient', 'Model/Video');
App::uses('VideoFileHasher', 'Lib/Video');

App::uses('Video', 'Model');
App::uses('VideoStream', 'Model');
App::uses('User', 'Model');
App::uses('AwsTranscodeJobClient', 'Model/Video');
App::uses('AwsVideoTranscodeJobRequest', 'Model/Video/Requests');
App::uses('TranscodeOutputVersionDefinition', 'Model/Video/Transcode');
App::uses('AwsEtsTranscodeInput', 'Model/Video/Transcode/AwsEtsStructure');
App::uses('VideoTranscodeLog', 'Model');
App::uses('TranscodeOutputVersionDefinition', 'Model/Video/Transcode');
App::uses('UploadVideoStreamRequest', 'Service/Request/VideoStream');
App::uses('Experiment', 'Model');
App::uses('TeamConfig', 'Model');

use Goalous\Enum as Enum;

/**
 * Class VideoStreamService
 */
class VideoStreamService extends AppService
{
    /**
     * Update video_streams data from
     * TranscodeProgressData (currently this data is usually from AWS SNS)
     *
     * @param array                 $videoStream
     * @param TranscodeProgressData $transcodeProgressData
     *
     * @return array
     */
    public function updateFromTranscodeProgressData(array $videoStream, TranscodeProgressData $transcodeProgressData): array
    {
        $videoStreamId = $videoStream['id'];
        $progressState = $transcodeProgressData->getProgressState();

        /** @var VideoStream $VideoStream */
        $VideoStream = ClassRegistry::init('VideoStream');

        $db = $VideoStream->getDataSource();

        $currentVideoStreamStatus = new Enum\Model\Video\VideoTranscodeStatus(intval($videoStream['transcode_status']));
        if ($progressState->equals(Enum\Model\Video\VideoTranscodeProgress::PROGRESS())) {
            // if transcode is started
            if (!$currentVideoStreamStatus->equals(Enum\Model\Video\VideoTranscodeStatus::QUEUED())) {
                throw new RuntimeException("video_streams.id({$videoStreamId}) is not queued");
            }
            $status = Enum\Model\Video\VideoTranscodeStatus::TRANSCODING;

            // DO NOT USE $Model->save() method
            // USE updateAll() for updating video_streams data
            // because if notification came in short interval, save() method will override old value
            $VideoStream->updateAll([
                'transcode_status' => $db->value($status, 'string'),
            ], [
                'VideoStream.id' => $videoStreamId,
            ]);

            $this->logTranscodeEvent($videoStreamId, Enum\Model\Video\VideoTranscodeLogType::STATUS_PROGRESSED(), [
                'progress_state'        => $progressState->getValue(),
                'transcode_status_from' => $currentVideoStreamStatus->getValue(),
                'transcode_status_to'   => $status,
                'job_id'                => $transcodeProgressData->getJobId(),
            ]);

            return $VideoStream->getById($videoStreamId);
        } else if ($progressState->equals(Enum\Model\Video\VideoTranscodeProgress::ERROR())) {
            // if transcode is error
            $status = Enum\Model\Video\VideoTranscodeStatus::ERROR;

            // DO NOT USE $Model->save() method
            // USE updateAll() for updating video_streams data
            // because if notification came in short interval, save() method will override old value
            $VideoStream->updateAll([
                'transcode_status' => $db->value($status, 'string'),
            ], [
                'VideoStream.id' => $videoStreamId,
            ]);

            $this->logTranscodeEvent($videoStreamId, Enum\Model\Video\VideoTranscodeLogType::ERROR(), [
                'progress_state'        => $progressState->getValue(),
                'transcode_status_from' => $currentVideoStreamStatus->getValue(),
                'transcode_status_to'   => $status,
                'reason'                => $transcodeProgressData->getError(),
                'job_id'                => $transcodeProgressData->getJobId(),
            ]);

            return $VideoStream->getById($videoStreamId);
        } else if ($progressState->equals(Enum\Model\Video\VideoTranscodeProgress::WARNING())) {
            // if transcode notifies warning
            $this->logTranscodeEvent($videoStreamId, Enum\Model\Video\VideoTranscodeLogType::WARNING(), [
                'progress_state'   => $progressState->getValue(),
                'transcode_status' => $currentVideoStreamStatus->getValue(),
                'reason'           => $transcodeProgressData->getWarning(),
                'job_id'           => $transcodeProgressData->getJobId(),
            ]);
            return $VideoStream->getById($videoStreamId);
        } else if ($progressState->equals(Enum\Model\Video\VideoTranscodeProgress::COMPLETE())) {
            // if transcode is completed
            $status = Enum\Model\Video\VideoTranscodeStatus::TRANSCODE_COMPLETE;

            $values = [
                'progress_state'        => $progressState->getValue(),
                'transcode_status_from' => $currentVideoStreamStatus->getValue(),
                'transcode_status_to'   => $status,
                'job_id'                => $transcodeProgressData->getJobId(),
            ];

            // if we missed the "PROGRESSING" notification, our video_stream.transcode_status is QUEUED
            // but this has no problem when we receive "COMPLETE" notification
            if (!in_array($currentVideoStreamStatus->getValue(), [
                Enum\Model\Video\VideoTranscodeStatus::TRANSCODING,
                Enum\Model\Video\VideoTranscodeStatus::QUEUED,
            ])) {
                $this->logTranscodeEvent($videoStreamId, Enum\Model\Video\VideoTranscodeLogType::ERROR(), am([
                    'reason' => 'transcoding is not started but notified complete',
                ], $values));
                throw new RuntimeException("video_streams.id({$videoStreamId}) is not transcoding");
            }

            // DO NOT USE $Model->save() method
            // USE updateAll() for updating video_streams data
            // because if notification came in short interval, save() method will override old value
            $VideoStream->updateAll([
                'transcode_status' => $db->value($status, 'string'),
                'duration'         => $db->value($transcodeProgressData->getDuration(), 'string'),
                'aspect_ratio'     => $db->value($transcodeProgressData->getAspectRatio(), 'string'),
                'storage_path'     => $db->value($transcodeProgressData->getOutputKeyPrefix(), 'string'),
            ], [
                'VideoStream.id' => $videoStreamId,
            ]);

            $this->logTranscodeEvent($videoStreamId, Enum\Model\Video\VideoTranscodeLogType::STATUS_PROGRESSED(), $values);
            return $VideoStream->getById($videoStreamId);
        }
        throw new RuntimeException("video_streams.id({$videoStreamId}) is not transcoding");
    }

    public function isVideoStreamBelongsToTeam(int $videoStreamId, int $teamId): bool
    {
        /** @var VideoStream $VideoStream */
        $VideoStream = ClassRegistry::init('VideoStream');
        $conditions = [
            'fields' => [
                'Videos.team_id',
            ],
            'conditions' => [
                'VideoStream.id' => $videoStreamId,
                'VideoStream.del_flg' => false,
            ],
            'joins' => [
                [
                    'table' => 'videos',
                    'alias' => 'Videos',
                    'type' => 'INNER',
                    'conditions' => [
                        'VideoStream.video_id = Videos.id',
                    ],
                ]
            ]
        ];
        $videoSteam = $VideoStream->find('first', $conditions);
        if (empty($videoSteam)) {
            return false;
        }
        return (int)$videoSteam['Videos']['team_id'] === $teamId;
    }

    /**
     * Find video stream by users.id, teams.id, and video hash string.
     * Return video_streams array if find
     *
     * @param int    $userId
     * @param int    $teamId
     * @param string $hash
     *
     * @return array
     */
    public function findVideoStreamIfExists(int $userId, int $teamId, string $hash): array
    {
        /** @var Video $Video */
        $Video = ClassRegistry::init("Video");
        /** @var VideoStream $VideoStream */
        $VideoStream = ClassRegistry::init("VideoStream");

        $video = $Video->getByUserIdAndTeamIdAndHash($userId, $teamId, $hash);
        if (empty($video)) {
            return [];
        }
        $videoId = $video['id'];
        $videoStream = $VideoStream->getFirstByVideoId($videoId);
        if (empty($videoStream)) {
            return [];
        }
        return $videoStream;
    }

    /**
     * return video output path
     *
     * @see https://confluence.goalous.com/display/GOAL/Video+storage+structure
     *
     * @param string $inputS3FileKey
     *
     * @return string
     */
    private function getOutputKeyPrefix(string $inputS3FileKey): string
    {
        // e.g.
        // $this->inputS3FileKey() = uploads/<env_name>/111/222/abcdef1234567890/original
        // return 'streams/<env_name>/111/222/abcdef1234567890/'

        $urlSplits = array_slice(explode('/', trim($inputS3FileKey, '/')), 1, -1);
        return sprintf('streams/%s/', implode($urlSplits, '/'));
    }

    /**
     * Upload and transcode video stream
     *
     * @param UploadVideoStreamRequest $uploadVideoStreamRequest
     * @return array
     * @throws Exception
     */
    public function uploadVideoStream(UploadVideoStreamRequest $uploadVideoStreamRequest): array
    {
        /** @var Video $Video */
        $Video = ClassRegistry::init("Video");
        /** @var VideoStream $VideoStream */
        $VideoStream = ClassRegistry::init("VideoStream");
        /** @var User $User */
        $User = ClassRegistry::init("User");
        /** @var VideoTranscodeLog $VideoTranscodeLog */
        $VideoTranscodeLog = ClassRegistry::init('VideoTranscodeLog');

        $user = $User->getById($uploadVideoStreamRequest->getUserId());
        if (is_null($user)) {
            throw new NotFoundException(sprintf('user(%d) not found', $uploadVideoStreamRequest->getUserId()));
        }
        $userId = $user['id'];

        $filePath = $uploadVideoStreamRequest->getUploadFile()['tmp_name'];
        $fileName = $uploadVideoStreamRequest->getUploadFile()['name'];

        $transcodeOutputVersion = TeamStatus::getCurrentTeam()->getTranscodeOutputVersion();

        $hash = VideoFileHasher::hashFile(new \SplFileInfo($filePath));

        $teamId = $uploadVideoStreamRequest->getTeamId();
        $videoStreamIfExists = $this->findVideoStreamIfExists($userId, $teamId, $hash);
        if (!empty($videoStreamIfExists)) {
            GoalousLog::info('uploaded same hash video exists', [
                'user_id'          => $userId,
                'team_id'          => $teamId,
                'hash'             => $hash,
                'video_streams.id' => $videoStreamIfExists['id'],
            ]);
            return $videoStreamIfExists;
        }
        GoalousLog::info('uploaded same hash video NOT exists', [
            'user_id' => $userId,
            'team_id' => $teamId,
            'hash'    => $hash,
        ]);

        // create video, video_stream
        // need to be create for Storage Meta data to save ids
        $Video->create([
            'user_id'       => $userId,
            'team_id'       => $teamId,
            'duration'      => null,// currently cant estimate (need ffprove or something)
            'width'         => null,// (same as above)
            'height'        => null,// (same as above)
            'hash'          => $hash,
            'file_size'     => filesize($filePath),
            'file_name'     => $fileName,
            'resource_path' => null,
        ]);
        $video = $Video->save();
        $video = reset($video);
        $VideoStream->create([
            'video_id'         => $video['id'],
            'duration'         => null,
            'aspect_ratio'     => null,
            'storage_path'     => null,
            'transcode_status' => Enum\Model\Video\VideoTranscodeStatus::UPLOADING,
            'output_version'   => $transcodeOutputVersion->getValue(),
        ]);
        $videoStream = $VideoStream->save();
        $videoStream = reset($videoStream);
        $videoStreamId = $videoStream['id'];

        $this->logTranscodeEvent($videoStreamId, Enum\Model\Video\VideoTranscodeLogType::STATUS_PROGRESSED(), [
            'transcode_status' => Enum\Model\Video\VideoTranscodeStatus::UPLOADING,
            'user_id'          => $userId,
            'team_id'          => $teamId,
        ]);

        // video upload
        $request = new VideoUploadRequestOnPost(new SplFileInfo($filePath), $user, $teamId, $video, $videoStream);
        $request->setFileHash($hash);
        $result = VideoStorageClient::upload($request);
        if (!$result->isSucceed()) {
            GoalousLog::error('video uploading to storage failed', [
                'code'             => $result->getErrorCode(),
                'message'          => $result->getErrorMessage(),
                'videos.id'        => $video['id'],
                'video_streams.id' => $videoStreamId,
            ]);
            $Video->softDelete($video['id'], false);
            $errorMessage = "failed upload video:" . $result->getErrorMessage();
            $this->deleteVideoStreamWithError($videoStream, $errorMessage);
            throw new RuntimeException($errorMessage);
        }

        // Upload complete
        $resourcePath = $result->getResourcePath();
        $video['resource_path'] = $resourcePath;
        $Video->save($video);

        $videoStream['transcode_status'] = Enum\Model\Video\VideoTranscodeStatus::UPLOAD_COMPLETE;
        $VideoStream->save($videoStream);

        $this->logTranscodeEvent($videoStreamId, Enum\Model\Video\VideoTranscodeLogType::STATUS_PROGRESSED(), [
            'transcode_status' => Enum\Model\Video\VideoTranscodeStatus::UPLOAD_COMPLETE,
            'resource_path'    => $resourcePath,
        ]);

        // create transcode job
        $transcodeRequest = new AwsVideoTranscodeJobRequest(
            $this->getOutputKeyPrefix($resourcePath),
            AWS_ELASTIC_TRANSCODER_PIPELINE_ID,
            $transcodeOutputVersion
        );
        $inputVideo = new AwsEtsTranscodeInput($resourcePath);
        $inputVideo->setTimeSpan($uploadVideoStreamRequest->getSecondsDurationLimit(), 0);
        $transcodeRequest->addInputVideo($inputVideo);
        $transcodeRequest->setUserMetaData([
            'videos.id'        => $video['id'],
            'video_streams.id' => $videoStreamId,
            'env'              => ENV_NAME,
        ]);
        // set watermark if env is not production
        $transcodeRequest->setPutWaterMark(in_array(ENV_NAME, ['local', 'dev', 'stage']));
        $createJobResult = AwsTranscodeJobClient::createJob($transcodeRequest);
        if (!$createJobResult->isSucceed()) {
            // failed to create transcode job
            // not doing transaction, set status = ERROR, set delete flag
            GoalousLog::error('creating video transcode job failed', [
                'code'             => $createJobResult->getErrorCode(),
                'message'          => $createJobResult->getErrorMessage(),
                'videos.id'        => $video['id'],
                'video_streams.id' => $videoStreamId,
            ]);
            $Video->softDelete($video['id'], false);
            $errorMessage = "creating video transcode job failed:" . $createJobResult->getErrorMessage();
            $this->deleteVideoStreamWithError($videoStream, $errorMessage);
            throw new RuntimeException($errorMessage);
        }

        // Queued complete
        $videoStream['transcode_status'] = Enum\Model\Video\VideoTranscodeStatus::QUEUED;
        $VideoStream->save($videoStream);

        $this->logTranscodeEvent($videoStreamId, Enum\Model\Video\VideoTranscodeLogType::STATUS_PROGRESSED(), [
            'transcode_status' => Enum\Model\Video\VideoTranscodeStatus::QUEUED,
            'job_id'           => $createJobResult->getJobId(),
        ]);

        return $videoStream;
    }

    private function logTranscodeEvent(int $videoStreamId, Enum\Model\Video\VideoTranscodeLogType $message, array $values)
    {
        /** @var VideoTranscodeLog $VideoTranscodeLog */
        $VideoTranscodeLog = ClassRegistry::init('VideoTranscodeLog');
        $VideoTranscodeLog->add($videoStreamId, $message, $values);
        GoalousLog::info(sprintf('video transcode: %s', $message->getValue()), am([
            'video_streams.id' => $videoStreamId,
        ], $values));
    }

    /**
     * Delete video stream with error message
     *
     * @param array  $videoStream
     * @param string $errorMessage
     */
    private function deleteVideoStreamWithError(array $videoStream, string $errorMessage)
    {
        $videoStreamId = $videoStream['id'];

        /** @var VideoStream $VideoStream */
        $VideoStream = ClassRegistry::init("VideoStream");
        $videoStream['transcode_status'] = Enum\Model\Video\VideoTranscodeStatus::ERROR;
        $VideoStream->save($videoStream);
        $VideoStream->softDelete($videoStreamId, false);

        $this->logTranscodeEvent($videoStreamId, Enum\Model\Video\VideoTranscodeLogType::ERROR(), [
            'reason' => $errorMessage,
        ]);
    }

    public function isAllCompletedTrancode(array $videoStreamIds): bool
    {
        if (empty($videoStreamIds)) {
            return true;
        }
        /** @var VideoStream $VideoStream */
        $VideoStream = ClassRegistry::init("VideoStream");

        $transcodeStatus = $VideoStream->find('all', [
            'conditions' => [
                'id' => $videoStreamIds,
            ],
            'fields'     => [
                'VideoStream.transcode_status'
            ]
        ]);
        $transcodeStatuses = Hash::extract($transcodeStatus, '{n}.VideoStream');
        foreach ($transcodeStatuses as $transcodeStatus) {
            if ((int)$transcodeStatus['transcode_status'] !== Enum\Model\Video\VideoTranscodeStatus::TRANSCODE_COMPLETE) {
                return false;
            }
        }

        return true;
    }

    public function getVideoStreamForPlayer(int $videoStreamId, bool $setDirectUrl = false)
    {
        /** @var VideoStream $VideoStream */
        $VideoStream = ClassRegistry::init("VideoStream");

        $resourceVideoStream = $VideoStream->getById($videoStreamId);
        if (empty($resourceVideoStream)) {
            return false;
        }
        $videoStoragePath = $resourceVideoStream['storage_path'];
        $urlBaseStorage = sprintf('%s/%s/%s', S3_BASE_URL, AWS_S3_BUCKET_VIDEO_TRANSCODED, $videoStoragePath);
        $transcoderOutputVersion = new Enum\Model\Video\TranscodeOutputVersion(intval($resourceVideoStream['output_version']));
        $transcodeOutput = TranscodeOutputVersionDefinition::getVersion($transcoderOutputVersion);

        $videoSources = [];
        foreach ($transcodeOutput->getVideoSources($urlBaseStorage) as $videoSource) {
            $source = [
                'video_stream_id' => $videoStreamId,
                'type'            => $videoSource->getType()->getValue(),
            ];
            if ($setDirectUrl && $videoSource->getType()->equals(Goalous\Enum\Model\Video\VideoSourceType::PLAYLIST_M3U8_HLS())) {
                $source['url'] = $urlBaseStorage . 'playlist.m3u8';
            }
            array_push($videoSources, $source);
        }
        $resourceVideoStream['video_sources'] = $videoSources;
        $resourceVideoStream['thumbnail'] = $transcodeOutput->getThumbnailUrl($urlBaseStorage);
        $resourceVideoStream['file_type'] = Enum\Model\Post\PostResourceType::VIDEO_STREAM;
        $resourceVideoStream['resource_type'] = Enum\Model\Post\PostResourceType::VIDEO_STREAM;

        return $resourceVideoStream;
    }

    /**
     * Decide current user browser is supporting redirecting on HLS video play.
     *
     * @see
     *     https://github.com/videojs/videojs-contrib-hls/pull/912#discussion_r164196518
     *     https://developer.mozilla.org/ja/docs/XMLHttpRequest/responseURL
     *     IE11 does not have responseURL property,
     *     unless we are redirecting, IE11 cant play video if we redirects Cross-Origin
     *
     * @return bool
     */
    public function isBrowserSupportManifestRedirects(): bool
    {
        try {
            $currentBrowser = (new \BrowscapPHP\Browscap())->getBrowser();
        } catch (Exception $e) {
            // logging on info
            // not critical if exception throws on here
            GoalousLog::info('Failed to detect browser', [
                'message' => $e->getMessage(),
            ]);
            return false;
        }

        $browser = strtolower(trim($currentBrowser->browser ?? ''));
        $version = intval($currentBrowser->majorver ?? 0);

        // IE is not supporting
        if ('ie' === $browser && $version <= 11) {
            return false;
        }

        return true;
    }

    /**
     * Soft delete entries in video_streams by their ids
     *
     * @param array $videoStreamIds
     *
     * @throws Exception
     */
    public function deleteStreamsAndVideos(array $videoStreamIds)
    {
        /** @var Video $Video */
        $Video = ClassRegistry::init('Video');
        /** @var VideoStream $VideoStream */
        $VideoStream = ClassRegistry::init('VideoStream');
        try {
            $this->TransactionManager->begin();
            $videoIds = $VideoStream->getVideoIds($videoStreamIds);
            $result = $VideoStream->softDeleteAll(['id' => $videoStreamIds], false) &&
                $Video->softDeleteAll(['id' => $videoIds], false);
            if (!$result) {
                throw new RuntimeException();
            }
            $this->TransactionManager->commit();
        } catch (Exception $e) {
            $this->TransactionManager->rollback();
            GoalousLog::error("Failed to delete video_streams", ['video_stream_ids' => $videoStreamIds]);
            throw $e;
        }
    }

    public function getTeamVideoDurationLimit(int $teamId): int
    {
        /** @var TeamConfig $TeamConfig */
        $TeamConfig = ClassRegistry::init('TeamConfig');
        /** @var TeamService $TeamService */
        $TeamService = ClassRegistry::init('TeamService');
        $serviceUserStatus = $TeamService->getServiceUseStatusByTeamId($teamId);

        $defaultVideoDurationLimit = ($serviceUserStatus === Enum\Model\Team\ServiceUseStatus::PAID)
            ? VIDEO_MAX_DURATION_SECONDS_PAID : VIDEO_MAX_DURATION_SECONDS_LIMITED;

        $teamConfig = $TeamConfig->getConfig($teamId);

        return $teamConfig->getVideoDurationMaxSecond() ?? $defaultVideoDurationLimit;
    }
}
