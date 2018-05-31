<?php
App::import('Service', 'AppService');
App::uses('User', 'Model');
App::uses('Goal', 'Model');
App::uses('Circle', 'Model');

/**
 * CirclePin Model
 */
class SearchService extends AppService
{
    public function searchByKeword($keyword, $limit = 20, $with_self = false) {
        /** @var User $User */
        $User = ClassRegistry::init('User');
        /** @var Goal $Goal */
        $Goal = ClassRegistry::init('Goal');
        /** @var Circle $Circle */
        $Circle = ClassRegistry::init('Circle');

        $user_res = $this->makeUsersList($User->getUsersByKeyword($keyword, $limit, !$with_self));
        $goal_res = $this->makeGoalsList($Goal->getGoalsByKeyword($keyword, $limit, null, null));
        $circle_res = $this->makeCirclesList($Circle->getAccessibleCirclesByKeyword($keyword, $limit));

        return [
            'results_users' => $user_res,
            'results_goals' => $goal_res,
            'results_circles' => $circle_res,
        ];
    }

    /**
     * Search 用のユーザーリスト配列を返す
     *
     * @param array $users
     *
     * @return array
     */
    private function makeUsersList(array $users)
    {
        App::uses('UploadHelper', 'View/Helper');
        $Upload = new UploadHelper(new View());

        $res = [];
        foreach ($users as $val) {
            $data = [];
            $data['id'] = 'user_' . $val['User']['id'];
            $data['text'] = $val['User']['display_username'] . " (" . $val['User']['roman_username'] . ")";
            $data['image'] = $Upload->uploadUrl($val, 'User.photo', ['style' => 'medium_large']);
            $res[] = $data;
        }
        return ['results' => $res];
    }

    /**
     * Search 用のゴールリスト配列を返す
     *
     * @param array $goals
     *
     * @return array
     */
    private function makeGoalsList(array $goals)
    {
        App::uses('UploadHelper', 'View/Helper');
        $Upload = new UploadHelper(new View());
        $res = [];
        foreach ($goals as $val) {
            $data = [];
            $data['id'] = 'goal_' . $val['Goal']['id'];
            $data['text'] = $val['Goal']['name'];
            $data['image'] = $Upload->uploadUrl($val, 'Goal.photo', ['style' => 'medium_large']);
            $res[] = $data;
        }
        return ['results' => $res];
    }

    /**
     * Search 用のサークルリスト配列を返す
     *
     * @param array $circles
     *
     * @return array
     */
    private function makeCirclesList(array $circles)
    {
        App::uses('UploadHelper', 'View/Helper');
        $Upload = new UploadHelper(new View());
        $res = [];
        foreach ($circles as $val) {
            $data = [];
            $data['id'] = 'goal_' . $val['Circle']['id'];
            $data['text'] = $val['Circle']['name'];
            $data['image'] = $Upload->uploadUrl($val, 'Circle.photo', ['style' => 'medium_large']);
            $res[] = $data;
        }
        return ['results' => $res];
    }
}
