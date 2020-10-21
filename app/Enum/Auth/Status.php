<?php


namespace Goalous\Enum\Auth;

use MyCLabs\Enum\Enum;

class Status extends Enum
{
    const OK = "OK";
    //Email address not found
    const USER_NOT_FOUND = "USER_NOT_FOUND";
    //Password mismatch, SSO faile
    const AUTH_MISMATCH = "AUTH_MISMATCH";
    //Internal system error
    const AUTH_ERROR = "AUTH_ERROR";
    //Password login ok, but request 2FA
    const REQUEST_2FA = "REQUEST_2FA";
}
