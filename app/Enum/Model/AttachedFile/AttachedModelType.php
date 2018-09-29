<?php
/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/08/01
 * Time: 18:55
 */

namespace Goalous\Enum\Model\AttachedFile;

use MyCLabs\Enum\Enum;

/**
 * Class AttachedFileType
 *
 * @package Goalous\Enum\Model\AttachedFile
 *
 * @method static static TYPE_MODEL_POST()
 * @method static static TYPE_MODEL_COMMENT()
 * @method static static TYPE_MODEL_ACTION_RESULT()
 * @method static static TYPE_MODEL_MESSAGE()
 */
class AttachedModelType extends Enum
{
    const TYPE_MODEL_POST = 0;
    const TYPE_MODEL_COMMENT = 1;
    const TYPE_MODEL_ACTION_RESULT = 2;
    const TYPE_MODEL_MESSAGE = 3;
}