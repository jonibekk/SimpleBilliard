<?php

class UserAgent
{
    /**
     * iOS/Android mobile app User-Agent definition
     * @var string
     */
    const USER_AGENT_APP_IOS     = 'Goalous App iOS';
    const USER_AGENT_APP_ANDROID = 'Goalous App Android';

    /**
     * Requested User-Agent
     * @var string
     */
    private $userAgent = '';

    /**
     * is requested User-Agent for mobile access
     * @var bool
     */
    private $isMobileAppAccess = false;

    /**
     * @var bool
     */
    private $isiOSAccess = false;

    /**
     * @var bool
     */
    private $isAndroidAccess = false;

    /**
     * environment of mobile app
     * @var string
     */
    private $mobileAppEnvironment = '';

    /**
     * version of mobile app
     * @var string
     */
    private $mobileAppVersion = '';

    /**
     * parse User-Agent Header string
     * @param null|string $userAgent
     *
     * @return UserAgent
     */
    static function detect($userAgent = null): self
    {
        if (!is_string($userAgent)) {
            $userAgent = Hash::get($_SERVER, 'HTTP_USER_AGENT');
        }
        return new self($userAgent);
    }

    /**
     * @param string $userAgent
     */
    public function __construct(string $userAgent)
    {
        $this->userAgent = $userAgent;
        $this->parseUserAgent();
    }

    /**
     * detecting device
     */
    private function parseUserAgent()
    {
        $this->isiOSAccess       = (false !== strpos($this->userAgent, self::USER_AGENT_APP_IOS));
        $this->isAndroidAccess   = (false !== strpos($this->userAgent, self::USER_AGENT_APP_ANDROID));
        $this->isMobileAppAccess = $this->isiOSAccess || $this->isAndroidAccess;

        if ($this->isMobileAppAccess) {
            $this->parseMobileAppUserAgent();
        }
    }

    /**
     * return device type of User-Agent
     * @return string
     */
    private function getAppUserAgent(): string
    {
        if ($this->isiOSAccess) {
            return self::USER_AGENT_APP_IOS;
        }
        if ($this->isAndroidAccess) {
            return self::USER_AGENT_APP_ANDROID;
        }
        return '';
    }

    /**
     * parse environment, app version from User-Agent
     */
    private function parseMobileAppUserAgent()
    {
        // ex: parsing User-Agent
        // 'Mozilla/5.0 (iPhone; CPU iPhone OS 10_3_2 like Mac OS X) AppleWebKit/603.2.4 (KHTML, like Gecko) Mobile/14F89 Goalous App iOS (Dev, 1.1.2)'
        $regexAppUserAgent = sprintf('/%s *\((.+?)\)/', $this->getAppUserAgent());
        $match = [];
        if (1 !== preg_match($regexAppUserAgent, $this->userAgent, $match)) {
            return;
        }
        // $match[1] should be string 'Dev, 1.1.2'
        $goalousAppUserAgent = explode(',', $match[1]);
        $this->mobileAppEnvironment = trim($goalousAppUserAgent[0]);
        $this->mobileAppVersion     = trim($goalousAppUserAgent[1]);
    }

    /**
     * return app version
     * @return string
     */
    public function getMobileAppVersion(): string
    {
        return $this->mobileAppVersion;
    }

    /**
     * return true if access from mobile app
     * @return bool
     */
    public function isMobileAppAccess(): bool
    {
        return $this->isMobileAppAccess;
    }

    /**
     * return true if iOS app access
     * @return bool
     */
    public function isiOSApp(): bool
    {
        return $this->isiOSAccess;
    }

    /**
     * return true if Android app access
     * @return bool
     */
    public function isAndroidApp(): bool
    {
        return $this->isAndroidAccess;
    }

    /**
     * return environment of App User-Agent header
     * @return string
     */
    public function getMobileAppEnvironment(): string
    {
        return $this->mobileAppEnvironment;
    }
}
