<?php
require('BaseValidator.php');
require('CommonValidator.php');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/04/26
 * Time: 14:07
 */

use Respect\Validation\Validator as validator;

class CommonValidator
{

    /**
     * @return Respect\Validation\Validator
     */
    public final static function nameValidation()
    {
        return validator::alnum('\'')->length(null, 128);
    }

    /**
     * @return Respect\Validation\Validator
     */
    public final static function passwordValidation()
    {
        return validator::notEmpty()
                        ->regex('/^(?=.*[0-9])(?=.*[a-zA-Z])[0-9a-zA-Z\!\@\#\$\%\^\&\*\(\)\_\-\+\=\{\}\[\]\|\:\;\<\>\,\.\?\/]{0,}$/i')
                        ->length(8, 50);
    }

    /**
     * @return Respect\Validation\Validator
     */
    public final static function dateValidation()
    {
        return validator::date('Y-m-d');
    }

}