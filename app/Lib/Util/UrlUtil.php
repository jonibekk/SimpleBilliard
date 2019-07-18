<?php

/**
 * Class UrlUtil
 */
class UrlUtil
{
    static function fqdnFrontEnd(): string {
        if (ENV_NAME === "local") {
            return "http://local.goalous.com:5790";
        }
        return '';
    }

}
