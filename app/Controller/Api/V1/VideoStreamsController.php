<?php
App::uses('ApiController', 'Controller/Api');
App::uses('VideoStream', 'Model');
App::uses('Video', 'Model');
App::uses('TeamMember', 'Model');
App::uses('TranscodeOutputVersionDefinition', 'Model/Video/Transcode');
App::uses('VideoStorageClient', 'Model/Video');
App::uses('TeamStatus', 'Lib/Status');

use Goalous\Enum as Enum;

/**
 * @property VideoStream $VideoStream
 * @property Video $Video
 * @property TeamMember $TeamMember
 */
class VideoStreamsController extends ApiController
{
    public $uses = [
        'VideoStream',
        'Video',
        'TeamMember',
    ];

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
    private function isBrowserSupportManifestRedirects(): bool
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
     * Return manifest body by string
     *
     * Get manifest file from storage and
     * replace video resource files(.key, .m3u8, .ts) path string to full-path
     *
     * @param array  $videoStream
     * @param string $manifestFileKey
     * @param string $relativeDirectory
     *
     * @return string
     */
    private function getResponseBodyOfManifest(array $videoStream, string $manifestFileKey, string $relativeDirectory): string
    {
        $getObjectResult = VideoStorageClient::getObjectFromTranscodedBucket($manifestFileKey);
        $playlistBody = $getObjectResult['Body'];

        // $baseUrlVideoStream = https://s3.aws.com/<bucket>/<storage_path>(/<directory>)?
        $baseUrlVideoStream = sprintf('%s/%s/%s/%s'
            , S3_BASE_URL, AWS_S3_BUCKET_VIDEO_TRANSCODED, rtrim($videoStream['storage_path'], '/'), $relativeDirectory);

        // replacing .m3u8 file to api playlist url
        // 'ts_500k/video.m3u8' to '/api/v1/video_streams/39/manifest?path=ts_500k/video.m3u8'
        $playlistBody = preg_replace_callback(
            '/[^#].+\.m3u8$/m',
            function ($matches) use ($videoStream) {
                return PHP_EOL.sprintf('/api/v1/video_streams/%d/manifest?path=%s'
                        , $videoStream['id']
                        , trim($matches[0]));
            }, $playlistBody);

        // replacing .ts files to fullpath
        // 'ts_500k/video.m3u8' to '/api/v1/video_streams/39/manifest?path=ts_500k/video.m3u8'
        // 'video00000.ts' to 'https://s3-ap-northeast-1.amazonaws.com/bucket/streams/.../ts_500k/video00000.ts'
        $playlistBody = preg_replace_callback(
            '/[^#].+\.ts$/m',
            function ($matches) use ($videoStream, $baseUrlVideoStream) {
                return PHP_EOL.sprintf('%s/%s'
                        , $baseUrlVideoStream
                        , trim($matches[0]));
            }, $playlistBody);

        // replacing key file
        // #EXT-X-KEY:METHOD=AES-128,URI="video.key",IV=0x5635a390c4392ec4b26c53c72aaa2fc3
        // to
        // #EXT-X-KEY:METHOD=AES-128,URI="https://s3-ap-northeast-1.amazonaws.com/bucket/streams/.../ts_500k/video.key",IV=0x5635a390c4392ec4b26c53c72aaa2fc3
        $playlistBody = preg_replace_callback(
            '/^(#EXT-X-KEY.+)\"(\w+.key)\"/m',
            function ($matches) use ($videoStream, $baseUrlVideoStream) {
                return PHP_EOL.sprintf('%s"%s/%s"'
                        , $matches[1]
                        , $baseUrlVideoStream
                        , trim($matches[2]));
            }, $playlistBody);
        return $playlistBody;
    }

    /**
     * Return directory path from passed value of manifest path
     *
     * @param string $path e.g. 'ts_500k/playlist.m3u8'
     *
     * @return string e.g. 'ts_500k'
     */
    private function getRelativeDirectoryFromPath(string $path): string
    {
        $explodes = explode('/', $path);
        array_pop($explodes);
        return implode('/', $explodes);
    }

