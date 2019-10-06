<?php
App::uses('GoalousTestCase', 'Test');
App::uses('Post', 'Model');
App::uses('PostDraft', 'Model');
App::uses('PostResource', 'Model');
App::uses('TranscodeNotificationAwsSns', 'Model/Video/Stream');
App::import('Service', 'VideoStreamService');
App::uses('TestVideoTrait', 'Test/Trait');
App::uses('TestPostDraftTrait', 'Test/Trait');

use Goalous\Enum as Enum;

class VideoStreamServiceTest extends GoalousTestCase
{
    use TestVideoTrait, TestPostDraftTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.video',
        'app.video_stream',
        'app.post',
        'app.post_draft',
        'app.post_resource',
        'app.circle',
        'app.post_share_circle',
        'app.circle_member',
        'app.post_file',
        'app.video_transcode_log',
    ];

    /**
     * @var VideoStreamService
     */
    private $VideoStreamService;

    /**
     * @var Post
     */
    private $Post;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->VideoStreamService = ClassRegistry::init('VideoStreamService');
        $this->VideoStream = ClassRegistry::init('VideoStream');
        $this->Video = ClassRegistry::init('Video');
        $this->Post = ClassRegistry::init('Post');
        $this->PostDraft = ClassRegistry::init('PostDraft');
        $this->PostResource = ClassRegistry::init('PostResource');
    }

    function test_findVideoStreamIfExists()
    {
        $userId = 1;
        $teamId = 1;
        $hashExists    = hash('sha256', 'a');
        $hashNotExists = hash('sha256', 'b');
        $this->createVideoSet($userId, $teamId, $hashExists, Enum\Model\Video\VideoTranscodeStatus::TRANSCODE_COMPLETE());
        $this->assertTrue(!empty($this->Video->getByUserIdAndTeamIdAndHash($userId, $teamId, $hashExists)));
        $this->assertTrue(empty($this->Video->getByUserIdAndTeamIdAndHash($userId, $teamId, $hashNotExists)));
    }

    function test_updateFromTranscodeProgressData_withNoPostDraft()
    {
        $userId = 1;
        $teamId = 1;
        $videoStreamId = 1;
        $videoId = 1;
        $hash = hash('sha256', 'a');
        list($video, $videoStream) = $this->createVideoSet($userId, $teamId, $hash, Enum\Model\Video\VideoTranscodeStatus::QUEUED());

        // PROGRESSING notification from AWS SNS
        $transcodeNotificationAwsSns = TranscodeNotificationAwsSns::parseJsonString(
            $this->createAwsSnsTranscodeNotificationString(
                $this->createAwsSnsTranscodeNotificationJsonProgressing($userId, $teamId, $hash, $videoStreamId, $videoId)
            )
        );
        $this->VideoStreamService->updateFromTranscodeProgressData($videoStream, $transcodeNotificationAwsSns);
        $videoStreamProgressing = $this->VideoStream->getById($videoStream['id']);
        $this->assertEquals(null, $videoStreamProgressing['duration']);
        $this->assertEquals(null, $videoStreamProgressing['aspect_ratio']);
        $this->assertEquals(null, $videoStreamProgressing['storage_path']);
        $this->assertEquals(Enum\Model\Video\VideoTranscodeStatus::TRANSCODING, $videoStreamProgressing['transcode_status']);

        // COMPLETED notification from AWS SNS
        $transcodeNotificationAwsSns = TranscodeNotificationAwsSns::parseJsonString(
            $this->createAwsSnsTranscodeNotificationString(
                $this->createAwsSnsTranscodeNotificationJsonCompleted($userId, $teamId, $hash, $videoStreamId, $videoId)
            )
        );
        $this->VideoStreamService->updateFromTranscodeProgressData($videoStreamProgressing, $transcodeNotificationAwsSns);
        $videoStreamProgressing = $this->VideoStream->getById($videoStream['id']);
        $this->assertTrue(ctype_digit($videoStreamProgressing['duration']));
        $this->assertTrue(is_numeric($videoStreamProgressing['aspect_ratio']));
        $this->assertTrue(is_string($videoStreamProgressing['storage_path']));
        $this->assertEquals(Enum\Model\Video\VideoTranscodeStatus::TRANSCODE_COMPLETE, $videoStreamProgressing['transcode_status']);
    }

    function test_isVideoStreamBelongsToTeam()
    {
        list($video1, $videoStream1) = $this->createVideoSet($userId = 1, $teamId = 1, 'A', Enum\Model\Video\VideoTranscodeStatus::TRANSCODE_COMPLETE());
        list($video2, $videoStream2) = $this->createVideoSet($userId = 2, $teamId = 2, 'B', Enum\Model\Video\VideoTranscodeStatus::TRANSCODE_COMPLETE());
        $this->assertTrue($this->VideoStreamService->isVideoStreamBelongsToTeam($videoStream1['id'], 1), 'video_steam1 belong to team1');
        $this->assertFalse($this->VideoStreamService->isVideoStreamBelongsToTeam($videoStream1['id'], 2), 'video_steam1 not belong to team2');
        $this->assertFalse($this->VideoStreamService->isVideoStreamBelongsToTeam($videoStream2['id'], 1), 'video_steam2 not belong to team1');
        $this->assertTrue($this->VideoStreamService->isVideoStreamBelongsToTeam($videoStream2['id'], 2), 'video_steam2 belong to team2');
        $this->assertFalse($this->VideoStreamService->isVideoStreamBelongsToTeam($videoStream1['id'], 99999), 'video_steam1 not belong to unexisting team');
        $this->assertFalse($this->VideoStreamService->isVideoStreamBelongsToTeam(99999, 1));
        $this->assertFalse($this->VideoStreamService->isVideoStreamBelongsToTeam(99999, 99999));
    }

    function test_updateFromTranscodeProgressData_withPostDraft()
    {
        $userId = 1;
        $teamId = 1;
        $videoStreamId = 1;
        $videoId = 1;
        $hash = hash('sha256', 'a');
        $bodyText = 'this is post message';
        list($video, $videoStream) = $this->createVideoSet($userId, $teamId, $hash, Enum\Model\Video\VideoTranscodeStatus::QUEUED());
        list($postDraft, $postResource) = $this->createPostDraftWithVideoStreamResource($userId, $teamId, $videoStream, $bodyText);
        //var_dump($postDraft, $postResource);

        // PROGRESSING notification from AWS SNS
        $transcodeNotificationAwsSns = TranscodeNotificationAwsSns::parseJsonString(
            $this->createAwsSnsTranscodeNotificationString(
                $this->createAwsSnsTranscodeNotificationJsonProgressing($userId, $teamId, $hash, $videoStreamId, $videoId)
            )
        );
        $this->VideoStreamService->updateFromTranscodeProgressData($videoStream, $transcodeNotificationAwsSns);
        $videoStreamProgressing = $this->VideoStream->getById($videoStream['id']);
        $this->assertNull($videoStreamProgressing['duration']);
        $this->assertNull($videoStreamProgressing['aspect_ratio']);
        $this->assertNull($videoStreamProgressing['storage_path']);
        $this->assertEquals(Enum\Model\Video\VideoTranscodeStatus::TRANSCODING, $videoStreamProgressing['transcode_status']);

        // COMPLETED notification from AWS SNS
        $transcodeNotificationAwsSns = TranscodeNotificationAwsSns::parseJsonString(
            $this->createAwsSnsTranscodeNotificationString(
                $this->createAwsSnsTranscodeNotificationJsonCompleted($userId, $teamId, $hash, $videoStreamId, $videoId)
            )
        );
        $this->VideoStreamService->updateFromTranscodeProgressData($videoStreamProgressing, $transcodeNotificationAwsSns);
        $videoStreamProgressing = $this->VideoStream->getById($videoStream['id']);
        $this->assertTrue(ctype_digit($videoStreamProgressing['duration']));
        $this->assertTrue(is_numeric($videoStreamProgressing['aspect_ratio']));
        $this->assertTrue(is_string($videoStreamProgressing['storage_path']));
        $this->assertEquals(Enum\Model\Video\VideoTranscodeStatus::TRANSCODE_COMPLETE, $videoStreamProgressing['transcode_status']);

        // TODO: this process is no more proceed in VideoStreamService, move to another service and write test
        // check draft post, post resource
        //$postDraftUpdated = $this->PostDraft->getById($postDraft['id']);
        //$postResourceUpdated = $this->PostResource->getById($postResource['id']);
        //$this->assertFalse($postDraftUpdated);
        //$this->assertTrue(is_numeric($postResourceUpdated['post_id']));

        //$post = $this->Post->getById($postResourceUpdated['post_id']);
        //$this->assertEquals($userId, intval($post['user_id']));
        //$this->assertEquals($teamId, intval($post['team_id']));
        //$this->assertEquals($bodyText, $post['body']);
    }

    function test_uploadNewVideoStream()
    {
        // TODO: implement here
        $this->assertTrue(true);
    }

    private function createAwsSnsTranscodeNotificationString(array $messageArray): string
    {
        return json_encode([
            "Type" =>  "Notification",
            "MessageId" =>  "3241eb3e-0567-55f0-b6ce-b9b81bc9f70a",
            "TopicArn" =>  "arn:aws:sns:ap-northeast-1:585065716000:goalous-test-transcode",
            "Subject" =>  "Subject message",
            "Message" =>  json_encode($messageArray),
            "Timestamp" =>  "2017-11-15T12:00:10.000Z",
            "SignatureVersion" =>  "1",
            "Signature" =>  "hashed-signature",
            "SigningCertURL" =>  "https://sns.ap-northeast-1.amazonaws.com/SimpleNotificationService-hash.pem",
            "UnsubscribeURL" =>  "https://sns.ap-northeast-1.amazonaws.com/?Action=Unsubscribe"
        ]);
    }


    private function createAwsSnsTranscodeNotificationMessageInput(int $userId, int $teamId, string $hash): array
    {
        return [
            'key' => "uploads/{$userId}/{$teamId}/{$hash}/video.mp4",
            'frameRate' => 'auto',
            'resolution' => 'auto',
            'aspectRatio' => 'auto',
            'interlaced' => 'auto',
            'container' => 'auto',
        ];
    }

    private function createAwsSnsTranscodeNotificationMessageMetaData(int $videoStreamId, int $videoId): array
    {
        return [
            'video_streams.id' => $videoStreamId,
            'video.id' => $videoId,
        ];
    }

    private function createAwsSnsTranscodeNotificationJsonProgressing(
        int $userId,
        int $teamId,
        string $hash,
        int $videoStreamId,
        int $videoId
    ): array
    {
        return [
            'state' => 'PROGRESSING',
            'version' => '2012-09-25',
            'jobId' => '1510743146111-jobid',
            'pipelineId' => '1510729662392-pipeid',
            'input' => $this->createAwsSnsTranscodeNotificationMessageInput($userId, $teamId, $hash),
            'inputCount' => 1,
            'outputKeyPrefix' => "streams/{$userId}/{$teamId}/{$hash}/",
            'outputs' => [
                [
                    'id' => '1',
                    'presetId' => '1508231140786-8hm2xg',
                    'key' => '256k/video',
                    'thumbnailPattern' => 'thumbs-{count}',
                    'rotate' => 'auto',
                    'segmentDuration' => 10,
                    'status' => 'Progressing',
                ],
            ],
            'playlists' => [
                [
                    'name' => 'playlist',
                    'format' => 'HLSv3',
                    'outputKeys' => [ '256k/video', ],
                    'status' => 'Progressing',
                ],
            ],
            'userMetadata' => $this->createAwsSnsTranscodeNotificationMessageMetaData($videoStreamId, $videoId),
        ];
    }

    private function createAwsSnsTranscodeNotificationJsonCompleted(
        int $userId,
        int $teamId,
        string $hash,
        int $videoStreamId,
        int $videoId
    ): array
    {
        return [
            'state' => 'COMPLETED',
            'version' => '2012-09-25',
            'jobId' => '1510743146111-jobid',
            'pipelineId' => '1510729662392-pipeid',
            'input' => $this->createAwsSnsTranscodeNotificationMessageInput($userId, $teamId, $hash),
            'inputCount' => 1,
            'outputKeyPrefix' => "streams/{$userId}/{$teamId}/{$hash}/",
            'outputs' => [
                [
                    'id' => '1',
                    'presetId' => '1508231140786-8hm2xg',
                    'key' => '256k/video',
                    'thumbnailPattern' => 'thumbs-{count}',
                    'rotate' => 'auto',
                    'segmentDuration' => 10,
                    'status' => 'Complete',
                    'duration' => 60,
                    'width' => 320,
                    'height' => 180,
                ],
            ],
            'playlists' => [
                [
                    'name' => 'playlist',
                    'format' => 'HLSv3',
                    'outputKeys' => [ '256k/video', ],
                    'status' => 'Complete',
                ],
            ],
            'userMetadata' => $this->createAwsSnsTranscodeNotificationMessageMetaData($videoStreamId, $videoId),
        ];
    }

    private function createAwsSnsTranscodeNotificationJsonError(
        int $userId,
        int $teamId,
        string $hash,
        int $videoStreamId,
        int $videoId
    ): array
    {
        return [
            'state' => 'ERROR',
            'version' => '2012-09-25',
            'jobId' => '1510743146111-jobid',
            'pipelineId' => '1510729662392-pipeid',
            'input' => $this->createAwsSnsTranscodeNotificationMessageInput($userId, $teamId, $hash),
            'inputCount' => 1,
            'outputKeyPrefix' => "streams/{$userId}/{$teamId}/{$hash}/",
            'outputs' => [
                [
                    'id' => '1',
                    'presetId' => '1508231140786-8hm2xg',
                    'key' => '256k/video',
                    'thumbnailPattern' => 'thumbs-{count}',
                    'rotate' => 'auto',
                    'segmentDuration' => 10,
                    'status' => 'Error',
                    'statusDetail' => '3002 2b9aec95-2960-4436-8419-c0a3f07a6200: The specified object could not be saved in the specified bucket because an object by that name already exists: bucket=goalous-dev-videos, key=streams/1/1/zxcvb/256k/video00000.ts.',
                    'errorCode' => 3002,
                ],
            ],
            'playlists' => [
                [
                    'name' => 'playlist',
                    'format' => 'HLSv3',
                    'outputKeys' =>
                        ['256k/video',],
                    'status' => 'Error',
                    'statusDetail' => '1001 bc05319c-880e-49ba-96d7-031231a9a1fa: Amazon Elastic Transcoder could not generate the playlist because it encountered an error with one or more of the playlists dependencies.',
                ],
            ],
            'userMetadata' => $this->createAwsSnsTranscodeNotificationMessageMetaData($videoStreamId, $videoId),
        ];
    }
}
