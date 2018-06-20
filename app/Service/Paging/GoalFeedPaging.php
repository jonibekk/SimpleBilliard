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

    const EXTEND_ALL = "ext:goal_post:all";
    const EXTEND_USER = "ext:goal_post:user";
    const EXTEND_COMMENT = "ext:goal_post:comment";
    const EXTEND_POST_FILE = "ext:goal_post:file";
    const EXTEND_GOAL = "ext:goal_post:goal";
    const EXTEND_KR = "ext:goal_post:kr";
    const EXTEND_ACTION_RESULT = "ext:goal_post:action_result";

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
        if (in_array(self::EXTEND_ALL, $flags) || in_array(self::EXTEND_USER, $flags)) {

        }
        if (in_array(self::EXTEND_ALL, $flags) || in_array(self::EXTEND_POST_LIKE, $flags)) {

        }
        if (in_array(self::EXTEND_ALL, $flags) || in_array(self::EXTEND_COMMENT, $flags)) {

        }
        if (in_array(self::EXTEND_ALL, $flags) || in_array(self::EXTEND_POST_FILE, $flags)) {

        }
        if (in_array(self::EXTEND_ALL, $flags) || in_array(self::EXTEND_GOAL, $flags)) {

        }
        if (in_array(self::EXTEND_ALL, $flags) || in_array(self::EXTEND_KR, $flags)) {

        }
        if (in_array(self::EXTEND_ALL, $flags) || in_array(self::EXTEND_ACTION_RESULT, $flags)) {

        }
    }

}