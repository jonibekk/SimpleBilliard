
<?php
App::uses('AppController', 'Controller');
App::import('Controller/Traits', 'AuthTrait');
App::uses('Email', 'Model');
App::uses('GoalousDateTime', 'DateTime');

class GroupsController extends AppController
{
    use AuthTrait;

    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->_checkAdmin([
            'ajax_set_current_team_admin_user_flag',
            'ajax_set_current_team_evaluation_flag',
            'ajax_inactivate_team_member',
            'activate_team_member',
            'activate_confirm_with_payment',
            'activate_with_payment'
        ]);
    }

    function members_list(int $groupId)
    {
        /** @var TeamService $TeamService */
        $TeamService = ClassRegistry::init("TeamService");
        $teamId = $this->current_team_id;
        $timezone = $TeamService->getTeamTimezone($teamId);
        $datetime = GoalousDateTime::now()->setTimeZoneByHour($timezone)->format('Ymd_His');

        // set filename: group_members_yyyyMMDD_HHMMSS 
        $this->response->download("group_members_{$datetime}.csv");

        /** @var Email $Email */
        $Email = ClassRegistry::init("Email");
        $data = $Email->findForGroup($groupId);

        $this->set(compact('data'));
        $this->layout = false;
        return;
    }

    function ajax_get_group_members()
    {
        $groupId = Hash::get($this->request->params, 'named.group_id');
        $this->_ajaxPreProcess();

        /** @var MemberGroup */
        $MemberGroup = ClassRegistry::init("MemberGroup");

        $groupMembers = $MemberGroup->find('all', [
            'conditions' => [
                'MemberGroup.group_id' => $groupId,
                'MemberGroup.del_flg != 1'
            ],
            'contain' => ['User']
        ]);

        $this->set(compact('groupMembers'));
        $response = $this->render('Group/modal_group_members');
        $html = $response->__toString();
        return $this->_ajaxGetResponse($html);
    }
}
