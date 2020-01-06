<?php

use Goalous\Enum\NotificationFlag\Name as NotificationFlagName;

class PaymentFlagKey
{
    /*
     * 1: block
     * 2: use white list
     * 3: pass
     */
    const SWITCH_FLAG_NAME = "chargeOnSignUp";
    /*
     * pass white list
     */
    const SWITCH_WHITE_LIST_NAME = "chargeOnSignUpWhiteList";
    /*
     * start date
     */
    const SWITCH_START_DATE_NAME = "chargeOnSignUpStartDate";

    /**
     * @var string
     */
    private $paymentKeyName;

    public function __construct(string $paymentKeyName)
    {
        $this->paymentKeyName = $paymentKeyName;
    }

    public function toRedisKey(): string
    {
        return sprintf('%s',
            $this->paymentKeyName);
    }
}
