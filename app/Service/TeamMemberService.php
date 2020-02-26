<?php
App::import('Service', 'AppService');
App::import('Service', 'TeamTranslationLanguageService');
App::import('Service', 'UnreadCirclePostService');
App::uses('TeamMember', 'Model');
App::uses('User', 'Model');
App::uses('TeamTranslationLanguage', 'Model');
App::uses('Email', 'Model');

use Goalous\Enum as Enum;
use Goalous\Exception as GlException;

/**
 * Class TeamMemberService
 */
class TeamMemberService extends AppService
{
    /**
     * Activate team member
     *
     * @param int $teamId
     * @param int $teamMemberId
     * @param int $opeUserId
     *
     * @return array [error:true|false, msg:""]
     */
    public function activateWithPayment(int $teamId, int $teamMemberId, int $opeUserId): array
    {
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init("TeamMember");
        /** @var Team $Team */
        $Team = ClassRegistry::init("Team");
        /** @var PaymentService $PaymentService */
        $PaymentService = ClassRegistry::init('PaymentService');

        $res = [
            'error' => false,
            'msg'   => "",
        ];

        try {
            $this->TransactionManager->begin();

            // Team member activate
            if (!$TeamMember->activate($teamMemberId)) {
                throw new Exception(sprintf("Failed to activate team member. data:%s",
                    AppUtil::varExportOneLine(compact('teamId', 'teamMemberId'))));
            }

            // Charge if paid plan
            if ($Team->isPaidPlan($teamId)) {
                $PaymentService->charge(
                    $teamId,
                    Enum\Model\ChargeHistory\ChargeType::USER_ACTIVATION_FEE(),
                    1,
                    $opeUserId
                );
            }
        } catch (CreditCardStatusException $e) {
            $this->TransactionManager->rollback();
            CakeLog::error(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            CakeLog::error($e->getTraceAsString());
            $res['error'] = true;
            $res['msg'] = __("Failed to activate team member.") . " " . __('There is a problem with your card.');
            return $res;
        } catch (StripeApiException $e) {
            $this->TransactionManager->rollback();
            CakeLog::emergency(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            CakeLog::emergency($e->getTraceAsString());
            $res['error'] = true;
            $res['msg'] = __("Failed to activate team member.") . " " . __('Please try again later.');
            return $res;
        } catch (Exception $e) {
            $this->TransactionManager->rollback();
            CakeLog::emergency(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            CakeLog::emergency($e->getTraceAsString());
            $res['error'] = true;
            $res['msg'] = __("Failed to activate team member.") . " " . __('System error has occurred.');
            return $res;
        }

        $this->TransactionManager->commit();
        return $res;
    }

    /**
     * Validate activate
     * - Check team plan
     * - Check being team member
     * - Check can activate status
     *
     * @param int $teamMemberId
     *
     * @return bool
     */
    public function validateActivation(int $teamId, int $teamMemberId): bool
    {
        /** @var Team $Team */
        $Team = ClassRegistry::init("Team");
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init("TeamMember");

        // Check team plan
        if (!$Team->isFreeTrial($teamId) && !$Team->isPaidPlan($teamId)) {
            return false;
        }

        // Check is team member
        if (!$TeamMember->isTeamMember($teamId, $teamMemberId)) {
            return false;
        }

        // Check inactive
        if (!$TeamMember->isInactive($teamMemberId)) {
            return false;
        }

        return true;
    }

    /**
     * Inactivate a team member by the team member id
     *
     * @param int $teamMemberId
     *
     * @return bool
     * @throws Exception
     */
    public function inactivate(int $teamMemberId): bool
    {
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init('TeamMember');
        /** @var User $User */
        $User = ClassRegistry::init('User');

        try {
            $this->TransactionManager->begin();

            $res = $TeamMember->inactivate($teamMemberId);

            if (!$res) {
                throw new RuntimeException();
            }

            $teamMember = $TeamMember->getById($teamMemberId);
            $user = $User->getById($teamMember['user_id']);

            //If inactivated team ID is the same as user's default one or is empty, update user's default team
            if (empty($user['default_team_id']) || $user['default_team_id'] == $teamMember['team_id']) {
                $newTeamId = $TeamMember->getLatestLoggedInActiveTeamId($teamMember['user_id']) ?: null;
                $User->updateDefaultTeam($newTeamId, true, $teamMember['user_id']);
            }

            // Delete unread post cache
            /** @var UnreadCirclePostService $UnreadCirclePostService */
            $UnreadCirclePostService = ClassRegistry::init('UnreadCirclePostService');
            $UnreadCirclePostService->deleteUserCacheInTeam($teamMember['team_id'], $teamMember['user_id']);

            $this->TransactionManager->commit();

            return true;
        } catch (Exception $exception) {
            $this->TransactionManager->rollback();
            throw $exception;
        }
    }

    /**
     * Get user's default translation language in a team. Will preferentially choose language from
     *                                browser language list.
     *
     * @param int   $teamId
     * @param int   $userId
     * @param array $browserLanguages Languages supported by user's browser.
     *
     * @return array
     *              ["en" => "English"]
     * @throws Exception
     */
    public function getDefaultTranslationLanguage(int $teamId, int $userId, array $browserLanguages = []): array
    {
        /** @var TeamTranslationLanguage $TeamTranslationLanguage */
        $TeamTranslationLanguage = ClassRegistry::init('TeamTranslationLanguage');

        if (!$TeamTranslationLanguage->hasLanguage($teamId)) {
            throw new GlException\GoalousNotFoundException("Team does not have translation languages");
        }

        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init('TeamMember');

        $defaultLanguage = $TeamMember->getDefaultTranslationLanguage($teamId, $userId);

        if (empty($defaultLanguage) || !$TeamTranslationLanguage->isLanguageSupported($teamId, $defaultLanguage)) {
            $defaultLanguage = $this->updateDefaultTranslationLanguage($teamId, $userId, $browserLanguages);
        }

        /** @var TranslationLanguage $TranslationLanguage */
        $TranslationLanguage = ClassRegistry::init('TranslationLanguage');
        $languageInfo = $TranslationLanguage->getLanguageByCode($defaultLanguage);

        return [$languageInfo['language'] => __($languageInfo['intl_name'])];
    }

    /**
     * Get language code of default translation language of an user in a team. Will preferentially choose language from
     *                                browser language list.
     *
     * @param int   $teamId
     * @param int   $userId
     * @param array $browserLanguages Languages supported by user's browser.
     *
     * @return string ISO 639-1 Language Code
     * @throws Exception
     */
    public function getDefaultTranslationLanguageCode(int $teamId, int $userId, array $browserLanguages = []): string
    {
        return array_keys($this->getDefaultTranslationLanguage($teamId, $userId, $browserLanguages))[0];
    }

    /**
     * Set user's default translation language in a team
     *
     * @param int    $teamId
     * @param int    $userId
     * @param string $langCode
     * @param bool   $overwriteFlg Whether language is saved even when data exists.
     *                             TRUE for overwriting, FALSE for skipping when data exists
     *
     * @throws Exception
     */
    public function setDefaultTranslationLanguage(int $teamId, int $userId, string $langCode, bool $overwriteFlg = true)
    {
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init('TeamMember');

        // If user already has a default translation language & overwriting is not allowed, end method
        if (!($overwriteFlg || empty($TeamMember->getDefaultTranslationLanguage($teamId, $userId)))) {
            return;
        }

        try {
            $this->TransactionManager->begin();
            $TeamMember->setDefaultTranslationLanguage($teamId, $userId, $langCode);
            $this->TransactionManager->commit();
        } catch (Exception $e) {
            $this->TransactionManager->rollback();
            GoalousLog::error("Failed to set default translation language.", [
                'message'                      => $e->getMessage(),
                'trace'                        => $e->getTraceAsString(),
                'team_id'                      => $teamId,
                'user_id'                      => $userId,
                'default_translation_language' => $langCode
            ]);
            throw $e;
        }
    }

    /**
     * Update team member's default translation language based on user's browser language. If not, use team's
     *
     * @param int   $teamId
     * @param int   $userId
     * @param array $browserLanguages ISO 639-1 language code array
     * @param bool  $overwriteFlg     True for updating regardless if team member already has a default translation
     *                                language
     *
     * @return string Team member's new language
     * @throws Exception
     */
    public function updateDefaultTranslationLanguage(
        int $teamId,
        int $userId,
        array $browserLanguages,
        bool $overwriteFlg = true
    ): string {
        /** @var TeamTranslationLanguageService $TeamTranslationLanguageService */
        $TeamTranslationLanguageService = ClassRegistry::init('TeamTranslationLanguageService');

        if (!empty($browserLanguages)) {
            $topBrowserLanguage = $TeamTranslationLanguageService->selectFirstSupportedLanguage($teamId,
                $browserLanguages);
        }

        if (empty($topBrowserLanguage)) {
            $defaultLanguage = $TeamTranslationLanguageService->getDefaultTranslationLanguageCode($teamId);
        } else {
            $defaultLanguage = $topBrowserLanguage;
        }

        $this->setDefaultTranslationLanguage($teamId, $userId, $defaultLanguage, $overwriteFlg);

        return $defaultLanguage;
    }

    /**
     * Initialize user's default translation language in a team
     *
     * @param int   $teamId
     * @param int   $userId
     * @param array $browserLanguages
     *
     * @throws Exception
     */
    public function initializeDefaultTranslationLanguage(int $teamId, int $userId, array $browserLanguages)
    {
        /** @var TeamTranslationLanguage $TeamTranslationLanguage */
        $TeamTranslationLanguage = ClassRegistry::init('TeamTranslationLanguage');

        if (!$TeamTranslationLanguage->hasLanguage($teamId)) {
            return;
        }

        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init('TeamMember');

        $defaultLanguage = $TeamMember->getDefaultTranslationLanguage($teamId, $userId);

        // If user alreadh have a valid default translation language, return
        if (!empty($defaultLanguage) && $TeamTranslationLanguage->isLanguageSupported($teamId, $defaultLanguage)) {
            return;
        }

        $this->updateDefaultTranslationLanguage($teamId, $userId, $browserLanguages, false);
    }


    /**
     * Update del_flg to delete user.
     *
     * Usecase example
     *  - Revoke inivitation
     *
     * - Update DB
     *  - team_members
     *
     * @param int    $teamId
     * @param string $emails
     */
    public function updateDelFlgToRevoke(int $teamId, string $email)
    {
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init("TeamMember");
        /** @var Email $Invite */
        $Email = ClassRegistry::init("Email");

        $emailObj = $Email->find('first', [
                'fields'     => [
                    'user_id'
                ],
                'conditions' => [
                    'email' => $email
                ]
            ]
        );
        $userId = Hash::get($emailObj,'Email.user_id');

        if (empty($userId)) {
            throw new GlException\GoalousNotFoundException("UserId not found");
        }

        $this->TransactionManager->begin();

        try{
            $TeamMember->deleteTeamMember($teamId, $userId);
        } catch (RuntimeException $e) {
            $this->TransactionManager->rollback();
            GoalousLog::error("Failed to delete team_members record to revoke invitation.", [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'team_id' => $teamId,
                'user_id' => $userId,
                'email'   => $email
            ]);
            throw $e;
        }

        $this->TransactionManager->commit();
    }
}
