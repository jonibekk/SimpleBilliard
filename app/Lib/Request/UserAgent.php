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

    static function detect($userAgent = null): self
    {
        if (!is_string($userAgent)) {
            $userAgent = Hash::get($_SERVER, 'HTTP_USER_AGENT');
        }
        return new self($userAgent);
    }

    public function __construct(string $userAgent)
    {
        $this->userAgent = $userAgent;
        $this->parseUserAgent();
    }

    private function parseUserAgent()
    {
        $this->isiOSAccess       = (false !== strpos($this->userAgent, self::USER_AGENT_APP_IOS));
        $this->isAndroidAccess   = (false !== strpos($this->userAgent, self::USER_AGENT_APP_ANDROID));
        $this->isMobileAppAccess = $this->isiOSAccess || $this->isAndroidAccess;

        if ($this->isMobileAppAccess) {
            $this->parseMobileAppUserAgent();
        }
    }

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

    public function getMobileAppVersion(): string
    {
        return $this->mobileAppVersion;
    }

    public function isMobileAppAccess(): bool
    {
        return $this->isMobileAppAccess;
    }

    public function isiOSApp(): bool
    {
        return $this->isiOSAccess;
    }

    public function isAndroidApp(): bool
    {
        return $this->isAndroidAccess;
    }

    public function getMobileAppEnvironment(): string
    {
        return $this->mobileAppEnvironment;
    }
}
