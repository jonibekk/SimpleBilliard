<?php

use Goalous\Model\Enum as Enum;

class AwsEtsTranscodeInput
{
    /**
     * @var string
     */
    private $inputKey;

    /**
     * @var array
     */
    private $timeSpan = [];

    public function __construct(string $inputKey)
    {
        $this->inputKey = $inputKey;
    }

    /**
     * @see http://docs.aws.amazon.com/aws-sdk-php/v3/api/api-elastictranscoder-2012-09-25.html#shape-timespan
     * @param int $durationSecond
     * @param int $startTimeSecond
     */
    public function setTimeSpan(int $durationSecond, int $startTimeSecond)
    {
        $this->timeSpan = [
            'Duration'  => sprintf('%d.000', $durationSecond),
            'StartTime' => sprintf('%d.000', $startTimeSecond),
        ];
    }

    /**
     * @see http://docs.aws.amazon.com/aws-sdk-php/v3/api/api-elastictranscoder-2012-09-25.html#shape-jobinput
     */
    public function getOutputArray(): array
    {
        return [
            'Key'         => $this->inputKey,
            'FrameRate'   => 'auto',
            'Resolution'  => 'auto',
            'AspectRatio' => 'auto',
            'Interlaced'  => 'auto',
            'Container'   => 'auto',
            'TimeSpan'    => $this->timeSpan,
        ];
    }
}
