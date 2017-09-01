<?php
App::uses('ApiController', 'Controller/Api');
App::uses('AppUtil', 'Util');
App::import('Service', 'InvitationService');
App::import('Service', 'PaymentService');

use Goalous\Model\Enum as Enum;

/**
 * Class InvitationsController
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
        $paySetting = $PaymentService->get($this->current_team_id);
        // Check if exist payment setting
        if (empty($paySetting)) {
            return $this->_getResponseSuccess();
        }

        $amountPerUser = $PaymentService->formatCharge($paySetting['amount_per_user'], $paySetting['currency']);
        // Calc charge user count
        $chargeUserCnt = $PaymentService->calcChargeUserCount($this->current_team_id, $invitationCnt);
        // Get use days from today to next paymant base date
        $useDaysByNext = $PaymentService->getUseDaysByNextBaseDate();
        // All days between before payment base date and next payment base date
        $allUseDays = $PaymentService->getCurrentAllUseDays();
        // Calc total charge
        $currency = new Enum\PaymentSetting\Currency((int)$paySetting['currency']);
        $totalCharge = $PaymentService->formatTotalChargeByAddUsers($chargeUserCnt, $currency, REQUEST_TIMESTAMP,  $useDaysByNext, $allUseDays);

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
        $errors = $InvitationService->validateEmails($this->current_team_id, $emails);
        if (!empty($errors)) {
            return $this->_getResponseValidationFail($errors);
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

    /**
     * re-inviting user
     *
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

        $userId         = $this->request->data('user_id');
        $requestedEmail = $this->request->data('email');
        if (intval($userId) <= 0) {
            return $this->_getResponseBadFail('Invalid user_id');
        }
        $validateErrors = $this->validateEmail($requestedEmail);
        if (!empty($validateErrors)) {
            return $this->_getResponseBadFail($validateErrors['email'][0]);
        }

        $inviteData = $Invite->getUnverifiedWithEmailByUserId($userId, $this->current_team_id);
        if ($Email->isVerified($requestedEmail)) {
            return $this->_getResponseBadFail("Error, this user already exists.");
        }
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
            return $this->_getResponseBadFail("Error, this user already exists.");
        }

        if (!$InvitationService->reInvite($inviteData['Invite'], $inviteData['Email'], $requestedEmail)) {
            return $this->_getResponseInternalServerError('Error, failed to invite');
        }

        // Send invitation mail
        $invitations = $Invite->findByEmails([$requestedEmail]);
        $team = $Team->getCurrentTeam();
        foreach ($invitations as $invitation) {
            $this->GlEmail->sendMailInvite($invitation, Hash::get($team, 'Team.name'));
        }

        return $this->_getResponseSuccess([
            'message' => 'Invite succeed'
        ]);
    }

    /**
     * return if string $email is valid email format
     * @param string $email
     *
     * @return array
     */
    private function validateEmail(string $email): array
    {
        /** @var Email $Email */
        $Email = ClassRegistry::init("Email");
        $Email->validate = [
            'maxLength'     => ['rule' => ['maxLength', 255]],
            'notBlank'      => ['rule' => 'notBlank',],
            'email'         => ['rule' => ['email'],],
        ];
        $Email->set(['email' => $email]);
        $Email->validates();
        return $Email->validationErrors;
    }
}
