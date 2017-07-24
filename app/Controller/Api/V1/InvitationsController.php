<?php
App::uses('ApiController', 'Controller/Api');
App::uses('AppUtil', 'Util');
App::import('Service', 'InvitationService');

/**
 * Class InvitationsController
 */
class InvitationsController extends ApiController
{
    /**
     * Validation
     */
    function post_validate()
    {
        /** @var InvitationService $InvitationService */
        $InvitationService = ClassRegistry::init("InvitationService");

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
        return $this->_getResponseSuccess();
    }
}
