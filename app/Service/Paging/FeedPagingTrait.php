<?php
/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/05/23
 * Time: 13:14
 */

trait FeedPagingTrait
{
    public function setCommentRead(&$postArray)
    {

    }

    public function setCommentUnreadCount(&$postArray)
    {

    }

    public function getPostResource(&$postArray)
    {

    }

    protected final function createDbExpression(DboSource $db, string $statement)
    {
        return $db->expression('Post.id IN (' . $statement . ')');
    }
}