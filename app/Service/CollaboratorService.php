<?php
/**
 * Created by PhpStorm.
 * User: yoshidam2
 * Date: 2016/09/21
 * Time: 17:57
 */

App::uses('Collaborator', 'Model');
App::uses('TeamMember', 'Model');
App::uses('User', 'Model');

class CollaboratorService
{
    const EXTEND_COACH = "GOAL:EXTEND_COACH";
    const EXTEND_COACHEE = "GOAL:EXTEND_COACHEE";

    function get($id, $extends =[])
    {
        $Collaborator = ClassRegistry::init("Collaborator");

        $data = Hash::extract($Collaborator->findById($id), 'Collaborator');
        if (empty($data)) {
            return $data;
        }

        return $this->extend($data, $extends);
    }

    function extend($data, $extends) {
        if (empty($data) || empty($extends)) {
            return $data;
        }

        $TeamMember = ClassRegistry::init("TeamMember");
        $User = ClassRegistry::init("User");

        if (in_array(self::EXTEND_COACH, $extends)) {
            $coachId = $TeamMember->getCoachId($data['user_id']);
            $data['coach'] = Hash::extract($User->findById($coachId), 'User');
        }

        if (in_array(self::EXTEND_COACHEE, $extends)) {
            $data['coachee'] = Hash::extract($User->findById($data['user_id']), 'User');
        }
        return $data;
    }
}
