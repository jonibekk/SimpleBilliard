<?php

require('BaseValidator.php');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/04/18
 * Time: 9:57
 */

use Respect\Validation\Validator as validator;

class UserValidator extends BaseValidator
{
    private $teamIdValidation = null;

    private $firstNameValidation = null;

    private $lastNameValidation = null;

    private $genderTypeValidation = null;

    private $birthDayValidation = null;

    private $hideYearFlagValidation = null;

    private $adminFlagValidation = null;

    private $noPassFlagValidation = null;

    private $activeFlagValidation = null;

    private $autoTimezoneFlagValidation = null;

    private $autoLanguageFlagValidation = null;

    private $romanizeFlagValidation = null;

    private $updateEmailFlagValidation = null;

    private $languageValidation = null;

    private $timezoneValidation = null;

    private $defaultTeamIdValidation = null;

    private $agreeTosValidation = null;

    private $delFlagValidation = null;

    private $oldPasswordValidation = null;

    private $photoValidation = null;

    private $hometownValidation = null;

    private $coverPhotoValidation = null;

    private $commentValidation = null;

    private $phoneNumberValidation = null;

    private $setupCompleteFlagValidation = null;

    private $passwordRequest1Validation = null;

    private $passwordRequest2Validation = null;

    private $passwordValidation = null;

    private $passwordConfirmValidation = null;

    public function __construct()
    {
        parent::__construct();

        $this->teamIdValidation = $this->createValidationForArray('team_id', $this->teamIdBaseValidation);

        $this->firstNameValidation = $this->createValidationForArray('first_name', $this->nameBaseValidation);

        $this->lastNameValidation = $this->createValidationForArray('last_name', $this->nameBaseValidation);

        $this->genderTypeValidation = $this->createValidationForArray('gender_type',
            validator::intVal()->between(1, 2), false);

        $this->birthDayValidation = $this->createValidationForArray('birth_day',
            validator::date('Y-m-d'), false);

        $this->hideYearFlagValidation = $this->createValidationForArray('hide_year_flg',
            validator::boolVal(), false);

        $this->adminFlagValidation = $this->createValidationForArray('admin_flg', validator::notEmpty()->boolVal());

        $this->noPassFlagValidation = $this->createValidationForArray('no_pass_flg', validator::notEmpty()->boolVal());

        $this->activeFlagValidation = $this->createValidationForArray('active_flg', validator::notEmpty()->boolVal());

        $this->autoTimezoneFlagValidation = $this->createValidationForArray('auto_timezone_flg',
            validator::boolVal(), false);

        $this->autoLanguageFlagValidation = $this->createValidationForArray('auto_language_flg',
            validator::boolVal(), false);

        $this->romanizeFlagValidation = $this->createValidationForArray('romanize_flg',
            validator::boolVal(), false);

        $this->updateEmailFlagValidation = $this->createValidationForArray('update_email_flg',
            validator::boolVal(), false);

        $this->languageValidation = $this->createValidationForArray('language',
            validator::stringType(), false);

        $this->timezoneValidation = $this->createValidationForArray('timezone',
            validator::numeric(), false);

        $this->defaultTeamIdValidation = $this->createValidationForArray('default_team_id', $this->teamIdBaseValidation);

        //TODO
        $this->agreeTosValidation = validator::alwaysValid();

        $this->delFlagValidation = $this->createValidationForArray('del_flg', validator::boolVal());

        $this->oldPasswordValidation = $this->createValidationForArray('old_password',
            validator::notEmpty()->length(8, null), false);

        //TODO
        $this->photoValidation = null;

        $this->hometownValidation = $this->createValidationForArray('hometown',
            validator::stringType()->length(null, 128), false);

        //TODO
        $this->coverPhotoValidation = null;

        $this->commentValidation = $this->createValidationForArray('comment', validator::notEmpty()->length(null, 2000));

        $this->phoneNumberValidation = $this->createValidationForArray('phone_no', validator::notEmpty()->length(null, 20));

        $this->setupCompleteFlagValidation = $this->createValidationForArray('setup_complete_flg',
            validator::boolVal(), false);

        $this->passwordRequest1Validation = $this->createValidationForArray('password_request',
            $this->_getPasswordValidation());

        $this->passwordRequest2Validation = $this->createValidationForArray('password_request2',
            $this->_getPasswordValidation());

        $this->passwordValidation = $this->createValidationForArray('password', $this->_getPasswordValidation());

        $this->passwordConfirmValidation = $this->createValidationForArray('password_confirm', validator::alwaysValid());
    }

    /**
     * @return validator Password validation
     */
    private function _getPasswordValidation()
    {
        return validator::notEmpty()
                        ->regex('/^(?=.*[0-9])(?=.*[a-zA-Z])[0-9a-zA-Z\!\@\#\$\%\^\&\*\(\)\_\-\+\=\{\}\[\]\|\:\;\<\>\,\.\?\/]{0,}$/i')
                        ->length(8, 50);
    }

    /**
     * @param array $array
     *
     * @return array|bool
     */
    public function validate($array)
    {
        try {
            $ret = validator::AllOf([
                $this->teamIdValidation, $this->firstNameValidation,
                $this->lastNameValidation, $this->genderTypeValidation,
                $this->birthDayValidation, $this->hideYearFlagValidation,
                $this->adminFlagValidation, $this->noPassFlagValidation,
                $this->activeFlagValidation, $this->autoTimezoneFlagValidation,
                $this->autoLanguageFlagValidation, $this->romanizeFlagValidation,
                $this->updateEmailFlagValidation, $this->languageValidation,
                $this->timezoneValidation, $this->defaultTeamIdValidation,
                $this->agreeTosValidation, $this->delFlagValidation,
                $this->oldPasswordValidation, $this->hometownValidation,
//                $this->photoValidation, $this->coverPhotoValidation,
                $this->commentValidation, $this->phoneNumberValidation,
                $this->setupCompleteFlagValidation])->assert($array);
        } catch (\Respect\Validation\Exceptions\NestedValidationException $exception) {
            return $exception->getMessages();
        }

        return true;
    }

    public function validatePassword(array $array)
    {
        try {
            $ret = validator::AllOf(
                $this->passwordRequest1Validation, $this->passwordRequest2Validation,
                $this->passwordValidation, $this->passwordConfirmValidation
            )->assert($array);
        } catch (\Respect\Validation\Exceptions\NestedValidationException $exception) {
            return $exception->getFullMessage();
        }

        return $ret ?? false;
    }

    private function _getAgreeTosValidation()
    {

    }

    private function _getPasswordConfirmValidation()
    {
    }

}