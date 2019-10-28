<?php
App::uses('TeamMember', 'Model');
App::uses('TeamTranslationStatus', 'Model');
App::import('Lib/Cache/Redis/NotificationFlag', 'NotificationFlagClient');
App::import('Lib/Cache/Redis/NotificationFlag', 'NotificationFlagKey');

use Goalous\Enum\NotificationFlag\Name as NotificationFlagName;

trait TranslationNotificationTrait
{
    public function sendTranslationUsageNotification(int $teamId)
    {
        /** @var TeamTranslationStatus $TeamTranslationStatus */
        $TeamTranslationStatus = ClassRegistry::init('TeamTranslationStatus');

        if (!$TeamTranslationStatus->hasEntry($teamId)) {
            return;
        }

        $teamTranslationStatus = $TeamTranslationStatus->getUsageStatus($teamId);

        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init('TeamMember');

        $notificationFlagClient = new NotificationFlagClient();

        $limitReachedKey = new NotificationFlagKey($teamId, NotificationFlagName::TYPE_TRANSLATION_LIMIT_REACHED());
        $limitClosingKey = new NotificationFlagKey($teamId, NotificationFlagName::TYPE_TRANSLATION_LIMIT_CLOSING());

        if (empty($notificationFlagClient->read($limitReachedKey)) && $teamTranslationStatus->isLimitReached()) {
            $this->notifyTranslateLimitReached($teamId, $TeamMember->findAdminList($teamId) ?? []);
            $notificationFlagClient->write($limitReachedKey);
        } else if (empty($notificationFlagClient->read($limitClosingKey)) && $teamTranslationStatus->isUsageWithinPercentageOfLimit(0.1)) {
            $this->notifyTranslateLimitClosing($teamId, $TeamMember->findAdminList($teamId) ?? []);
            $notificationFlagClient->write($limitClosingKey);
        }
    }

    private function notifyTranslateLimitReached(int $teamId, array $userIds)
    {
        $this->NotifyBiz->sendNotify(
            NotifySetting::TYPE_TRANSLATION_LIMIT_REACHED,
            null,
            null,
            $userIds,
            null,
            $teamId);
    }

    private function notifyTranslateLimitClosing(int $teamId, array $userIds)
    {
        $this->NotifyBiz->sendNotify(
            NotifySetting::TYPE_TRANSLATION_LIMIT_CLOSING,
            null,
            null,
            $userIds,
            null,
            $teamId);
    }
}
