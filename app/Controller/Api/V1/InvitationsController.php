<?php
App::uses('ApiController', 'Controller/Api');
App::uses('AppUtil', 'Util');
App::uses('PaymentUtil', 'Util');
App::import('Service', 'InvitationService');
App::import('Service', 'TeamMemberService');
App::import('Service', 'PaymentService');
App::import('Service', 'CampaignService');

use Goalous\Enum as Enum;

/**
 * Class InvitationsController
 *
 * @property NotificationComponent $Notification
 */
class InvitationsController extends ApiController
{
    public $components = [
        'Notification',
    ];

    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->_checkAdmin();
    }

    /**
     * Validation
     */
    function post_validate()
    {
        /** @var InvitationService $InvitationService */
        $InvitationService = ClassRegistry::init("InvitationService");
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init("TeamMember");

        // Check permission
        if (!$TeamMember->isAdmin($this->Auth->user('id'))) {
            return $this->_getResponseForbidden();
        }

        $emailsStr = $this->request->data("emails");
        if (!is_string($emailsStr)) {
            return $this->_getResponseValidationFail(["Parameter is invalid"]);
        }
        // Convert to mail address list.
        $emails = AppUtil::convStrToArr($emailsStr);
        // Validation
        $errors = $InvitationService->validateEmails($this->current_team_id, $emails);
        if (!empty($errors)) {
            return $this->_getResponseValidationFail($errors);
        }
        return $this->_getResponseSuccess(compact('emails'));
    }

    /**
     * Get information for invitation input page.
     */
    function get_input()
    {
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init("TeamMember");
        /** @var Team $Team */
        $Team = ClassRegistry::init("Team");

        // Check permission
        if (!$TeamMember->isAdmin($this->Auth->user('id'))) {
            return $this->_getResponseForbidden();
        }

        $team = Hash::get($Team->getCurrentTeam(), 'Team');
        return $this->_getResponseSuccess([
            'team' => $team
        ]);
    }

    /**
     * Get information for displaying invitation confirmation page.
     * ※ Call this api only if team's plan is paid plan
     *
     * @queryParam int invitation_count required
     */
    function get_confirm()
    {
        /** @var InvitationService $InvitationService */
        $InvitationService = ClassRegistry::init("InvitationService");
        /** @var PaymentService $PaymentService */
        $PaymentService = ClassRegistry::init("PaymentService");
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init("TeamMember");
        /** @var CampaignService $CampaignService */
        $CampaignService = ClassRegistry::init('CampaignService');

        // Check permission
        if (!$TeamMember->isAdmin($this->Auth->user('id'))) {
            return $this->_getResponseForbidden();
        }

        $invitationCnt = $this->request->query("invitation_count");

        /* These errors are invalid request */
        if (!AppUtil::isInt($invitationCnt)) {
            return $this->_getResponseBadFail(__("Parameter is invalid"));
        }
        $invitationCnt = (int)$invitationCnt;
        if ($invitationCnt <= 0) {
            return $this->_getResponseBadFail(__("Parameter is invalid"));
        }

        // Get payment setting by team id
        $teamId = $this->current_team_id;
        $paySetting = $PaymentService->get($teamId);
        // Check if exist payment setting
        if (empty($paySetting)) {
            return $this->_getResponseSuccess();
        }

        // Calc charge user count
        $chargeUserCnt = $PaymentService->calcChargeUserCount($teamId, $invitationCnt);

        // Charges not applicable to campaign users or count 0
        $isCampaign = $CampaignService->purchased($teamId);
        if ($isCampaign && $CampaignService->willExceedMaximumCampaignAllowedUser($teamId, $invitationCnt)) {
            $res = [
                'exceedMaximumUsers' => true,
            ];
        } else if ($chargeUserCnt == 0 || $isCampaign) {
            $res = [
                'charge_users_count' => 0,
            ];
        } else {
            $amountPerUser = $PaymentService->formatCharge($paySetting['amount_per_user'], $paySetting['currency']);
            // Get use days from today to next paymant base date
            $useDaysByNext = $PaymentService->getUseDaysByNextBaseDate($teamId);
            // All days between before payment base date and next payment base date
            $allUseDays = $PaymentService->getCurrentAllUseDays($teamId);
            // Calc total charge
            $currency = new Enum\Model\PaymentSetting\Currency((int)$paySetting['currency']);
            $totalCharge = $PaymentService->formatTotalChargeByAddUsers($teamId, $chargeUserCnt, $currency,
                $useDaysByNext, $allUseDays);

            $res = [
                'amount_per_user'            => $amountPerUser,
                'charge_users_count'         => $chargeUserCnt,
                'use_days_by_next_base_date' => $useDaysByNext,
                'all_use_days'               => $allUseDays,
                'total_charge'               => $totalCharge,
            ];
        }

        return $this->_getResponseSuccess($res);
    }

    /**
     *
     */
    function post()
    {
        /** @var InvitationService $InvitationService */
        $InvitationService = ClassRegistry::init("InvitationService");
        /** @var Invite $Invite */
        $Invite = ClassRegistry::init("Invite");
        /** @var PaymentService $PaymentService */
        $PaymentService = ClassRegistry::init("PaymentService");
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init("TeamMember");
        /** @var Team $Team */
        $Team = ClassRegistry::init("Team");
        /** @var CampaignService $CampaignService */
        $CampaignService = ClassRegistry::init('CampaignService');

        // Check permission
        $userId = $this->Auth->user('id');
        if (!$TeamMember->isAdmin($userId)) {
            return $this->_getResponseForbidden();
        }

        $teamId = $this->current_team_id;

        // Validation
        $emails = $this->request->data("emails");
        $countInvitedPeople = count($emails);
        $errors = $InvitationService->validateEmails($teamId, $emails);
        if (!empty($errors)) {
            return $this->_getResponseValidationFail($errors);
        }

        // Validation campaign plan need to be exceed
        $isCampaign = $CampaignService->purchased($teamId);
        if ($isCampaign && $CampaignService->willExceedMaximumCampaignAllowedUser($teamId, $countInvitedPeople)) {
            // should not come in here
            // check campaign need to exceed using confirm API
            // before call this API
            // /api/v1/invitations/confirm?invitation_count=*
            CakeLog::notice(sprintf('campaign plan need to exceed: %s', AppUtil::jsonOneLine([
                'teams.id'     => $teamId,
                'count_invite' => $countInvitedPeople,
            ])));
            return $this->_getResponseForbidden([]);
        }

        // Invite
        $resInvite = $InvitationService->invite($teamId, $userId, $emails);
        if ($resInvite['error']) {
            // TODO.payment: switch message when exists problem card status
            return $this->_getResponseBadFail($resInvite['msg']);
        }

        // Send invitation mail
        $invitations = $Invite->findByEmails($emails);
        $team = $Team->getCurrentTeam();
        foreach ($invitations as $invitation) {
            $this->GlEmail->sendMailInvite($invitation, Hash::get($team, 'Team.name'));
        }

        $this->Notification->outSuccess(__("Invited %s people.", $countInvitedPeople));

        CakeLog::info(sprintf('invited people: %s', AppUtil::jsonOneLine([
            'teams.id'     => $teamId,
            'count_invite' => $countInvitedPeople,
        ])));
        PaymentUtil::logCurrentTeamChargeUsers($teamId);
        return $this->_getResponseSuccess();
    }

    /**
     * re-inviting user
     * this API takes id of user_id
     *
     * @return CakeResponse
     */
    public function post_reInvite()
    {
        /** @var Invite $Invite */
        $Invite = ClassRegistry::init('Invite');
        /** @var InvitationService $InvitationService */
        $InvitationService = ClassRegistry::init('InvitationService');
        /** @var Team $Team */
        $Team = ClassRegistry::init('Team');
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init('TeamMember');
        /** @var User $User */
        $User = ClassRegistry::init('User');
        /** @var Email $Email */
        $Email = ClassRegistry::init('Email');

        $userId = $this->request->data('user_id');
        $requestedEmail = $this->request->data('email') ?? '';
        if (!AppUtil::isInt($userId)) {
            return $this->_getResponseBadFail(__('Param is incorrect'));
        }

        $extractedEmailValidationErrors = $InvitationService->validateEmail($requestedEmail);
        if (!empty($extractedEmailValidationErrors)) {
            return $this->_getResponseValidationFail($extractedEmailValidationErrors);
        }

        $inviteData = $Invite->getUnverifiedWithEmailByUserId($userId, $this->current_team_id);
        if (empty($inviteData)) {
            return $this->_getResponseNotFound();
        }
        if (empty($User->getById($userId))) {
            return $this->_getResponseNotFound();
        }
        if (empty($TeamMember->getWithTeam($this->current_team_id, $userId))) {
            return $this->_getResponseNotFound();
        }

        // if already joined, throw error, already exists
        if ($inviteData['Invite']['email_verified']) {
            return $this->_getResponseBadFail(__("Error, this user already exists."));
        }

        if (!$InvitationService->reInvite($inviteData['Invite'], $inviteData['Email'], $requestedEmail)) {
            $this->Notification->outError(__('Error, failed to invite.'));
            return $this->_getResponseInternalServerError(__('Error, failed to invite'));
        }

        // Send invitation mail
        $invitations = $Invite->findByEmails([$requestedEmail]);
        $team = $Team->getCurrentTeam();
        foreach ($invitations as $invitation) {
            $this->GlEmail->sendMailInvite($invitation, Hash::get($team, 'Team.name'));
        }

        $messageSuccess = __("Invited %s people.", 1);
        $this->Notification->outSuccess($messageSuccess);

        return $this->_getResponseSuccess([
            'message' => $messageSuccess,
        ]);
    }

    /**
     * Revoke user invitation.
     * This API takes email of target user.
     *
     * @return CakeResponse
     */
    public function post_revokeInvitation()
    {

        /** @var InvitationService $InvitationService */
        $InvitationService = ClassRegistry::init('InvitationService');
        /** @var TeamMember $TeamMember */
        $TeamMemberService = ClassRegistry::init('TeamMemberService');

        // request parameter
        $email  = $this->request->data('email');

        // validate
        if (empty($email)) {
            return $this->_getResponseBadFail(__('Param is invalid'));
        }

        try {
            //update Invites table
            $InvitationService->revokeInvitation($this->current_team_id, $email);

            //update TeamMembers table
            $TeamMemberService->updateDelFlgToRevoke($this->current_team_id, $email);
        }
        catch(RuntimeException $e) {
            return $this->_getResponseBadFail('Error, ' . $e->getMessage());
        }

        $messageSuccess = __("Revoked %s people.", 1);
        $this->Notification->outSuccess($messageSuccess);

        return $this->_getResponseSuccess([
            'message' => $messageSuccess,
        ]);

    }

}
