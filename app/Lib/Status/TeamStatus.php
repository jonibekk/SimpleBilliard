<?php
App::import('Service', 'TeamService');
App::import('Service', 'CampaignService');
App::uses('Experiment', 'Model');

use Goalous\Enum as Enum;

class TeamStatus {

    /**
     * @var self
     */
    private static $current = null;

    private $teamId = null;

    private $isTeamCampaign = false;

    private $serviceUserStatus = null;

    private $teamsExperiments = [];

    /**
     * Video post feature enable/disable
     * @see https://confluence.goalous.com/pages/viewpage.action?pageId=13861014
     */
    private $enableVideoPostTranscodingOnEnvironment = false;
    private $enableVideoPostPlayOnEnvironment = false;

    private function __construct()
    {
        $this->serviceUserStatus = Enum\Model\Team\ServiceUseStatus::FREE_TRIAL();
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
        /** @var Experiment $Experiment */
        $Experiment = ClassRegistry::init('Experiment');

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
            $this->setServiceUseStatus(new Enum\Model\Team\ServiceUseStatus($serviceUseStatus));
        }

        // fetch experiment settings of team
        $experiments = $Experiment->findAllByTeamId($this->getTeamId());
        $experiments = Hash::extract($experiments, '{n}.Experiment');
        foreach ($experiments as $experiment) {
            $this->teamsExperiments[$experiment['name']] = true;
        }

        $this->setEnableVideoPostTranscodingOnEnvironment(boolval(ENABLE_VIDEO_POST_TRANSCODING));
        $this->setEnableVideoPostPlayOnEnvironment(boolval(ENABLE_VIDEO_POST_PLAY));
    }

    /**
     * return true if team can transcode video
     * @see https://confluence.goalous.com/pages/viewpage.action?pageId=13861014
     *
     * @return bool
     */
    public function canVideoPostTranscode(): bool
    {
        return true;
    }

    /**
     * return true if team can play video
     * @see https://confluence.goalous.com/pages/viewpage.action?pageId=13861014
     *
     * @return bool
     */
    public function canVideoPostPlay(): bool
    {
        return true;
    }

    /**
     * @param mixed $enableVideoPostTranscodingOnEnvironment
     */
    public function setEnableVideoPostTranscodingOnEnvironment(bool $enableVideoPostTranscodingOnEnvironment)
    {
        $this->enableVideoPostTranscodingOnEnvironment = $enableVideoPostTranscodingOnEnvironment;
    }

    /**
     * @param bool $enableVideoPostPlayOnEnvironment
     */
    public function setEnableVideoPostPlayOnEnvironment(bool $enableVideoPostPlayOnEnvironment)
    {
        $this->enableVideoPostPlayOnEnvironment = $enableVideoPostPlayOnEnvironment;
    }

    /**
     * return true if teams experiment is enabled
     * @param string $experimentName
     *
     * @return bool
     */
    public function isEnableExperiment(string $experimentName): bool
    {
        return $this->teamsExperiments[$experimentName] ?? false;
    }

    /**
     * set teams service use status
     *
     * @param Enum\Model\Team\ServiceUseStatus $serviceUseStatus
     */
    public function setServiceUseStatus(Enum\Model\Team\ServiceUseStatus $serviceUseStatus)
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
     * return true if team is on any paid plan
     *
     * @return bool
     */
    public function isTeamPaid(): bool
    {
        return $this->getServiceUseStatus()->equals(Enum\Model\Team\ServiceUseStatus::PAID());
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
     * @return Enum\Model\Team\ServiceUseStatus
     */
    public function getServiceUseStatus(): Enum\Model\Team\ServiceUseStatus
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
        if ($this->getServiceUseStatus()->equals(Enum\Model\Team\ServiceUseStatus::PAID())) {
            return Enum\TeamPlan::PAID();
        }
        return Enum\TeamPlan::REGULAR();
    }

    /**
     * @return Enum\Model\Video\TranscodeOutputVersion
     */
    public function getTranscodeOutputVersion(): Enum\Model\Video\TranscodeOutputVersion
    {
        return Enum\Model\Video\TranscodeOutputVersion::V1();
    }
}
