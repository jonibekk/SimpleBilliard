<?php
App::uses('ApiController', 'Controller/Api');
App::uses('AppUtil', 'Util');
App::import('Service', 'InvitationService');
App::import('Service', 'PaymentService');

/**
 * Class InvitationsController
 */
class InvitationsController extends ApiController
{
    public $components = [
        'Notification',
    ];

    /**
     * Validation
     *
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
        $errors = $InvitationService->validateEmails($emails);
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
        $paymentSetting = $PaymentService->get($this->current_team_id);
        // Check if exist payment setting
        if (empty($paymentSetting)) {
            return $this->_getResponseSuccess();
        }

        // TODO: Unify naming charge or amount or billing
        $amountPerUser = $PaymentService->formatCharge($paymentSetting['amount_per_user']);
        // Calc charge user count
        $chargeUserCnt = $InvitationService->calcChargeUserCount($invitationCnt);
        // Get use days from today to next paymant base date
        $useDaysByNext = $PaymentService->getUseDaysByNextBaseDate();
        // All days between before payment base date and next payment base date
        $allUseDays = $PaymentService->getCurrentAllUseDays();
        // Calc total charge
        $totalCharge = $PaymentService->formatTotalChargeByAddUsers($chargeUserCnt, REQUEST_TIMESTAMP,  $useDaysByNext, $allUseDays);

        $res = [
            'amount_per_user' => $amountPerUser,
            'charge_users_count' => $chargeUserCnt,
            'use_days_by_next_base_date' => $useDaysByNext,
            'all_use_days' => $allUseDays,
            'total_charge' => $totalCharge,
        ];

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

        // Check permission
        $userId = $this->Auth->user('id');
        if (!$TeamMember->isAdmin($userId)) {
            return $this->_getResponseForbidden();
        }

        // Validation
        $emails = $this->request->data("emails");
        $errors = $InvitationService->validateEmails($emails);
        if (!empty($errors)) {
            return $this->_getResponseValidationFail($errors);
        }

        // Check permission
        if (!$TeamMember->isAdmin($userId)) {
            return $this->_getResponseForbidden();
        }

        // Invite
        if (!$InvitationService->invite($this->current_team_id, $userId, $emails)) {
            return $this->_getResponseInternalServerError();
        }

        // Send invitation mail
        $invitations = $Invite->findByEmails($emails);
        $team = $Team->getCurrentTeam();
        foreach ($invitations as $invitation) {
            $this->GlEmail->sendMailInvite($invitation, Hash::get($team, 'Team.name'));
        }

        $this->Notification->outSuccess(__("Invited %s people.", count($emails)));
        return $this->_getResponseSuccess();
    }
}
