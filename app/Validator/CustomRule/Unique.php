<?php
/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/05/01
 * Time: 11:13
 */

namespace Custom\Validation\Rule;

use Respect\Validation\Exceptions\ComponentException;
use Respect\Validation\Rules\AbstractRule;

class Unique extends AbstractRule
{
    public function validate($input)
    {
        if (!is_array($input)) {
            throw new ComponentException('Input must be an array');
        }

        foreach (array_values(array_count_values($input)) as $count) {
            if ($count > 1) {
                return false;
            }
        }

        return true;
    }

}