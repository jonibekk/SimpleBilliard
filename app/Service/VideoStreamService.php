<?php
App::import('Service', 'AppService');
App::uses('VideoUploadRequestOnPost', 'Model/Video/Requests');
App::uses('VideoStorageClient', 'Model/Video');

App::uses('Video', 'Model');
App::uses('VideoStream', 'Model');

use Goalous\Model\Enum as Enum;

/**
 * Class VideoStreamService
 */
class VideoStreamService extends AppService
{
    public function updateFromTranscodeProgressData(array $videoStream, TranscodeProgressData $transcodeProgressData): bool
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
            $videoStream['transcode_info'] = "[]";// TODO: add ETS JobId at least
            $VideoStream->save($videoStream);
            CakeLog::info(sprintf('transcode status changed: %s', AppUtil::jsonOneLine([
                'video_streams.id' => $videoStreamId,
                'state' => $progressState->getValue(),
                'status_value_from' => $currentVideoStreamStatus->getValue(),
                'status_value_to' => $status,
            ])));
            return true;
        } else if ($progressState->equals(Enum\Video\VideoTranscodeProgress::ERROR())) {
            // if transcode is error
            $status = Enum\Video\VideoTranscodeStatus::ERROR;
            if (!$currentVideoStreamStatus->equals(Enum\Video\VideoTranscodeStatus::TRANSCODING())) {
                throw new RuntimeException("video_streams.id({$videoStreamId}) is not transcoding");
            }
            $videoStream['status_transcode'] = $status;
            $VideoStream->save($videoStream);
            CakeLog::info(sprintf('transcode status changed: %s', AppUtil::jsonOneLine([
                'video_streams.id' => $videoStreamId,
                'state' => $progressState->getValue(),
                'status_value_from' => $currentVideoStreamStatus->getValue(),
                'status_value_to' => $status,
            ])));
            return true;
        } else if ($progressState->equals(Enum\Video\VideoTranscodeProgress::COMPLETE())) {
            // if transcode is completed
            $status = Enum\Video\VideoTranscodeStatus::TRANSCODE_COMPLETE;
            if (!$currentVideoStreamStatus->equals(Enum\Video\VideoTranscodeStatus::TRANSCODING())) {
                throw new RuntimeException("video_streams.id({$videoStreamId}) is not transcoding");
            }
            $videoStream['status_transcode']     = Enum\Video\VideoTranscodeStatus::TRANSCODE_COMPLETE;
            $videoStream['duration']             = $transcodeProgressData->getDuration();
            $videoStream['aspect_ratio']         = $transcodeProgressData->getAspectRatio();
            $videoStream['master_playlist_path'] = $transcodeProgressData->getPlaylistPath();
            $VideoStream->save($videoStream);
            CakeLog::info(sprintf('transcode status changed: %s', AppUtil::jsonOneLine([
                'video_streams.id' => $videoStreamId,
                'state' => $progressState->getValue(),
                'status_value_from' => $currentVideoStreamStatus->getValue(),
                'status_value_to' => $status,
            ])));

            /** @var PostDraft $PostDraft */
            $PostDraft = ClassRegistry::init('PostDraft');
            $postDraft = $PostDraft->getFirstByResourceTypeAndResourceId(Enum\Post\PostResourceType::VIDEO_STREAM(), $videoStreamId);
            // TODO: ここ、複数紐付いている下書きがあった場合、一つしかpostされない可能性がある
            if (!empty($postDraft)) {
                /** @var Post $Post */
                $Post = ClassRegistry::init('Post');
                $this->current_team_id = $postDraft['team_id'];
                $Post->addNormalFromPostDraft($postDraft);
            }
            return true;
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

    public function uploadNewVideoStream(array $uploadFile, array $user, int $teamId): array
    {
        /** @var Video $Video */
        $Video = ClassRegistry::init("Video");
        /** @var VideoStream $VideoStream */
        $VideoStream = ClassRegistry::init("VideoStream");

        $userId = $user['id'];

        CakeLog::info(sprintf(__METHOD__."%s", AppUtil::jsonOneLine([
            'team_id' => $teamId,
        ])));

        $filePath = $uploadFile['tmp_name'];
        $fileName = $uploadFile['name'];

        $hash = hash_file('sha256', $filePath);

        $videoStreamIfExists = $this->findVideoStreamIfExists($userId, $teamId, $hash);
        if (!empty($videoStreamIfExists)) {
            CakeLog::info(sprintf('uploaded video exists %s', AppUtil::jsonOneLine([
                'user_id' => $userId,
                'team_id' => $teamId,
                'hash'    => $hash,
                'video_streams.id' => $videoStreamIfExists['id'],
            ])));
            return $videoStreamIfExists;
        }
        CakeLog::info(sprintf('uploaded video NOT exists %s', AppUtil::jsonOneLine([
            'user_id' => $userId,
            'team_id' => $teamId,
            'hash'    => $hash,
        ])));

        // create video, video_stream
        // need to be create for Storage Meta data to save ids
        $Video->create([
            'user_id' => $userId,
            'team_id' => $teamId,
            'duration' => null,// TODO: cant estimate now (need ffprove or something)
            'width' => null,// TODO: cant estimate now
            'height' => null,// TODO: cant estimate now
            'hash' => hash_file('sha256', $filePath),//$request->getFileHash(),// TODO: move to some function/class
            'file_size' => filesize($filePath),
            'file_name' => $fileName,// TODO: passing wrong name(pass the user's file name)
            'resource_path' => null,
        ]);
        $video = $Video->save();
        $video = reset($video);
        $VideoStream->create([
            'video_id' => $video['id'],
            'duration' => 0,// TODO: make null can be taken
            'aspect_ratio' => 0,// TODO: make null can be taken
            'master_playlist_path' => '',// TODO: make null can be taken
            'status_transcode' => Enum\Video\VideoTranscodeStatus::UPLOADING,
            'transcode_info' => json_encode([]),
        ]);
        $videoStream = $VideoStream->save();
        $videoStream = reset($videoStream);

        $request = new VideoUploadRequestOnPost(new SplFileInfo($filePath), $user, $teamId, $video, $videoStream);
        $result = VideoStorageClient::upload($request);

        if (!$result->isSucceed()) {
            CakeLog::error(sprintf(__METHOD__."::failed", AppUtil::jsonOneLine([
                'code' => $result->getErrorCode(),
                'message' => $result->getErrorMessage(),
            ])));
            $videoStream['status_transcode'] = Enum\Video\VideoTranscodeStatus::ERROR;
            $VideoStream->save($videoStream);
            throw new RuntimeException("failed upload video:" . $result->getErrorMessage());
        }
        // Succeeded process
        $video['resource_path'] = $result->getResourcePath();
        $Video->save($video);

        // Upload complete and queued is same timing on AWS Elastic Transcoder (using S3 event + Lambda)
        $videoStream['status_transcode'] = Enum\Video\VideoTranscodeStatus::UPLOAD_COMPLETE;
        $videoStream['status_transcode'] = Enum\Video\VideoTranscodeStatus::QUEUED;
        $VideoStream->save($videoStream);

        CakeLog::info(sprintf(__METHOD__."::succeed", AppUtil::jsonOneLine([
            'team_id' => $teamId,
        ])));
        return $videoStream;
    }
}
