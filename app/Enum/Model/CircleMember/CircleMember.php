<?php
/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/09/04
 * Time: 11:46
 */

namespace Goalous\Enum\Model\CircleMember;

use MyCLabs\Enum\Enum;

/**
 * Class AttachedFileType
 *
 * @package Goalous\Enum\Model\CircleMember
 * @method static static NOT_ADMIN()
 * @method static static ADMIN()
 */
class CircleMember extends Enum
{
    const NOT_ADMIN = 0;
    const ADMIN = 1;
}