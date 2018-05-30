<?php
/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/05/23
 * Time: 17:05
 */

class GoalFeedPaging implements PagingServiceInterface
{
    use PagingServiceTrait;
    use FeedPagingTrait;

    const EXTEND_ALL_FLAG = -1;
    const EXTEND_USER_FLAG = 0;
    const EXTEND_POST_LIKE_FLAG = 1;
    const EXTEND_COMMENT_FLAG = 3;
    const EXTEND_POST_FILE_FLAG = 6;
    const EXTEND_GOAL_FLAG = 7;
    const EXTEND_KR_FLAG = 8;
    const EXTEND_ACTION_RESULT_FLAG = 9;

    protected function readData($pagingCursor, $limit): array
    {
        // TODO: Implement readData() method.
    }

    protected function countData($conditions): int
    {
        // TODO: Implement countData() method.
    }

    protected function extendPagingResult(&$resultArray, &$conditions, $flags = [])
    {
        if (in_array(self::EXTEND_ALL_FLAG, $flags) || in_array(self::EXTEND_USER_FLAG, $flags)) {

        }
        if (in_array(self::EXTEND_ALL_FLAG, $flags) || in_array(self::EXTEND_POST_LIKE_FLAG, $flags)) {

        }
        if (in_array(self::EXTEND_ALL_FLAG, $flags) || in_array(self::EXTEND_COMMENT_FLAG, $flags)) {

        }
        if (in_array(self::EXTEND_ALL_FLAG, $flags) || in_array(self::EXTEND_POST_FILE_FLAG, $flags)) {

        }
        if (in_array(self::EXTEND_ALL_FLAG, $flags) || in_array(self::EXTEND_GOAL_FLAG, $flags)) {

        }
        if (in_array(self::EXTEND_ALL_FLAG, $flags) || in_array(self::EXTEND_KR_FLAG, $flags)) {

        }
        if (in_array(self::EXTEND_ALL_FLAG, $flags) || in_array(self::EXTEND_ACTION_RESULT_FLAG, $flags)) {

        }
    }

}