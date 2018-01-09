<?php
App::uses('GoalousTestCase', 'Test');
App::uses('TranscodeInfo', 'Lib/Video/Stream');
App::uses('AppUtil', 'Util');

use Goalous\Model\Enum as Enum;

/**
 * VideoStreamTest Test Case
 */
class TranscodeInfoTest extends GoalousTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
    ];

    function tearDown()
    {
        parent::tearDown();
    }

    function test_empty()
    {
        $transcodeInfo = TranscodeInfo::createNew();
        $this->assertNull($transcodeInfo->getTranscodeJobId());
        $this->assertNull($transcodeInfo->getTranscoderType());
        $this->assertFalse($transcodeInfo->isTranscodeNoProgress());
        $jsonString = $transcodeInfo->toJson();

        $transcodeInfoArray = json_decode($jsonString, true);

        $this->assertEquals([], $transcodeInfoArray);
    }

    function test_createFromJson()
    {
        $jobId = '1234567890';
        $transcoder = Enum\Video\Transcoder::AWS_ETS;
        $transcodeErrors = ['a', 'b', 'c'];
        $transcodeWarnings = ['d', 'e', 'f'];
        $transcodeInfo = TranscodeInfo::createFromJson(json_encode([
            TranscodeInfo::HASH_TRANSCODER => $transcoder,
            TranscodeInfo::HASH_TRANSCODE_JOB_ID => $jobId,
            'transcode' => [
                'no_progress' => true,
                'errors' => $transcodeErrors,
                'warnings' => $transcodeWarnings,
            ]
        ]));
        $this->assertEquals($jobId, $transcodeInfo->getTranscodeJobId());
        $this->assertEquals($transcoder, $transcodeInfo->getTranscoderType());
        $this->assertTrue($transcodeInfo->isTranscodeNoProgress());
        $this->assertEquals($transcodeErrors, $transcodeInfo->getTranscodeErrors());
        $this->assertEquals($transcodeWarnings, $transcodeInfo->getTranscodeWarnings());
    }

    function test_setValues()
    {
        $jobId = '1234567890';
        $transcoder = Enum\Video\Transcoder::AWS_ETS();

        $transcodeInfo = TranscodeInfo::createNew();
        $transcodeInfo->setTranscodeJobId($jobId);
        $transcodeInfo->setTranscoderType($transcoder);
        $transcodeInfo->setTranscodeNoProgress(true);
        $transcodeInfo->addTranscodeError('a');
        $transcodeInfo->addTranscodeError('b');
        $transcodeInfo->addTranscodeError('c');
        $transcodeInfo->addTranscodeWarning('d');
        $transcodeInfo->addTranscodeWarning('e');
        $transcodeInfo->addTranscodeWarning('f');
        $jsonString = $transcodeInfo->toJson();

        $transcodeInfoArray = json_decode($jsonString, true);

        $this->assertEquals([
            TranscodeInfo::HASH_TRANSCODER => $transcoder->getValue(),
            TranscodeInfo::HASH_TRANSCODE_JOB_ID => $jobId,
            'transcode' => [
                'no_progress' => true,
                'errors' => ['a', 'b', 'c'],
                'warnings' => ['d', 'e', 'f'],
            ]
        ], $transcodeInfoArray);

    }
}
