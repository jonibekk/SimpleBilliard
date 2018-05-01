<?php
/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/05/01
 * Time: 11:13
 */

namespace Validator\CustomRule;

use Respect\Validation\Exceptions\ComponentException;
use Respect\Validation\Rules\AbstractRule;

class Unique extends AbstractRule
{
    public function validate($input)
    {
        if (!is_array($input)) {
            throw new ComponentException('Input must be an array');
        }

        $count = array();

        foreach ($input as $value) {

            if (isset($count[$value])) {
                return false;
            } else {
                $count[$value] = 1;
            }
        }

        return true;
    }

}