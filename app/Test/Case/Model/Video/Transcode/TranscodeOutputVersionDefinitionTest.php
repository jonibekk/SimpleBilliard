<?php

App::uses('GoalousTestCase', 'Test');
App::import('Model/Video/Transcode', 'TranscodeOutputVersionDefinition');

use Goalous\Model\Enum as Enum;

/**
 * Class TranscodeOutputVersionManagerTest
 */
class TranscodeOutputVersionDefinitionTest extends GoalousTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.badge',
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();
    }

    function test_videoSource_v1()
    {
        $transcodeOutput = TranscodeOutputVersionDefinition::getVersion(Enum\Video\TranscodeOutputVersion::V1());

        $baseUrl = 'https://s3.aws.com/stream/';
        $videoSources = $transcodeOutput->getVideoSources($baseUrl);
        $this->assertTrue(
            $videoSources[0]->getType()->equals(Enum\Video\VideoSourceType::VIDEO_WEBM())
        );
        $this->assertEquals(
            $baseUrl . 'webm_500k/video.webm',
            $videoSources[0]->getSource()
        );
        $this->assertTrue(
            $videoSources[1]->getType()->equals(Enum\Video\VideoSourceType::PLAYLIST_M3U8_HLS())
        );
        $this->assertEquals(
            $baseUrl . 'playlist.m3u8',
            $videoSources[1]->getSource()
        );
    }

    function test_jobParameter_v1()
    {
        $transcodeOutput = TranscodeOutputVersionDefinition::getVersion(Enum\Video\TranscodeOutputVersion::V1());

        $key = 'key';
        $pipelineId = 'pipeline-id';
        $outputKeyPrefix = 'output_key_prefix';
        $userMetaData = [
            'videos.id' => 1,
            'video_streams.id' => 2,
        ];

        $expectedArray = [
            'PipelineId'      => $pipelineId,
            'OutputKeyPrefix' => $outputKeyPrefix,
            'Input'           => [
                'Key'         => $key,
                'FrameRate'   => 'auto',
                'Resolution'  => 'auto',
                'AspectRatio' => 'auto',
                'Interlaced'  => 'auto',
                'Container'   => 'auto',
            ],
            'Outputs'          => [
                [
                    'Key'              => 'webm_500k/video.webm',
                    'ThumbnailPattern' => 'thumbs-{count}',
                    'PresetId'         => '1513327166916-ghbctw',
                    'Rotate'           => 'auto',
                    'Watermarks'       => [],
                ],
                [
                    'Key'              => 'ts_500k/video',
                    'ThumbnailPattern' => 'thumbs-{count}',
                    'PresetId'         => '1513234427744-pkctj7',
                    'Rotate'           => 'auto',
                    'Watermarks'       => [],
                    'SegmentDuration'  => 10,
                ],
            ],
            'Playlists'       => [
                [
                    'Format'     => 'HLSv3',
                    'Name'       => 'playlist',
                    'OutputKeys' => [
                        'ts_500k/video',
                    ],
                ],
            ],
            'UserMetadata'    => [
                'videos.id'                => 1,
                'video_streams.id'         => 2,
                'transcode_output_version' => Enum\Video\TranscodeOutputVersion::V1,
            ],
        ];

        $this->assertEquals($expectedArray, $transcodeOutput->getCreateJobArray($key, $pipelineId, $outputKeyPrefix, $userMetaData, false));

        $expectedArray['Outputs'][0]['Watermarks'][0] = [
            'InputKey' => 'images/watermark_h264.png',
            'PresetWatermarkId' => 'TopLeft',
        ];
        $expectedArray['Outputs'][1]['Watermarks'][0] = [
            'InputKey' => 'images/watermark_vp9.png',
            'PresetWatermarkId' => 'TopLeft',
        ];
        $this->assertEquals($expectedArray, $transcodeOutput->getCreateJobArray($key, $pipelineId, $outputKeyPrefix, $userMetaData, true));
    }
}
