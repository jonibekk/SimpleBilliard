<?php
App::uses('Post', 'Model');
App::uses('GoalGroup', 'Model');
App::uses('TeamMember', 'Model');
App::uses('Evaluator', 'Model');
App::import('Policy', 'BasePolicy');
App::import('Service', 'PostService');

use Goalous\Enum as Enum;

/**
 * Class PostPolicy
 */
class PostPolicy extends BasePolicy
{
    public function read($post): bool
    {
        switch ($post['type']) {
            case Enum\Model\Post\Type::ACTION:
            case Enum\Model\Post\Type::CREATE_GOAL:
                return (((int)$post['user_id'] === $this->userId) ||
                    //($this->isTeamAdminForItem($post['team_id'])) ||
                    //($this->isCoach($post['goal_id'])) ||
                    //($this->isActiveEvaluator($post['goal_id'])) ||
                    ($this->isSameGroup($post))
                );            
            case Enum\Model\Post\Type::NORMAL:
                return $this->checkCirclePostAccess($post);
        }

        return false;
    }


    private function checkCirclePostAccess($post): bool
    {
        /** @var PostService */
        $PostService = ClassRegistry::init('PostService');
        return $PostService->checkUserAccessToCirclePost($this->userId, $post['id']);
    }

    private function isSameGroup($post): bool
    {
        /** @var GoalGroup */
        $GoalGroup = ClassRegistry::init('GoalGroup');

        // check if post is linked to any groups, none means it is visible to entire team
        if (!$GoalGroup->hasAny(['GoalGroup.goal_id' => $post['goal_id']])) {
            return $post['team_id'] === $this->teamId;
        }

        $results = $GoalGroup->find('all', [
            'conditions' => [
                'GoalGroup.goal_id' => $post['goal_id']
            ],
            'joins' => [$GoalGroup->joinByUserId($this->userId)]
        ]);

        return !empty($results);
    }

    public function scope($type = 'read'): array
    {
        //if ($this->isTeamAdmin()) {
            //return ['conditions' => ['Post.team_id' => $this->teamId]];
        //}
        
        /** @var Post **/
        $Post = ClassRegistry::init('Post');
        /** @var GoalGroup */
        $GoalGroup = ClassRegistry::init('GoalGroup');

        $allPublicQuery = $Post->publicPostsSubQuery();
        $allGroupsQuery = $GoalGroup->goalByUserIdSubQuery($this->userId);
        //$allCoacheesQuery = $Post->coacheePostsSubQuery($this->userId, $this->teamId);
        //$allEvaluateesQuerys = $Post->evaluateePostsSubQuery($this->userId, $this->teamId);

        $fullQuery = 'Post.id in (' . $allPublicQuery . ') OR 
                      Post.goal_id in (' . $allGroupsQuery . ') OR
                      Post.type = 1';

        //if ($type === 'read') {
            //$query = 'Post.id in (' . $allCoacheesQuery . ') OR ';
            //$fullQuery = $query . $fullQuery;

            //if ($this->evaluationSettingEnabled()) {
                //$query = 'Post.id in (' . $allEvaluateesQuerys . ') OR ';
                //$fullQuery = $query . $fullQuery;
            //}
        //}

        return [
            'conditions' => [
                'Post.team_id' => $this->teamId,
                '(' . $fullQuery . ')',
            ]
        ];
    }
}