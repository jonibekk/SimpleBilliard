<?php
App::import('Service', 'AppService');
App::uses('Email', 'Model');

/**
 * Class InvitationService
 */
class InvitationService extends AppService
{
    const MAX_INVITATION_CNT = 100;

    /**
     * Validate emails
     *
     * @param array $emails
     *
     * @return null
     */
    function validateEmails(array $emails)
    {
        /** @var Email $Email */
        $Email = ClassRegistry::init("Email");

        /* Check empty */
        if (empty($emails) || empty(array_filter($emails))) {
            return [__("Input is required")];
        }
        /* Format validation */
        $errors = [];
        foreach ($emails as $i => $email) {
            if (empty($email)) {
                continue;
            }
            $Email->set(['email' => $email]);
            if (!$Email->validates(['fieldList' => ['email']])) {
                $errors[] = __("Line %d", $i + 1) . "：" . Hash::get($Email->validationErrors, 'email.0');
            }
        }
        if (!empty($errors)) {
            return $errors;
        }

        /* Check invitations limit  */
        if (count($emails) > self::MAX_INVITATION_CNT) {
            return [__("%s invitations are the limits in one time.", self::MAX_INVITATION_CNT)];
        }

        $duplicateEmails = array_diff_key($emails, array_unique($emails));
        foreach ($duplicateEmails as $i => $duplicateEmail) {
            $errors[] = __("Line %d", $i + 1) . "：" . __("%s is duplicated.", __("Email address"));
        }

        return $errors;
    }
}
