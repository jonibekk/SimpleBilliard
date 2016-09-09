<?php
App::uses('ApiController', 'Controller/Api');
App::uses('TeamVision', 'Model');
App::uses('GroupVision', 'Model');

/**
 * Visions Controller
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 9/6/16
 * Time: 16:38
 *
 * @property TeamVision  $TeamVision
 * @property GroupVision $GroupVision
 */
class VisionsController extends ApiController
{
    public $uses = [
        'TeamVision',
        'GroupVision',
    ];

    /**
     * GET visions
     *
     * @return string
     */
    function get_list()
    {
        $team_visions = Hash::insert(
            Hash::extract($this->TeamVision->getTeamVision($this->current_team_id, true, true),
                '{n}.TeamVision'), '{n}.type', 'team_vision');
        $group_visions = Hash::insert($this->GroupVision->getMyGroupVision(true), '{n}.type', 'group_vision');

        $visions = am($team_visions, $group_visions);
        return $this->_getResponseSuccess($visions);
    }

    /**
     * TODO 以下はあくまで仕様の説明の為に用意しているもの(最後削除する)
     */

    /**
     * REST
     */
    function get_detail($id)
    {
        return $this->_getResponse(200, 'Success! This action is view($id). ID:' . $id);
    }

    function post()
    {
        return $this->_getResponse(200, 'Success! This action is add()');
    }

    function put($id)
    {
        return $this->_getResponse(200, 'Success! This action is edit($id). ID:' . $id);
    }

    function delete($id)
    {
        return $this->_getResponse(200, 'Success! This action is delete($id). ID:' . $id);
    }

    /**
     * RESTじゃないもの
     */
    function get_test_get_only()
    {
        $this->request->allowMethod('get');
        return $this->_getResponse(200, 'success');
    }

    function put_test_post_with_id_only($id)
    {
        $this->request->allowMethod('put');
        $this->_requiredId($id);
        return $this->_getResponse(200, 'success');
    }

}
