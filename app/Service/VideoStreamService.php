<?php
App::import('Service', 'AppService');
App::uses('VideoUploadRequestOnPost', 'Model/Video/Requests');
App::uses('VideoStorageClient', 'Model/Video');
App::uses('VideoFileHasher', 'Lib/Video');

App::uses('Video', 'Model');
App::uses('VideoStream', 'Model');
App::uses('AwsTranscodeJobClient', 'Model/Video');
App::uses('AwsVideoTranscodeJobRequest', 'Model/Video/Requests');
App::uses('TranscodeOutputVersionDefinition', 'Model/Video/Transcode');
App::uses('AwsEtsTranscodeInput', 'Model/Video/Transcode/AwsEtsStructure');

use Goalous\Model\Enum as Enum;

/**
 * Class VideoStreamService
 */
class VideoStreamService extends AppService
{
    public function updateFromTranscodeProgressData(array $videoStream, TranscodeProgressData $transcodeProgressData): array
    {
        $videoStreamId = $videoStream['id'];
        $progressState = $transcodeProgressData->getProgressState();

        /** @var VideoStream $VideoStream */
        $VideoStream = ClassRegistry::init('VideoStream');
        $currentVideoStreamStatus = new Enum\Video\VideoTranscodeStatus(intval($videoStream['status_transcode']));
        if ($progressState->equals(Enum\Video\VideoTranscodeProgress::PROGRESS())) {
            // if transcode is started
            if (!$currentVideoStreamStatus->equals(Enum\Video\VideoTranscodeStatus::QUEUED())) {
                throw new RuntimeException("video_streams.id({$videoStreamId}) is not queued");
            }
            $status = Enum\Video\VideoTranscodeStatus::TRANSCODING;
            $videoStream['status_transcode'] = $status;

            $transcodeInfo = $VideoStream->getTranscodeInfo($videoStream);
            $transcodeInfo->setTranscodeJobId($transcodeProgressData->getJobId());
            $videoStream['transcode_info'] = $transcodeInfo->toJson();
            $VideoStream->save($videoStream);
            GoalousLog::info('transcode status changed', [
                'video_streams.id' => $videoStreamId,
                'state' => $progressState->getValue(),
                'status_value_from' => $currentVideoStreamStatus->getValue(),
                'status_value_to' => $status,
            ]);
            return $videoStream;
        } else if ($progressState->equals(Enum\Video\VideoTranscodeProgress::ERROR())) {
            // if transcode is error
            $status = Enum\Video\VideoTranscodeStatus::ERROR;
            $videoStream['status_transcode'] = $status;

            $transcodeInfo = $VideoStream->getTranscodeInfo($videoStream);
            $transcodeInfo->setTranscodeJobId($transcodeProgressData->getJobId());
            if ($transcodeProgressData->isError()) {
                $transcodeInfo->addTranscodeError($transcodeProgressData->getError());
            }
            $videoStream['transcode_info'] = $transcodeInfo->toJson();

            $VideoStream->save($videoStream);
            GoalousLog::info('transcode status changed', [
                'video_streams.id' => $videoStreamId,
                'state' => $progressState->getValue(),
                'status_value_from' => $currentVideoStreamStatus->getValue(),
                'status_value_to' => $status,
            ]);
            return $videoStream;
        } else if ($progressState->equals(Enum\Video\VideoTranscodeProgress::WARNING())) {
            // if transcode notified warning
            $transcodeInfo = $VideoStream->getTranscodeInfo($videoStream);
            $transcodeInfo->addTranscodeWarning($transcodeProgressData->getWarning());
            $videoStream['transcode_info'] = $transcodeInfo->toJson();
            $VideoStream->save($videoStream);

            GoalousLog::info('transcode status warning notified', [
                'video_streams.id' => $videoStreamId,
                'state' => $progressState->getValue(),
                'message' => $transcodeProgressData->getWarning(),
            ]);
            return $videoStream;
        } else if ($progressState->equals(Enum\Video\VideoTranscodeProgress::COMPLETE())) {
            // if transcode is completed
            $status = Enum\Video\VideoTranscodeStatus::TRANSCODE_COMPLETE;

            // if we missed the "PROGRESSING" notification, our video_stream.status_transcode is QUEUED
            // but this has no problem when we receive "COMPLETE" notification
            if (!in_array($currentVideoStreamStatus->getValue(), [
                Enum\Video\VideoTranscodeStatus::TRANSCODING,
                Enum\Video\VideoTranscodeStatus::QUEUED,
            ])) {
                throw new RuntimeException("video_streams.id({$videoStreamId}) is not transcoding");
            }
            $videoStream['status_transcode']     = Enum\Video\VideoTranscodeStatus::TRANSCODE_COMPLETE;
            $videoStream['duration']             = $transcodeProgressData->getDuration();
            $videoStream['aspect_ratio']         = $transcodeProgressData->getAspectRatio();
            $videoStream['storage_path']         = $transcodeProgressData->getOutputKeyPrefix();

            $transcodeInfo = $VideoStream->getTranscodeInfo($videoStream);
            $transcodeInfo->setTranscodeJobId($transcodeProgressData->getJobId());
            $videoStream['transcode_info'] = $transcodeInfo->toJson();
            $VideoStream->save($videoStream);
            GoalousLog::info('transcode status changed ', [
                'video_streams.id' => $videoStreamId,
                'state' => $progressState->getValue(),
                'status_value_from' => $currentVideoStreamStatus->getValue(),
                'status_value_to' => $status,
            ]);
            return $videoStream;
        }
        throw new RuntimeException("video_streams.id({$videoStreamId}) is not transcoding");
    }

