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

    function getFollowOption($goal)
    {
        $option = [
            'class'    => 'follow-off',
            'style'    => null,
            'text'     => __("Follow"),
            'disabled' => null,
        ];

        //if coaching goal then, already following.
        if (viaIsSet($goal['User']['TeamMember'][0]['coach_user_id'])) {
            $option['disabled'] = "disabled";
            return $option;
        }

        if (viaIsSet($goal['MyCollabo'])) {
            $option['disabled'] = "disabled";
        }

        if (empty($goal['MyFollow']) && !viaIsSet($goal['User']['TeamMember'][0]['coach_user_id'])) {
            return $option;
        }

        if (!empty($goal['MyFollow']) && viaIsSet($goal['User']['TeamMember'][0]['coach_user_id'])) {
            $option['disabled'] = "disabled";
            return $option;
        }

        $option['class'] = 'follow-on';
        $option['style'] = 'display:none;';
        $option['text'] = __("Following");
        return $option;
    }

    function getCollaboOption($goal)
    {
        $option = [
            'class' => 'collabo-off',
            'style' => null,
            'text'  => __("Collaborate"),
        ];

        if (!viaIsSet($goal['MyCollabo'])) {
            return $option;
        }
        $option['class'] = 'collabo-on';
        $option['style'] = 'display:none;';
        $option['text'] = __("Collaborating");
        return $option;
    }

    /**
     * @param array $goal_member
     *
     * @return null
     */
    function displayGoalMemberNameList($goal_member)
    {
        if (!is_array($goal_member) || empty($goal_member)) {
            return null;
        }
        $items = [];
        $i = 1;
        foreach ($goal_member as $k => $v) {
            $items[] = h($v['User']['display_username']);
            if ($i >= 2) {
                break;
            }
            $i++;
        }
        $rest_count = count($goal_member) - 2;
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
     * @param array $goal_member
     *
     * @return string
     */
    function displayApprovalStatus($goal_member)
    {
        $waiting = __("Waiting for approval");
        $out_of_evaluation = __("Out of Evaluation");
        $in_evaluation = __("In Evaluation");

        if (!($goal_member['is_wish_approval'] && $goal_member['is_approval_enabled'])) {
            return '';
        }

        if ($goal_member['approval_status'] == GoalMember::APPROVAL_STATUS_NEW || $goal_member['approval_status'] == GoalMember::APPROVAL_STATUS_REAPPLICATION) {
            return $waiting;
        }

        if ($goal_member['is_target_evaluation']) {
            return $in_evaluation;
        }

        return $out_of_evaluation;
    }

}
