<?php
App::uses('ApiController', 'Controller/Api');
App::uses('VideoStream', 'Model');
App::uses('Video', 'Model');
App::uses('TeamMember', 'Model');
App::uses('TranscodeOutputVersionDefinition', 'Model/Video/Transcode');
App::uses('VideoStorageClient', 'Model/Video');
App::uses('TeamStatus', 'Lib/Status');

use Goalous\Model\Enum as Enum;

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

    function get_source($id)
    {
        $type = $this->request->query('type');
        if (empty($type)) {
            return $this->_getResponseBadFail('bad request');
        }
        // retrieve video data
        $videoStream = $this->VideoStream->findById($id);
        if (empty($videoStream)) {
            return $this->_getResponseNotFound();
        }
        $videoStream = reset($videoStream);
        $video = $this->Video->findById($videoStream['video_id']);
        if (empty($video)) {
            return $this->_getResponseNotFound();
        }
        $video = reset($video);

        // check target video can playable by user belongs teams
        $videosTeamId = $video['team_id'];
        $userId = $this->Auth->user('id');
        $teamsUserBelongs = $this->TeamMember->getAllTeam($userId);
        $teamIdsUserBelongs = Hash::extract($teamsUserBelongs, '{n}.TeamMember.team_id');
        if (!in_array($videosTeamId, $teamIdsUserBelongs)) {
            return $this->_getResponseNotFound();
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
