<?php

use Goalous\Enum as Enum;

trait AwsEtsForVideoSourceTrait
{
    /**
     * @var bool
     */
    private $isForVideoSource = false;

    public function setForVideoSource(bool $bool)
    {
        $this->isForVideoSource = $bool;
    }

    public function isForVideoSource(): bool
    {
        return $this->isForVideoSource;
    }
}