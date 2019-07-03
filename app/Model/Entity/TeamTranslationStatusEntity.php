<?php
App::import('Model/Entity', 'BaseEntity');

class TeamTranslationStatusEntity extends BaseEntity
{
    /**
     * Get total translation usage count
     *
     * @return int
     */
    public function getTotalUsageCount(): int
    {
        return $this->getCirclePostUsageCount()
            + $this->getCirclePostCommentUsageCount()
            + $this->getActionPostUsageCount()
            + $this->getActionPostCommentUsageCount();
    }

    /**
     * Get total translation usage for circle post
     *
     * @return int
     */
    public function getCirclePostUsageCount(): int
    {
        return $this['circle_post_total'] ?: 0;
    }

    /**
     * Get total translation usage for circle post comment
     *
     * @return int
     */
    public function getCirclePostCommentUsageCount(): int
    {
        return $this['circle_post_comment_total'] ?: 0;
    }

    /**
     * Get total translation usage for action post
     *
     * @return int
     */
    public function getActionPostUsageCount(): int
    {
        return $this['action_post_total'] ?: 0;
    }

    /**
     * Get total translation usage for action post comment
     *
     * @return int
     */
    public function getActionPostCommentUsageCount(): int
    {
        return $this['action_post_comment_total'] ?: 0;
    }

    /**
     * Get translation limit of the team
     *
     * @return int
     */
    public function getTotalLimit(): int
    {
        return $this['total_limit'];
    }

    /**
     * Check whether usage is reached
     *
     * @return bool
     */
    public function isLimitReached(): bool
    {
        return $this->getTotalUsageCount() >= $this->getTotalLimit();
    }

    /**
     * Return true if difference between usage and limit is within percentage of limit
     *
     * @param float $percent
     *
     * @return bool
     */
    public function isUsageWithinPercentageOfLimit(float $percent): bool
    {
        $currentUsage = $this->getTotalUsageCount();
        $total = $this->getTotalLimit();

        return (float)((1 - $percent) * $total) < $currentUsage;
    }
}