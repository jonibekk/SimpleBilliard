<?php
App::uses('GoalousTestCase', 'Test');
App::import('Service', 'VisionService');

/**
 * VisionServiceTest Class
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 2016/12/08
 * Time: 17:50
 *
 * @property VisionService $VisionService
 */
class VisionServiceTest extends GoalousTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->VisionService = ClassRegistry::init('VisionService');
    }

    function test_getGroupListAddableVision()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function test_hasPermissionToEdit()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function test_isGroupMemberByGroupVisionId()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function test_existsGroupVision()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function test_buildGroupVisionListForResponse()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function test_buildGroupVisionDetailForResponse()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function test_buildTeamVisionListForResponse()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function test_buildTeamVisionDetailForResponse()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function test_extendGroupVision()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function test_extendTeamVision()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

}
