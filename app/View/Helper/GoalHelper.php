<?php
App::uses('AppHelper', 'View/Helper');

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
            'text'     => __d('app', "フォロー"),
            'disabled' => null,
        ];

        //if coaching goal then, already following.
        if (viaIsSet($goal['User']['TeamMember'][0]['coach_user_id'])) {
            $option['class'] = 'follow-on';
            $option['style'] = 'display:none;';
            $option['disabled'] = "disabled";
            $option['text'] = __d('app', "フォロー中");
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
        $option['text'] = __d('app', "フォロー中");
        return $option;
    }

    function getCollaboOption($goal)
    {
        $option = [
            'class' => 'collabo-off',
            'style' => null,
            'text'  => __d('app', "コラボる"),
        ];

        if (!viaIsSet($goal['MyCollabo'])) {
            return $option;
        }
        $option['class'] = 'collabo-on';
        $option['style'] = 'display:none;';
        $option['text'] = __d('app', "コラボり中");
        return $option;
    }

    /**
     * @param array $collaborator
     *
     * @return null
     */
    function displayCollaboratorNameList($collaborator)
    {
        if (!is_array($collaborator) || empty($collaborator)) {
            return null;
        }
        $items = [];
        $i = 1;
        foreach ($collaborator as $k => $v) {
            $items[] = h($v['User']['display_username']);
            if ($i >= 2) {
                break;
            }
            $i++;
        }
        $rest_count = count($collaborator) - 2;
        if ($rest_count > 0) {
            $items[] = __d('app', "他%s人", $rest_count);
        }

        return "( " . implode(", ", $items) . " )";
    }

    /**
     * @param $goal
     * @param $my_coaching_users
     *
     * @return bool
     */
    function containMyCoachingUserInCollabos($goal, $my_coaching_users)
    {
        if (!$my_coaching_users || !is_array($my_coaching_users)) {
            return false;
        }

        if (!isset($goal['Collaborator'])) {
            return false;
        }

        $collabos = Hash::extract($goal['Collaborator'], '{n}.user_id');
        if (!$collabos) {
            return false;
        }

        return (boolean)array_intersect($collabos, $my_coaching_users);
    }

}
