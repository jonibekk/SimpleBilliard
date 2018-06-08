<?php
App::uses('GoalousTestCase', 'Test');
App::uses('VideoTranscodeLog', 'Model');

use Goalous\Enum as Enum;

/**
 * VideoTranscodeLogTest Test Case
 */
class VideoTranscodeLogTest extends GoalousTestCase
{
    /**
     * @var VideoTranscodeLog
     */
    private $VideoTranscodeLog;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.video_transcode_log',
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->VideoTranscodeLog = ClassRegistry::init('VideoTranscodeLog');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->VideoTranscodeLog);
        parent::tearDown();
    }

    function test_add()
    {
        $videoStreamId_1 = 1;
        $videoStreamId_2 = 2;
        $this->VideoTranscodeLog->add($videoStreamId_1, Enum\Model\Video\VideoTranscodeLogType::JOB_REGISTERED(), [
            'a' => 1,
        ]);
        $this->VideoTranscodeLog->add($videoStreamId_1, Enum\Model\Video\VideoTranscodeLogType::ERROR(), [
            'a' => '2',
        ]);
        $this->VideoTranscodeLog->add($videoStreamId_2, Enum\Model\Video\VideoTranscodeLogType::WARNING(), [
        ]);

        $logVideoStreamId_1 = $this->VideoTranscodeLog->find('all', [
            'conditions' => [
                'video_stream_id' => $videoStreamId_1,
            ]
        ]);
        $this->assertSame(2, count($logVideoStreamId_1));
        $this->assertSame([
            'message' => Enum\Model\Video\VideoTranscodeLogType::JOB_REGISTERED,
            'values'  => [
                'a' => 1,
            ],
        ], json_decode($logVideoStreamId_1[0]['VideoTranscodeLog']['log'], true));
        $this->assertSame([
            'message' => Enum\Model\Video\VideoTranscodeLogType::ERROR,
            'values'  => [
                'a' => '2',
            ],
        ], json_decode($logVideoStreamId_1[1]['VideoTranscodeLog']['log'], true));

        $logVideoStreamId_2 = $this->VideoTranscodeLog->find('all', [
            'conditions' => [
                'video_stream_id' => $videoStreamId_2,
            ]
        ]);
        $this->assertSame(1, count($logVideoStreamId_2));
        $this->assertSame([
            'message' => Enum\Model\Video\VideoTranscodeLogType::WARNING,
            'values'  => [],
        ], json_decode($logVideoStreamId_2[0]['VideoTranscodeLog']['log'], true));

        $this->assertTrue(true);
    }
}
