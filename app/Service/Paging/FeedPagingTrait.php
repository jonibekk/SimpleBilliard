<?php
/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/05/23
 * Time: 13:14
 */

trait FeedPagingTrait
{
    protected final function createDbExpression(DboSource $db, string $statement)
    {
        return $db->expression('Post.id IN (' . $statement . ')');
    }
}