<?php

App::uses('AwsEtsTranscodeJob', 'Model/Video/Transcode/AwsEtsStructure');
App::uses('AwsEtsTranscodeOutput', 'Model/Video/Transcode/AwsEtsStructure');
App::uses('AwsEtsTranscodeOutputPlaylist', 'Model/Video/Transcode/AwsEtsStructure');
App::uses('AwsEtsForVideoSourceTrait', 'Model/Video/Transcode/AwsEtsStructure');

use Goalous\Model\Enum as Enum;

class TranscodeOutputVersionDefinition
{
    /**
     * definitions of the video output
     * @see https://confluence.goalous.com/display/GOAL/Video+Transcode+Output+Versions
     *
     * @param Enum\Video\TranscodeOutputVersion $transcodeOutputVersion
     *
     * @return TranscodeOutput
     */
    public static function getVersion(Enum\Video\TranscodeOutputVersion $transcodeOutputVersion): TranscodeOutput
    {
        switch ($transcodeOutputVersion->getValue()) {
            // Do not change values of the Version
            // if necessary to change the value, create the new Version
            case Enum\Video\TranscodeOutputVersion::V1:
                $outputVp9  = new AwsEtsTranscodeOutput(Enum\Video\VideoSourceType::VIDEO_WEBM(), 'webm_500k/video.webm', 'thumbs-{count}', '1513327166916-ghbctw');
                $outputVp9->setForVideoSource(true);
                $outputVp9->addWaterMark('images/watermark_vp9.png', 'TopLeft');
                $outputH264 = new AwsEtsTranscodeOutput(Enum\Video\VideoSourceType::NOT_RECOMMENDED(), 'ts_500k/video', 'ts_500k/thumbs-{count}', '1513234427744-pkctj7');
                $outputH264->addWaterMark('images/watermark_h264.png', 'TopLeft');
                $outputH264->setSegmentDuration(10);
                $outputPlaylistHls = new AwsEtsTranscodeOutputPlaylist(Enum\Video\VideoSourceType::PLAYLIST_M3U8_HLS(), 'HLSv3', 'playlist', [
                    $outputH264,
                ]);
                $outputPlaylistHls->setForVideoSource(true);
                $outputPlaylistHls->setEnableHlsContentProtection(true);
                $transcodeOutput = new AwsEtsTranscodeJob(
                    $transcodeOutputVersion, Enum\Video\Transcoder::AWS_ETS()
                );
                $transcodeOutput->addOutputVideo($outputVp9);
                $transcodeOutput->addOutputVideo($outputH264);
                $transcodeOutput->addOutputPlaylist($outputPlaylistHls);
                return $transcodeOutput;
        }
        throw new InvalidArgumentException('version not defined');
    }
}


