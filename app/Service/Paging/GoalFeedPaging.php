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
    const EXTEND_COMMENT_FLAG = 1;
    const EXTEND_GOAL_FLAG = 2;
    const EXTEND_KR_FLAG = 3;
    const EXTEND_ACTION_RESULT_FLAG = 4;
    const EXTEND_POST_FILE_FLAG = 5;

    protected function readData($pagingCursor, $limit): array
    {
        // TODO: Implement readData() method.
    }

    protected function countData($conditions): int
    {
        // TODO: Implement countData() method.
    }

    protected function extendPagingResult(&$resultArray, $flags = [])
    {
    }

}