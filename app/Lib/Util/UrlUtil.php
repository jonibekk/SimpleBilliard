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

    /**
     * Enclose url of chosen URL with given string
     *
     * @param string $baseString String containing URL
     * @param array  $protocols  Protocols. E.g. http, https, etc.
     * @param string $prefix
     * @param string $suffix
     *
     * @return string
     */
    public static function encapsulateUrl(string $baseString, array $protocols, string $prefix, string $suffix): string
    {
        /**
         * Taken from site below, with some modification
         *
         * @url https://urlregex.com/
         */
        $urlPattern = "%(" . implode(
                "|",
                $protocols
            ) . ")://(?:\S+(?::\S*)?@|\d{1,3}(?:\.\d{1,3}){3}|(?:(?:[a-z\d\x{00a1}-\x{ffff}]+-?)*[a-z\d\x{00a1}-\x{ffff}]+)(?:\.(?:[a-z\d\x{00a1}-\x{ffff}]+-?)*[a-z\d\x{00a1}-\x{ffff}]+)*(?:\.[a-z\x{00a1}-\x{ffff}]{2,6}))(?::\d+)?(?:[^\s]*)?%iu";

        //Check if there is a url in the text
        if (preg_match($urlPattern, $baseString, $url)) {

            $result = preg_replace($urlPattern, $prefix.'${0}'.$suffix, $baseString);

            return $result;
        }

        return $baseString;
    }
}
