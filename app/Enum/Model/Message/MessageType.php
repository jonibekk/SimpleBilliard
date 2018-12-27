<?php
/**
 * Created by PhpStorm.
 * User: Stephen Raharja
 * Date: 12/18/2018
 * Time: 10:37 AM
 */

namespace Goalous\Enum\Model\Message;

use MyCLabs\Enum\Enum;

/**
 * Class MessageType
 * @method static static NORMAL()
 * @method static static ADD_MEMBER()
 * @method static static LEAVE()
 * @method static static SET_TOPIC_NAME()
 *
 * @package Goalous\Enum\Model\Message
 */
class MessageType extends Enum
{
    const NORMAL = 1;
    const ADD_MEMBER = 2;
    const LEAVE = 3;
    const SET_TOPIC_NAME = 4;
}