    /**
     * Return response by
     * Content-Type: application/x-mpegURL
     * with manifest text body.
     *
     * @param $id
     *
     * @return CakeResponse|null
     */
    function get_manifest($id)
    {
        $path = $this->request->query('path');
        $path = trim($path);
        if (empty($path)) {
            return $this->_getResponseBadFail('bad request');
        }
        $videoStream = $this->VideoStream->getById($id);
        if (empty($videoStream)) {
            return $this->_getResponseNotFound();
        }

        $relativeDirectory = $this->getRelativeDirectoryFromPath($path);
        $key = $videoStream['storage_path'] . $path;
        try {
            $body = $this->getResponseBodyOfManifest($videoStream, $key, $relativeDirectory);
        } catch (Exception $e) {
            GoalousLog::info('Failed to get video manifest from storage', [
                'message' => $e->getMessage(),
            ]);
            return $this->_getResponseBadFail('bad request');
        }

        // below both type() is need to change Content-Type header
        $this->response->type(['m3u8' => 'application/x-mpegURL']);
        $this->response->type('m3u8'); // Do not delete this is also need

        $this->response->body($body);
        $this->response->statusCode(200);
        return $this->response;
    }

    /**
     * Return true if user have a authority to play target video stream
     *
     * @param array $videoStream single video_stream data array
     * @param       $userId
     *
     * @return bool
     */
    private function isPlayableVideoStreamByUser(array $videoStream, $userId): bool
    {
        $video = $this->Video->getById($videoStream['video_id']);
        if (empty($video)) {
            return false;
        }
        // check target video can playable by user belongs teams
        $videosTeamId = $video['team_id'];
        $teamsUserBelongs = $this->TeamMember->getAllTeam($userId);
        $teamIdsUserBelongs = Hash::extract($teamsUserBelongs, '{n}.TeamMember.team_id');
        return in_array($videosTeamId, $teamIdsUserBelongs);
    }

    /**
     * Redirecting video source which manifest or video file
     *
     * @param $id
     *
     * @return CakeResponse|void
     */
    function get_source($id)
    {
        $type = $this->request->query('type');
        if (empty($type)) {
            return $this->_getResponseBadFail('bad request');
        }
        // retrieve video data
        $videoStream = $this->VideoStream->getById($id);
        if (empty($videoStream)) {
            return $this->_getResponseNotFound();
        }

        if (false === $this->isPlayableVideoStreamByUser($videoStream, $this->Auth->user('id'))) {
            return $this->_getResponseNotFound();
        }

        // If user's browser is not supporting cross-origin redirecting of manifest
        // redirecting to manifest API that showing manifest string in same origin
        //
        // @see
        //  https://developer.mozilla.org/ja/docs/XMLHttpRequest/responseURL
        //  https://github.com/videojs/videojs-contrib-hls/pull/912#discussion_r164196518
        //  https://github.com/IsaoCorp/goalous/pull/6640
        if ($type === Enum\Video\VideoSourceType::PLAYLIST_M3U8_HLS
            && !$this->isBrowserSupportManifestRedirects()) {
            $this->redirect(sprintf('/api/v1/video_streams/%d/manifest?path=playlist.m3u8', $id));
        }

        $videoStoragePath = $videoStream['storage_path'];
        $outputVersion = new Enum\Video\TranscodeOutputVersion(intval($videoStream['output_version']));
        $transcodeOutputVersionDefinition = TranscodeOutputVersionDefinition::getVersion($outputVersion);
        foreach ($transcodeOutputVersionDefinition->getVideoSources($videoStoragePath) as $videoSource) {
            if ($videoSource->getType()->getValue() === $type) {
                $preSignedUrl = VideoStorageClient::createPreSignedUriFromTranscoded(
                    $videoSource->getSource(),
                    GoalousDateTime::now()->addHour(1)
                    );
                $this->redirect($preSignedUrl);
                return;
            }
        }
        return $this->_getResponseBadFail('bad request');
    }
}
