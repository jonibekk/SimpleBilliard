<?php

/**
 * Class UrlUtil
 */
class UrlUtil
{
    static function fqdnFrontEnd(): string {
        if (ENV_NAME === "local") {
            if (SESSION_DOMAIN == 'localhost'){
                return "http://localhost:5790";
            } else {
                return "http://local.goalous.com:5790";
            }
        }
        return '';
    }

}
