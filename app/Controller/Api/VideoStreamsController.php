<?php
App::uses('BaseApiController', 'Controller/Api');
App::uses('VideoStream', 'Model');
App::uses('Video', 'Model');
App::uses('TeamMember', 'Model');
App::uses('TranscodeOutputVersionDefinition', 'Model/Video/Transcode');
App::uses('VideoStorageClient', 'Model/Video');
App::uses('TeamStatus', 'Lib/Status');

use Goalous\Enum as Enum;
use Goalous\Exception as GlException;

/**
 * Class VideoStreamController
 */
class VideoStreamsController extends BaseApiController
{
    public $components = [
    ];

    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    /**
     * @skipAuthentication
     *
     * @param $id
     * @return $this|BaseApiResponse|void
     */
    function get_source($id)
    {
        $type = $this->request->query('type');
        if (empty($type)) {
            return ErrorResponse::badRequest()->getResponse();
        }
        // retrieve video data
        $videoStream = $this->VideoStream->getById($id);
        if (empty($videoStream)) {
            return ErrorResponse::notFound()->getResponse();
        }

        // TODO: Unknown user because Videogular couldn't add header to video stream play list source.
//        if (false === $this->isPlayableVideoStreamByUser($videoStream, $this->getUserId())) {
//            GoalousLog::info('isPlayableVideoStreamByUser', [$videoStream]);
//            return ErrorResponse::notFound()->getResponse();
//        }

        // If user's browser is not supporting cross-origin redirecting of manifest
        // redirecting to manifest API that showing manifest string in same origin
        //
        // @see
        //  https://developer.mozilla.org/ja/docs/XMLHttpRequest/responseURL
        //  https://github.com/videojs/videojs-contrib-hls/pull/912#discussion_r164196518
        //  https://github.com/IsaoCorp/goalous/pull/6640
        if ($type === Enum\Model\Video\VideoSourceType::PLAYLIST_M3U8_HLS
            && !$this->isBrowserSupportManifestRedirects()) {
            $this->redirect(sprintf('/api/v1/video_streams/%d/manifest?path=playlist.m3u8', $id));
        }

        $videoStoragePath = $videoStream['storage_path'];
        $outputVersion = new Enum\Model\Video\TranscodeOutputVersion(intval($videoStream['output_version']));
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
        return ErrorResponse::badRequest()->getResponse();
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
        /** @var VideoStream $VideoStream */
        $VideoStream = ClassRegistry::init('VideoStream');
        /** @var Video $Video */
        $Video = ClassRegistry::init('Video');
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init('TeamMember');

        $video = $Video->getById($videoStream['video_id']);
        if (empty($video)) {
            return false;
        }
        // check target video can playable by user belongs teams
        $videosTeamId = $video['team_id'];
        $teamsUserBelongs = $TeamMember->getAllTeam($userId);
        $teamIdsUserBelongs = Hash::extract($teamsUserBelongs, '{n}.TeamMember.team_id');
        return in_array($videosTeamId, $teamIdsUserBelongs);
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


}
