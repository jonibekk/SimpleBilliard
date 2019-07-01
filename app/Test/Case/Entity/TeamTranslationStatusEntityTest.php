<?php
App::uses('GoalousTestCase', 'Test');
App::uses('TeamTranslationStatusEntity', 'Model/Entity');

class TeamTranslationStatusEntityTest extends GoalousTestCase
{
    private static $modelname = 'TeamTranslationStatusEntity';

    function test_isUsageBecomeHighThanPercent()
    {
        $entity = new TeamTranslationStatusEntity([
            'team_id' => 1,
            'circle_post_total' => 8999,
            'circle_post_comment_total' => 0,
            'action_post_total' => 0,
            'action_post_comment_total' => 0,
            'total_limit' => 10000,
        ], static::$modelname);
        $this->assertFalse($entity->isUsageBecomeHighThanPercent(0.9, 1));

        $entity = new TeamTranslationStatusEntity([
            'team_id' => 1,
            'circle_post_total' => 9000,
            'circle_post_comment_total' => 0,
            'action_post_total' => 0,
            'action_post_comment_total' => 0,
            'total_limit' => 10000,
        ], static::$modelname);
        $this->assertTrue($entity->isUsageBecomeHighThanPercent(0.9, 1));

        $entity = new TeamTranslationStatusEntity([
            'team_id' => 1,
            'circle_post_total' => 9001,
            'circle_post_comment_total' => 0,
            'action_post_total' => 0,
            'action_post_comment_total' => 0,
            'total_limit' => 10000,
        ], static::$modelname);
        $this->assertFalse($entity->isUsageBecomeHighThanPercent(0.9, 1));
    }

    function test_isLimitReached()
    {
        $notOverLimit = new TeamTranslationStatusEntity([
            'team_id' => 1,
            'circle_post_total' => 2,
            'circle_post_comment_total' => 2,
            'action_post_total' => 2,
            'action_post_comment_total' => 2,
            'total_limit' => 10,
        ], static::$modelname);
        $this->assertFalse($notOverLimit->isLimitReached());

        $overLimit = new TeamTranslationStatusEntity([
            'team_id' => 1,
            'circle_post_total' => 3,
            'circle_post_comment_total' => 3,
            'action_post_total' => 3,
            'action_post_comment_total' => 3,
            'total_limit' => 10,
        ], static::$modelname);

        $this->assertTrue($overLimit->isLimitReached());

        $equalLimit = new TeamTranslationStatusEntity([
            'team_id' => 1,
            'circle_post_total' => 3,
            'circle_post_comment_total' => 3,
            'action_post_total' => 3,
            'action_post_comment_total' => 1,
            'total_limit' => 10,
        ], static::$modelname);

        $this->assertTrue($equalLimit->isLimitReached());
    }
}