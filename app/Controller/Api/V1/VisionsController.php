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
    function index()
    {
        $team_visions = Hash::insert(
            Hash::extract($this->TeamVision->getTeamVision($this->current_team_id, true, true),
                '{n}.TeamVision'), '{n}.type', 'team_vision');
        $group_visions = Hash::insert($this->GroupVision->getMyGroupVision(true), '{n}.type', 'group_vision');

        $visions = am($team_visions, $group_visions);
        return $this->_getResponse(200, $visions);
    }

}
