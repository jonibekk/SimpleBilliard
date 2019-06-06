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
        return ($this['circle_post_total'] ?: 0) +
            ($this['circle_post_comment_total'] ?: 0) +
            ($this['action_post_total'] ?: 0) +
            ($this['action_post_comment_total'] ?: 0);
    }
}