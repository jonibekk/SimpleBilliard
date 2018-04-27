<?php
require('BaseValidator.php');
require('CommonValidator.php');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/04/18
 * Time: 9:57
 */

use Respect\Validation\Validator as validator;

class UserValidator extends BaseValidator
{
    public function getDefaultValidationRule(): array
    {
        $defaultRule = [
            'team_id'            => [validator::intType(), "optional"],
            'first_name'         => [CommonValidator::nameValidation()],
            'last_name'          => [CommonValidator::nameValidation()],
            'gender_type'        => [validator::intVal()->between(1, 2), "optional"],
            'birth_day'          => [CommonValidator::dateValidation(), "optional"],
            'hide_year_flg'      => [validator::boolType(), "optional"],
            'admin_flg'          => [validator::boolType()],
            'no_pass_flg'        => [validator::boolType()],
            'active_flg'         => [validator::boolType()],
            'auto_timezone_flg'  => [validator::boolType(), "optional"],
            'auto_language_flg'  => [validator::boolType(), "optional"],
            'romanize_flg'       => [validator::boolType(), "optional"],
            'update_email_flg'   => [validator::boolType(), "optional"],
            'language'           => [validator::stringType(), "optional"],
            'timezone'           => [validator::numeric(), "optional"],
            'default_team_id'    => [validator::intType()],
            'del_flg'            => [validator::boolType()],
            'old_password'       => [validator::length(8, null), "optional"],
            'hometown'           => [validator::stringType()->length(null, 128), "optional"],
            'comment'            => [validator::length(null, 2000)],
            'phone_no'           => [validator::length(null, 20)],
            'setup_complete_flg' => [validator::boolType(), "optional"],
        ];

        return $defaultRule;
    }

    public function getPasswordValidationRule(): array
    {
        $passwordRule = [
            "password_request"  => [CommonValidator::passwordValidation()],
            "password_request2" => [CommonValidator::passwordValidation()],
            "password"          => [CommonValidator::passwordValidation()],
        ];

        return $passwordRule;
    }

}