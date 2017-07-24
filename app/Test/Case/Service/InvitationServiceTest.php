<?php
App::uses('GoalousTestCase', 'Test');
App::import('Service', 'InvitationService');

/**
 * InvitationServiceTest Class
 *
 * @property InvitationService $InvitationService
 */
class InvitationServiceTest extends GoalousTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.email',
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->InvitationService = ClassRegistry::init('InvitationService');
    }

    /**
     * Validate emails
     * check empty
     */
    function test_validateEmails_checkEmpty()
    {
        $emails = [];
        $errors = $this->InvitationService->validateEmails($emails);
        $this->assertEquals($errors[0], __("Input is required"));

        $emails = ['', ''];
        $errors = $this->InvitationService->validateEmails($emails);
        $this->assertEquals($errors[0], __("Input is required"));
    }

    /**
     * Validate emails
     * format
     */
    function test_validateEmails_format()
    {
        $emails = ['a'];
        $errors = $this->InvitationService->validateEmails($emails);
        $this->assertEquals($errors[0], __("Line %d", 1) . "：" . __("Email address is incorrect."));

        $emails = ['a@f'];
        $errors = $this->InvitationService->validateEmails($emails);
        $this->assertEquals($errors[0], __("Line %d", 1) . "：" . __("Email address is incorrect."));

        $emails = ['test@example.com'];
        $errors = $this->InvitationService->validateEmails($emails);
        $this->assertEmpty($errors);

        $emails = ['a', 'b'];
        $errors = $this->InvitationService->validateEmails($emails);
        $this->assertEquals($errors[0], __("Line %d", 1) . "：" . __("Email address is incorrect."));
        $this->assertEquals($errors[1], __("Line %d", 2) . "：" . __("Email address is incorrect."));

        $emails = ['', 'a', '', 'b', '', 'test@example.com'];
        $errors = $this->InvitationService->validateEmails($emails);
        $this->assertEquals(count($errors), 2);
        $this->assertEquals($errors[0], __("Line %d", 2) . "：" . __("Email address is incorrect."));
        $this->assertEquals($errors[1], __("Line %d", 4) . "：" . __("Email address is incorrect."));
    }

    /**
     * Validate emails
     * Check max invitation count
     */
    function test_validateEmails_maxInvitationCount()
    {
        $emails = [];
        for ($i = 1; $i <= 100; $i++) {
            $emails[] = sprintf("test%d@example.com", $i);
        }
        $errors = $this->InvitationService->validateEmails($emails);
        $this->assertEmpty($errors);

        $emails[] = "test101@example.com";
        $errors = $this->InvitationService->validateEmails($emails);
        $this->assertEquals($errors[0],
            __("%s invitations are the limits in one time.", InvitationService::MAX_INVITATION_CNT));
    }
    /**
     * Validate emails
     * Check duplicates
     */
    function test_validateEmails_duplicate()
    {
        $duplicateErrMsg = "：" . __("%s is duplicated.", __("Email address"));
        $emails = array_fill(0, 2, 'test@example.com');
        $errors = $this->InvitationService->validateEmails($emails);
        $this->assertEquals($errors[0],
            __("Line %d", 2) . $duplicateErrMsg);

        $emails = array_fill(0, 3, 'test@example.com');
        $errors = $this->InvitationService->validateEmails($emails);
        $this->assertEquals($errors[0],
            __("Line %d", 2) . $duplicateErrMsg);
        $this->assertEquals($errors[1],
            __("Line %d", 3) . $duplicateErrMsg);
    }

}
