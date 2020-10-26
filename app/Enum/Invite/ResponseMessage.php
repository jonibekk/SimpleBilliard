<?php


namespace Goalous\Enum\Invite;


class ResponseMessage
{
    const SUCCESS = "INVITE_SUCCESS";

    const FAILED = "INVITE_FAILED";

    const FAILED_INVALID_USER = "INVITE_FAILED_INVALID_USER";

    const FAILED_TOKEN_USED = "INVITE_FAILED_TOKEN_USED";

    const FAILED_TOKEN_EXPIRED = "INVITE_FAILED_TOKEN_EXPIRED";
}
