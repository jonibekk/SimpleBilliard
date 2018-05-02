<?php
/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/05/02
 * Time: 14:34
 */

namespace Validator\Exception;

use Respect\Validation\Exceptions\ValidationException;

class UniqueException extends ValidationException
{
    public static $defaultTemplates = [
        self::MODE_DEFAULT  => [
            self::STANDARD => '{{name}} contains duplicate entry',
        ],
        self::MODE_NEGATIVE => [
            self::STANDARD => '{{name}} does not contain duplicate entry',
        ],
    ];
}