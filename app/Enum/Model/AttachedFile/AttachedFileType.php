<?php
/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/08/01
 * Time: 13:50
 */

namespace Goalous\Enum\Model\AttachedFile;

use MyCLabs\Enum\Enum;

/**
 * Class AttachedFileType
 *
 * @package Goalous\Enum\Model\AttachedFile
 *
 * @method static static TYPE_FILE_IMG()
 * @method static static TYPE_FILE_VIDEO()
 * @method static static TYPE_FILE_DOC()
 */
class AttachedFileType extends Enum
{
    /**
     * file type
     */
    const TYPE_FILE_IMG = 0;
    const TYPE_FILE_VIDEO = 1;
    const TYPE_FILE_DOC = 2;

}
