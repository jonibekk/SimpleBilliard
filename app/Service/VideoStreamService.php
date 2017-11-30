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