    public function findVideoStreamIfExists(int $userId, int $teamId, string $hash): array
    {
        /** @var Video $Video */
        $Video = ClassRegistry::init("Video");
        /** @var VideoStream $VideoStream */
        $VideoStream = ClassRegistry::init("VideoStream");

        $video = $Video->getByUserIdAndTeamIdAndHash($userId, $teamId, $hash);
        if(empty($video)) {
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
     * @see https://confluence.goalous.com/display/GOAL/Video+storage+structure
     *
     * @param string $inputS3FileKey
     *
     * @return string
     */
    private function getOutputKeyPrefix(string $inputS3FileKey): string
    {
        // e.g.
        // $this->inputS3FileKey() = uploads/111/222/abcdef1234567890/original
        // return 'streams/111/222/abcdef1234567890/'

        $urlSplits = array_slice(explode('/', trim($inputS3FileKey, '/')), 1, -1);
        return sprintf('streams/%s/', implode($urlSplits, '/'));
    }

    public function uploadNewVideoStream(array $uploadFile, array $user, int $teamId): array
    {
        /** @var Video $Video */
        $Video = ClassRegistry::init("Video");
        /** @var VideoStream $VideoStream */
        $VideoStream = ClassRegistry::init("VideoStream");

        $userId = $user['id'];

        $filePath = $uploadFile['tmp_name'];
        $fileName = $uploadFile['name'];

        $transcodeOutputVersion = TeamStatus::getCurrentTeam()->getTranscodeOutputVersion();

        $hash = VideoFileHasher::hashFile(new \SplFileInfo($filePath));

        $videoStreamIfExists = $this->findVideoStreamIfExists($userId, $teamId, $hash);
        if (!empty($videoStreamIfExists)) {
            GoalousLog::info('uploaded same hash video exists', [
                'user_id' => $userId,
                'team_id' => $teamId,
                'hash'    => $hash,
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
            'status_transcode' => Enum\Video\VideoTranscodeStatus::UPLOADING,
            'output_version'   => $transcodeOutputVersion->getValue(),
            'transcode_info'   => TranscodeInfo::createNew()->toJson(),
        ]);
        $videoStream = $VideoStream->save();
        $videoStream = reset($videoStream);

        // video upload
        $request = new VideoUploadRequestOnPost(new SplFileInfo($filePath), $user, $teamId, $video, $videoStream);
        $request->setFileHash($hash);
        $result = VideoStorageClient::upload($request);
        if (!$result->isSucceed()) {
            GoalousLog::error('video uploading to storage failed', [
                'code'             => $result->getErrorCode(),
                'message'          => $result->getErrorMessage(),
                'videos.id'        => $video['id'],
                'video_streams.id' => $videoStream['id'],
            ]);
            $Video->softDelete($video['id'], false);
            $errorMessage = "failed upload video:" . $result->getErrorMessage();
            $this->deleteVideoStreamWithError($videoStream, $errorMessage);
            throw new RuntimeException($errorMessage);
        }

        // Upload complete
        $resourcePath = $result->getResourcePath();
        $video['resource_path'] = $resourcePath;
        GoalousLog::info('video uploaded', [
            'resource_path'    => $resourcePath,
            'user_id'          => $userId,
            'team_id'          => $teamId,
            'videos.id'        => $video['id'],
            'video_streams.id' => $videoStream['id'],
        ]);
        $Video->save($video);

        $videoStream['status_transcode'] = Enum\Video\VideoTranscodeStatus::UPLOAD_COMPLETE;
        $VideoStream->save($videoStream);

        // create transcode job
        $transcodeRequest = new AwsVideoTranscodeJobRequest(
            $this->getOutputKeyPrefix($resourcePath),
            AWS_ELASTIC_TRANSCODER_PIPELINE_ID,
            $transcodeOutputVersion
        );
        $inputVideo = new AwsEtsTranscodeInput($resourcePath);
        $inputVideo->setTimeSpan(60, 0);// 00:00 to 01:00
        $transcodeRequest->addInputVideo($inputVideo);
        $transcodeRequest->setUserMetaData([
            'videos.id'        => $video['id'],
            'video_streams.id' => $videoStream['id'],
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
                'video_streams.id' => $videoStream['id'],
            ]);
            $Video->softDelete($video['id'], false);
            $errorMessage = "creating video transcode job failed:" . $createJobResult->getErrorMessage();
            $this->deleteVideoStreamWithError($videoStream, $errorMessage);
            throw new RuntimeException($errorMessage);
        }

        // Queued complete
        $transcodeInfo = $VideoStream->getTranscodeInfo($videoStream);
        $transcodeInfo->setTranscoderType($transcodeRequest->getTranscoder());
        $transcodeInfo->setTranscodeJobId($createJobResult->getJobId());
        $videoStream['transcode_info'] = $transcodeInfo->toJson();

        $videoStream['status_transcode'] = Enum\Video\VideoTranscodeStatus::QUEUED;
        $VideoStream->save($videoStream);

        GoalousLog::info('video transcode queued', [
            'job_id'           => $createJobResult->getJobId(),
            'teams.id'         => $teamId,
            'videos.id'        => $video['id'],
            'video_streams.id' => $videoStream['id'],
        ]);

        return $videoStream;
    }

    private function deleteVideoStreamWithError(array $videoStream, string $errorMessage)
    {
        /** @var VideoStream $VideoStream */
        $VideoStream = ClassRegistry::init("VideoStream");

        $transcodeInfo = $VideoStream->getTranscodeInfo($videoStream);
        $transcodeInfo->addTranscodeError($errorMessage);
        $videoStream['status_transcode'] = Enum\Video\VideoTranscodeStatus::ERROR;
        $videoStream['transcode_info'] = $transcodeInfo->toJson();
        $VideoStream->save($videoStream);
        $VideoStream->softDelete($videoStream['id'], false);
    }
}
