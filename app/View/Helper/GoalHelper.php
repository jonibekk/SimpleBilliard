<?php
App::uses('AppHelper', 'View/Helper');
App::uses('GoalMember', 'Model');

/**
 * GoalHelper
 *
 * @author daikihirakata
 */
class GoalHelper extends AppHelper
{
    /**
     * Get HTML options for Follow element
     * @param      $goal
     * @param null $goalTerm
     *
     * @return array
     */
    function getFollowOption($goal, $goalTerm = null)
    {
        $option = [
            'class'    => 'follow-off',
            'style'    => null,
            'text'     => __("Follow"),
            'disabled' => null,
        ];

        //if coaching goal then, already following.
        if (Hash::get($goal, 'User.TeamMember.0.coach_user_id')) {
            $option['disabled'] = "disabled";
            return $option;
        }

        // Check if goal is completed or expired
        if ($this->isExpiredOrCompleted($goal, $goalTerm)) {
            $option['disabled'] = "disabled";
        }

        if (Hash::get($goal, 'MyCollabo')) {
            $option['disabled'] = "disabled";
        }

        if (empty($goal['MyFollow']) && !Hash::get($goal, 'User.TeamMember.0.coach_user_id')) {
            return $option;
        }

        if (!empty($goal['MyFollow']) && Hash::get($goal, 'User.TeamMember.0.coach_user_id')) {
            $option['disabled'] = "disabled";
            return $option;
        }

        $option['class'] = 'follow-on';
        $option['style'] = 'display:none;';
        $option['text'] = __("Following");
        return $option;
    }

    function isExpiredOrCompleted($goal, $goalTerm)
    {
        // Check if goal is completed
        $completed = Hash::get($goal, 'Goal.completed');
        if ($completed !== null) {
            return true;
        }

        // If the goal is from previous term, do not allow to follow
        if ($goalTerm) {
            $endDate = Hash::get($goal, 'Goal.end_date');
            $today = AppUtil::todayDateYmdLocal(Hash::get($goalTerm, 'timezone'));

            if ($today > $endDate) {
                return true;
            }
        }
        return true;
    }

    /**
     * Get HTML options for Collabo element
     * @param      $goal
     * @param null $goalTerm
     *
     * @return array
     */
    function getCollaboOption($goal, $goalTerm = null)
    {
        $option = [
            'class' => 'collabo-off',
            'style' => null,
            'text'  => __("Collabo"),
            'disabled' => null
        ];

        // Check if goal is completed or expired
        if ($this->isExpiredOrCompleted($goal, $goalTerm)) {
            $option['disabled'] = "disabled";
        }

        if (!Hash::get($goal, 'MyCollabo')) {
            return $option;
        }
        $option['class'] = 'collabo-on';
        $option['style'] = 'display:none;';
        $option['text'] = __("Collaborating");
        return $option;
    }

    /**
     * @param array $goalMember
     *
     * @return null
     */
    function displayGoalMemberNameList($goalMember)
    {
        if (!is_array($goalMember) || empty($goalMember)) {
            return null;
        }
        $items = [];
        $i = 1;
        foreach ($goalMember as $k => $v) {
            $items[] = h($v['User']['display_username']);
            if ($i >= 2) {
                break;
            }
            $i++;
        }
        $rest_count = count($goalMember) - 2;
        if ($rest_count > 0) {
            $items[] = __("Other %d members", $rest_count);
        }

        return "( " . implode(", ", $items) . " )";
    }

    /**
     * @param $goal
     * @param $my_coaching_users
     *
     * @return bool
     */
    function isCoachingUserGoal($goal, $my_coaching_users)
    {
        if (!is_array($my_coaching_users) || count($my_coaching_users) === 0) {
            return false;
        }

        if (!isset($goal['GoalMember'])) {
            return false;
        }

        // Case of coaching user is goal leader
        if (in_array($goal['Leader'][0]['user_id'], $my_coaching_users)) {
            return true;
        }

        $collabos = Hash::extract($goal['GoalMember'], '{n}.user_id');
        if (!$collabos) {
            return false;
        }
        // Case of coaching user is goal goal_member
        if (array_intersect($collabos, $my_coaching_users)) {
            return true;
        }

        return false;
    }

    /**
     * @param array $goalMember
     *
     * @return string
     */
    function displayApprovalStatus($goalMember)
    {
        $waiting = __("Waiting for approval");
        $out_of_evaluation = __("Out of Evaluation");
        $in_evaluation = __("In Evaluation");

        if (!($goalMember['is_wish_approval'] && $goalMember['is_approval_enabled'])) {
            return '';
        }

        if ($goalMember['approval_status'] == GoalMember::APPROVAL_STATUS_NEW || $goalMember['approval_status'] == GoalMember::APPROVAL_STATUS_REAPPLICATION) {
            return $waiting;
        }

        if ($goalMember['is_target_evaluation']) {
            return $in_evaluation;
        }

        return $out_of_evaluation;
    }
}
