<?php
App::uses('GoalousTestCase', 'Test');
App::uses('User', 'Model');
App::uses('UserValidator', 'Validator');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/04/27
 * Time: 10:35
 */

use Respect\Validation\Validator as validator;

class UserValidatorTest extends GoalousTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.local_name',
        'app.user',
        'app.team',
        'app.team_member',
        'app.email',
        'app.post',
        'app.notify_setting',
        'app.member_group',
        'app.device',
        'app.term',
        'app.goal',
        'app.action_result',
        'app.post_share_circle',
        'app.job_category',
        'app.member_type',
        'app.terms_of_service'
    );

    public function test_DefaultValidation_success()
    {
        /** @var User $user */
        $user = ClassRegistry::init('User');

        $sampleUser = $user->getById(1);
        $sampleUser['team_id'] = 1;
        $sampleUser['default_team_id'] = 1;
        $sampleUser['phone_no'] = '01234567890';

        $userValidator = new UserValidator;

        try {
            $this->assertTrue($userValidator->validateWithDefaultRules($sampleUser));
        } catch (\Respect\Validation\Exceptions\NestedValidationException $exception) {
        }

    }

    public function test_DefaultValidation_failure()
    {
        /** @var User $user */
        $user = ClassRegistry::init('User');

        $sampleUser = $user->getById(1);

        $sampleUser['team_id'] = 1;
        $sampleUser['default_team_id'] = 1;
        $sampleUser['phone_no'] = '01234567890';
        $sampleUser['first_name'] = '129yrb8y))*&)@&$)';

        $userValidator = new UserValidator;

        try {
            $this->assertFalse($userValidator->validateWithDefaultRules($sampleUser));
        } catch (\Respect\Validation\Exceptions\NestedValidationException $exception) {
        }
    }

    public function test_ModifyValidation_success()
    {
        /** @var User $user */
        $user = ClassRegistry::init('User');

        $sampleUser = $user->getById(1);

        $sampleUser['team_id'] = 1;
        $sampleUser['default_team_id'] = 1;
        $sampleUser['phone_no'] = '01234567890';
        $sampleUser['test_1'] = 'lalalalalalalalololol';
        $sampleUser['test_2'] = 3.14;

        try {
            $newRule = [
                "test_1" => [validator::stringType()->length(5, null)],
                "test_2" => [validator::numeric()]
            ];

            $userValidator = new UserValidator;

            $this->assertTrue($userValidator->validate($sampleUser, $newRule));

            $sampleUser['test_1'] = 'a';

            $newRule = [
                "test_1" => [validator::stringType()],
                "test_2" => [validator::numeric()]
            ];

            $this->assertTrue($userValidator->validate($sampleUser, $newRule));

            unset($sampleUser['test_2']);

            $newRule = [
                "test_1" => [validator::stringType()],
                "test_2" => [validator::numeric(), "optional"]
            ];

            $this->assertTrue($userValidator->validate($sampleUser, $newRule));

        } catch (\Respect\Validation\Exceptions\NestedValidationException $exception) {
        }
    }

    public function test_ModifyValidation_failure()
    {
        /** @var User $user */
        $user = ClassRegistry::init('User');

        $sampleUser = $user->getById(1);

        $sampleUser['team_id'] = 1;
        $sampleUser['default_team_id'] = 1;
        $sampleUser['phone_no'] = '01234567890';
        $sampleUser['test_1'] = '';
        $sampleUser['test_2'] = 3.14;

        try {
            $newRule = [
                "test_1" => [validator::stringType()->length(5, null)],
                "test_2" => [validator::numeric()]
            ];

            $userValidator = new UserValidator;

            $this->assertFalse($userValidator->validate($sampleUser, $newRule));

            unset($sampleUser['test_1']);

            $this->assertFalse($userValidator->validate($sampleUser, $newRule));

        } catch (\Respect\Validation\Exceptions\NestedValidationException $exception) {
        }
    }
}