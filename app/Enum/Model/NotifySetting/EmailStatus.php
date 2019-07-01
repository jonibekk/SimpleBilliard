<?php
namespace Goalous\Enum\Model\NotifySetting;

use MyCLabs\Enum\Enum;

/**
 * @method static static NONE()
 * @method static static ALL()
 * @method static static PRIMARY()
 */
class EmailStatus extends Enum
{
    const NONE = 'none';
    const ALL = 'all';
    const PRIMARY = 'primary';
}
