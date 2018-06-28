<?php

namespace Goalous\Enum\Model\Video;

use MyCLabs\Enum\Enum;

/**
 * Class VideoTranscodeLogType
 * this class is use for logging message of video transcode
 *
 * e.g.
 *      $VideoTranscodeLog->add($videoStreamsId, VideoTranscodeLogType::JOB_REGISTERED() ['transcoder' => 'aws']);
 *
 * @method static static JOB_REGISTERED()
 * @method static static STATUS_PROGRESSED()
 * @method static static ERROR()
 * @method static static WARNING()
 * @method static static JUDGED_STUCK()
 */
class VideoTranscodeLogType extends Enum
{
    /**
     * When transcode job is created
     * @var string
     */
    const JOB_REGISTERED = 'job_registered';

    /**
     * When transcode is progressed and notified by any method(e.g. AWS SNS)
     * @var string
     */
    const STATUS_PROGRESSED = 'status_progressed';

    /**
     * When transcode detected error
     * @var string
     */
    const ERROR = 'error';

    /**
     * When transcode detected warning
     * @var string
     */
    const WARNING = 'warning';

    /**
     * When transcode job has no progress response
     * and system(Goalous) decided the transcode job is stopped in any reason.
     *
     * @var string
     */
    const JUDGED_STUCK = 'judged_stuck';
}