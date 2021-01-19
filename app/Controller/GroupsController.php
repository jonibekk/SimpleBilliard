
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

    function ajax_get_group_members()
    {
        $groupId = Hash::get($this->request->params, 'named.group_id');
        $this->_ajaxPreProcess();

        /** @var Group */
        $Group = ClassRegistry::init("Group");

        $groupMembers = $Group->findMembers($groupId);
        $this->set(compact('groupMembers'));
        $response = $this->render('Group/modal_group_members');
        $html = $response->__toString();
        return $this->_ajaxGetResponse($html);
    }
}
