<?php
namespace Goalous\Enum\Csv;

use MyCLabs\Enum\Enum;

/**
 * @method static static GOAL_ID()
 * @method static static GOAL_NAME()
 * @method static static GOAL_DESCRIPTION()
 * @method static static GOAL_CATEGORY()
 * @method static static GOAL_LABELS()
 * @method static static GOAL_MEMBERS_COUNT()
 * @method static static FOLLOWERS_COUNT()
 * @method static static KRS_COUNT()
 * @method static static TERM()
 * @method static static GOAL_START_DATE()
 * @method static static GOAL_END_DATE()
 * @method static static LEADER_USER_ID()
 * @method static static LEADER_NAME()
 * @method static static GOAL_PROGRESS()
 * @method static static GOAL_CREATED()
 * @method static static GOAL_EDITED()
 * @method static static KR_ID()
 * @method static static KR_NAME()
 * @method static static KR_DESCRIPTION()
 * @method static static KR_TYPE()
 * @method static static KR_WEIGHT()
 * @method static static KR_START_DATE()
 * @method static static KR_END_DATE()
 * @method static static KR_PROGRESS()
 * @method static static KR_UNIT()
 * @method static static KR_INITIAL()
 * @method static static KR_TARGET()
 * @method static static KR_CURRENT()
 * @method static static KR_CREATED()
 * @method static static KR_EDITED()
 */
class GoalAndKrs extends Enum
{
    const GOAL_ID = 0;
    const GOAL_NAME = 1;
    const GOAL_DESCRIPTION = 2;
    const GOAL_CATEGORY = 3;
    const GOAL_LABELS = 4;
    const GOAL_MEMBERS_COUNT = 5;
    const FOLLOWERS_COUNT = 6;
    const KRS_COUNT = 7;
    const TERM = 8;
    const GOAL_START_DATE = 11;
    const GOAL_END_DATE = 12;
    const LEADER_USER_ID = 13;
    const LEADER_NAME = 14;
    const GOAL_PROGRESS = 15;
    const GOAL_CREATED = 16;
    const GOAL_EDITED = 17;
    const KR_ID = 18;
    const KR_NAME = 19;
    const KR_DESCRIPTION = 20;
    const KR_TYPE = 21;
    const KR_WEIGHT = 22;
    const KR_START_DATE = 23;
    const KR_END_DATE = 24;
    const KR_PROGRESS = 25;
    const KR_UNIT = 26;
    const KR_INITIAL = 27;
    const KR_TARGET = 28;
    const KR_CURRENT = 29;
    const KR_CREATED = 30;
    const KR_EDITED = 31;
}
