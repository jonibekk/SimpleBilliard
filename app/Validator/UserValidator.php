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

    private $birthDateValidation = null;

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

        $this->teamIdValidation = $this->_createValidation('team_id', $this->teamIdBaseValidation);

        $this->firstNameValidation = $this->_createValidation('first_name', $this->nameBaseValidation);

        $this->lastNameValidation = $this->_createValidation('last_name', $this->nameBaseValidation);

        $this->genderTypeValidation = $this->_createValidation('gender_type',
            validator::when(validator::notEmpty(), validator::stringType()));

        $this->birthDateValidation = $this->_createValidation('birth_day',
            validator::when(validator::notEmpty(), validator::date('Y-m-d')));

        $this->hideYearFlagValidation = $this->_createValidation('hide_year_flg',
            validator::when(validator::notEmpty(), validator::boolType()));

        $this->adminFlagValidation = $this->_createValidation('admin_flg', validator::notEmpty()->boolType());

        $this->noPassFlagValidation = $this->_createValidation('no_pass_flg', validator::notEmpty()->boolType());

        $this->activeFlagValidation = $this->_createValidation('active_flg', validator::notEmpty()->boolType());

        $this->autoTimezoneFlagValidation = $this->_createValidation('auto_timezone_flg',
            validator::when(validator::notEmpty(), validator::boolType()));

        $this->autoLanguageFlagValidation = $this->_createValidation('auto_language_flg',
            validator::when(validator::notEmpty(), validator::boolType()));

        $this->romanizeFlagValidation = $this->_createValidation('romanize_flg',
            validator::when(validator::notEmpty(), validator::boolType()));

        $this->updateEmailFlagValidation = $this->_createValidation('update_email_flg',
            validator::when(validator::notEmpty(), validator::boolType()));

        $this->languageValidation = $this->_createValidation('language',
            validator::when(validator::notEmpty(), validator::stringType()));

        $this->timezoneValidation = $this->_createValidation('timezone',
            validator::when(validator::notEmpty(), validator::intType()));

        $this->defaultTeamIdValidation = $this->_createValidation('default_team_id', $this->teamIdBaseValidation);

        //TODO
        $this->agreeTosValidation = validator::alwaysValid();

        $this->delFlagValidation = $this->_createValidation('del_flg', validator::notEmpty()->boolType());

        $this->oldPasswordValidation = $this->_createValidation('old_password', validator::notEmpty()->length(8, null));

        //TODO
        $this->photoValidation = null;

        $this->hometownValidation = $this->_createValidation('hometown', validator::when(validator::notEmpty(),
            validator::stringType()->length(null, 128)));

        //TODO
        $this->coverPhotoValidation = null;

        $this->commentValidation = $this->_createValidation('comment', validator::notEmpty()->length(null, 2000));

        $this->phoneNumberValidation = $this->_createValidation('phone_no', validator::notEmpty()->length(null, 20));

        $this->setupCompleteFlagValidation = $this->_createValidation('setup_complete_flg',
            validator::when(validator::notEmpty(), validator::boolType()));

        $this->passwordRequest1Validation = $this->_createValidation('password_request',
            $this->_getPasswordValidation());

        $this->passwordRequest2Validation = $this->_createValidation('password_request2',
            $this->_getPasswordValidation());

        $this->passwordValidation = $this->_createValidation('password', $this->_getPasswordValidation());

        $this->passwordConfirmValidation = $this->_createValidation('password_confirm', validator::alwaysValid());
    }

    private function _getPasswordValidation()
    {
        return validator::notEmpty()
                        ->regex('/^(?=.*[0-9])(?=.*[a-zA-Z])[0-9a-zA-Z\!\@\#\$\%\^\&\*\(\)\_\-\+\=\{\}\[\]\|\:\;\<\>\,\.\?\/]{0,}$/i')
                        ->length(8, 50);
    }

    public function validateModel(Model $userModel): bool
    {
        try {
            $ret = validator::AllOf(
                $this->teamIdValidation, $this->firstNameValidation,
                $this->lastNameValidation, $this->genderTypeValidation,
                $this->birthDateValidation, $this->hideYearFlagValidation,
                $this->adminFlagValidation, $this->noPassFlagValidation,
                $this->activeFlagValidation, $this->autoTimezoneFlagValidation,
                $this->autoLanguageFlagValidation, $this->romanizeFlagValidation,
                $this->updateEmailFlagValidation, $this->languageValidation,
                $this->timezoneValidation, $this->defaultTeamIdValidation,
                $this->agreeTosValidation, $this->delFlagValidation,
                $this->oldPasswordValidation, $this->hometownValidation,
//                $this->photoValidation, $this->coverPhotoValidation,
                $this->commentValidation, $this->phoneNumberValidation,
                $this->setupCompleteFlagValidation)->assert($userModel);
        } catch (\Respect\Validation\Exceptions\NestedValidationException $exception) {
            return $exception->getFullMessage();
        }

        return $ret ?? false;
    }

    public function validatePassword(Model $userModel): bool
    {
        try {
            $ret = validator::AllOf(
                $this->passwordRequest1Validation, $this->passwordRequest2Validation,
                $this->passwordValidation, $this->passwordConfirmValidation
            )->assert($userModel);
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