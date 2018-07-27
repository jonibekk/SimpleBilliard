<?php
App::uses('GoalousTestCase', 'Test');
App::uses('User', 'Model');
App::uses('UserValidator', 'Validator/Model');
App::uses('AuthRequestValidator', 'Validator/Request/Api/V2');

use Respect\Validation\Validator as validator;

class RespectTranslationTest extends GoalousTestCase
{
    public function test_validatePost_success()
    {
        $authRequestValidator = AuthRequestValidator::createLoginValidator();
        Configure::write('Config.language', 'jpn');

        try {
            $this->assertTrue($authRequestValidator->validate([
                'email' => "not email format",
                'password' => ""
            ]));
        } catch (\Respect\Validation\Exceptions\AllOfException $e) {
            // TODO: assert translated
            // ↓ this is translated currently (2018-07-27)
            var_dump($e->getFullMessage());
        }
    }
}
