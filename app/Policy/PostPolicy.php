<?php
App::uses('Post', 'Model');
App::uses('GoalGroup', 'Model');
App::import('Policy', 'BasePolicy');
App::import('Service', 'PostService');

/**
 * Class PostPolicy
 */
class PostPolicy extends BasePolicy
{
    public function scope(): array
    {
        if ($this->isTeamAdmin()) {
            return ['conditions' => ['Post.team_id' => $this->teamId]];
        }
        /** @var Post **/
        $Post = ClassRegistry::init('Post');
        /** @var GoalGroup */
        $GoalGroup = ClassRegistry::init('GoalGroup');

        $allPublicQuery = $Post->publicPostsSubQuery();
        $allGroupsQuery = $GoalGroup->goalByUserIdSubQuery($this->userId);

        $result =  [
            'conditions' => [
                'Post.id in (' . $allPublicQuery . ') OR 
                 Post.goal_id in (' . $allGroupsQuery . ')'
            ],
        ];

        return $result;
    }
}
