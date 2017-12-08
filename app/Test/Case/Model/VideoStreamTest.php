<?php
App::uses('GoalousTestCase', 'Test');
App::uses('VideoStream', 'Model');
App::uses('Video', 'Model');
App::uses('TestVideoTrait', 'Test/Trait');

use Goalous\Model\Enum as Enum;

/**
 * VideoStreamTest Test Case
 */
class VideoStreamTest extends GoalousTestCase
{
    use TestVideoTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.video',
        'app.video_stream',
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->VideoStream = ClassRegistry::init('VideoStream');
        $this->Video = ClassRegistry::init('Video');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->VideoStream);
        unset($this->Video);
        parent::tearDown();
    }

    function test_getNoProgressInSeconds()
    {
        $userId = 1;
        $teamId = 1;
        $videos = [];
        $videoStreams = [];
        list($videos[], $videoStreams[]) = $this->createVideoSet($userId, $teamId, hash('sha256', '1'), Enum\Video\VideoTranscodeStatus::QUEUED());
        list($videos[], $videoStreams[]) = $this->createVideoSet($userId, $teamId, hash('sha256', '2'), Enum\Video\VideoTranscodeStatus::TRANSCODING());
        list($videos[], $videoStreams[]) = $this->createVideoSet($userId, $teamId, hash('sha256', '3'), Enum\Video\VideoTranscodeStatus::ERROR());
        list($videos[], $videoStreams[]) = $this->createVideoSet($userId, $teamId, hash('sha256', '4'), Enum\Video\VideoTranscodeStatus::TRANSCODE_COMPLETE());
        list($videos[], $videoStreams[]) = $this->createVideoSet($userId, $teamId, hash('sha256', '5'), Enum\Video\VideoTranscodeStatus::UPLOAD_COMPLETE());
        list($videos[], $videoStreams[]) = $this->createVideoSet($userId, $teamId, hash('sha256', '6'), Enum\Video\VideoTranscodeStatus::UPLOADING());

        $this->assertEquals(
            [],
            $this->VideoStream->getNoProgressBeforeTimestamp(GoalousDateTime::now()->subSecond(600)->getTimestamp())
        );

        // status QUEUED(), TRANSCODING() will be fetched
        $fetchedVideoStreams = $this->VideoStream->getNoProgressBeforeTimestamp(GoalousDateTime::now()->addSeconds(600)->getTimestamp());
        $this->assertEquals(4, count($fetchedVideoStreams));

        foreach ($fetchedVideoStreams as $fetchedVideoStream) {
            $this->assertTrue(in_array($fetchedVideoStream['status_transcode'], [
                Enum\Video\VideoTranscodeStatus::QUEUED,
                Enum\Video\VideoTranscodeStatus::TRANSCODING,
                Enum\Video\VideoTranscodeStatus::UPLOAD_COMPLETE,
                Enum\Video\VideoTranscodeStatus::UPLOADING,
            ]));
        }
    }
}
