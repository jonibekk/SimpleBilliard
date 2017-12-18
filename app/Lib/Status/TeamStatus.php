<?php
App::import('Service', 'TeamService');
App::import('Service', 'CampaignService');

use Goalous\Model\Enum as Enum;

class TeamStatus {

    /**
     * @var self
     */
    private static $current = null;

    private $teamId = null;

    private $isTeamCampaign = false;

    private $enabledVideoPostInEnvironment = false;

    private $serviceUserStatus = null;

    private function __construct()
    {
        $this->serviceUserStatus = Enum\Team\ServiceUseStatus::FREE_TRIAL();
    }

    /**
     * singleton
     * @return TeamStatus
     */
    public static function getCurrentTeam(): self
    {
        if (is_null(self::$current)) {
            self::$current = new self();
        }
        return self::$current;
    }

    /**
     * Initialize instance property from passed team id
     * normally uses this method to set instance properties
     *
     * @param int $teamId
     */
    public function initializeByTeamId(int $teamId)
    {
        /** @var TeamService $TeamService */
        $TeamService = ClassRegistry::init('TeamService');
        /** @var CampaignService $CampaignService */
        $CampaignService = ClassRegistry::init('CampaignService');

        $this->setTeamId($teamId);
        $this->setIsTeamCampaign($CampaignService->isCampaignTeam($teamId));

        $serviceUseStatus = null;
        try {
            // getServiceUseStatus() method is not considering guest user(not login)
            // Not considering method call from non-login user(external API's), batch shell ...
            // If $Team->current_team is not set, this method
            // Cause Notice Error and throwing Error Exception (both)
            // "@" suppressing Notice Error
            // Catch(\Throwable) is catching Error Exception that can expect
            // But this method is useful if user is login

            // getServiceUseStatus() はログインユーザー以外からの呼出しを考慮していない作り
            // 外部APIやバッチからの利用が想定されていない
            // $Team->current_team に依存しており, 設定されていなければ
            // Notice Error と Error Exception を同時に発生させる
            // "@" は Notice Error を抑制する為
            // catch節は Error Exceptionを補足する為
            // getServiceUseStatus() はユーザーがログインしている場合に限り有用なので利用する
            $serviceUseStatus = @$TeamService->getServiceUseStatus();
        } catch (\Throwable $throwable) {
            // catching in here is expected behaviour
            // even logging is not needed

            // ここに到達する事は想定している挙動のため、ログ出力もしない
        }
        if (is_null($serviceUseStatus)) {
            // Could not get from $TeamService->getServiceUseStatus(), fetch from DB
            $serviceUseStatus = $TeamService->getServiceUseStatusByTeamId($teamId);
        }

        if (!is_null($serviceUseStatus)) {
            $this->setServiceUseStatus(new Enum\Team\ServiceUseStatus($serviceUseStatus));
        }

        if (defined('ENABLE_VIDEO_POST') && is_bool(ENABLE_VIDEO_POST)) {
            $this->setEnabledVideoPostInEnvironment(ENABLE_VIDEO_POST);
        }
    }

    /**
     * set teams service use status
     *
     * @param Enum\Team\ServiceUseStatus $serviceUseStatus
     */
    public function setServiceUseStatus(Enum\Team\ServiceUseStatus $serviceUseStatus)
    {
        $this->serviceUserStatus = $serviceUseStatus;
    }

    /**
     * set teams id
     *
     * @param int $teamId
     */
    public function setTeamId(int $teamId)
    {
        $this->teamId = $teamId;
    }

    /**
     * set true if team paid on campaign
     *
     * @param bool $isTeamCampaign
     */
    public function setIsTeamCampaign(bool $isTeamCampaign)
    {
        $this->isTeamCampaign = $isTeamCampaign;
    }

    /**
     * set bool if current environment is video post enabled
     *
     * @param bool $isEnabled
     */
    public function setEnabledVideoPostInEnvironment(bool $isEnabled)
    {
        $this->enabledVideoPostInEnvironment = $isEnabled;
    }

    /**
     * return true if team is on any paid plan
     *
     * @return bool
     */
    public function isTeamPaid(): bool
    {
        return $this->getServiceUseStatus()->equals(Enum\Team\ServiceUseStatus::PAID());
    }

    /**
     * return true if team is campaign team
     *
     * @return bool
     */
    public function isTeamCampaign(): bool
    {
        return $this->isTeamCampaign;
    }

    /**
     * return true if team is not paid any plan
     *
     * @return bool
     */
    public function isTeamPlanRegular(): bool
    {
        return !$this->isTeamPaid();
    }

    /**
     * return true if able to post video
     *
     * @return bool
     */
    public function isAbleToPostVideo(): bool
    {
        return $this->enabledVideoPostInEnvironment;
    }

    /**
     * this method is for testing
     * do not use in product code
     */
    public static function reset()
    {
        self::$current = null;
    }

    /**
     * return current users team id
     *
     * @return null|int
     */
    public function getTeamId()
    {
        return $this->teamId;
    }

    /**
     * @return Enum\Team\ServiceUseStatus
     */
    public function getServiceUseStatus(): Enum\Team\ServiceUseStatus
    {
        return $this->serviceUserStatus;
    }

    /**
     * return teams plan
     *
     * @return Enum\TeamPlan
     */
    public function getTeamPlan(): Enum\TeamPlan
    {
        if ($this->getServiceUseStatus()->equals(Enum\Team\ServiceUseStatus::PAID())) {
            return Enum\TeamPlan::PAID();
        }
        return Enum\TeamPlan::REGULAR();
    }

    /**
     * return teams video transcoding quality
     *
     * @return Enum\TranscodePattern
     */
    public function getTranscodeQuality(): Enum\TranscodePattern
    {
        return Enum\TranscodePattern::LIMITED();
    }
}
