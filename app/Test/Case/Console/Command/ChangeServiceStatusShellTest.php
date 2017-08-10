<?php
App::uses('GoalousTestCase', 'Test');
App::uses('ConsoleOutput', 'Console');
App::uses('ShellDispatcher', 'Console');
App::uses('Shell', 'Console');
App::uses('Folder', 'Utility');
App::uses('ChangeServiceStatusShell', 'Console/Command');
App::uses('Team', 'Model');

/**
 * Class ChangeServiceStatusShellTest
 *
 * @property ChangeServiceStatusShell $ChangeServiceStatusShell
 * @property Team                     $Team
 */
class ChangeServiceStatusShellTest extends GoalousTestCase
{
    public $ChangeServiceStatusShell;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.team',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    function setUp()
    {
        parent::setUp();
        $output = $this->getMock('ConsoleOutput', [], [], '', false);
        $error = $this->getMock('ConsoleOutput', [], [], '', false);
        $in = $this->getMock('ConsoleInput', [], [], '', false);
        /** @var ChangeServiceStatusShell $ChangeServiceStatusShell */
        $this->ChangeServiceStatusShell = new ChangeServiceStatusShell($output, $error, $in);
        $this->ChangeServiceStatusShell->initialize();
        $this->ChangeServiceStatusShell->startup();
        $this->Team = ClassRegistry::init('Team');
    }

    function tearDown()
    {

    }

    function test_construct()
    {
        $this->assertEquals('ChangeServiceStatus', $this->ChangeServiceStatusShell->name);
    }

    /**
     * Status will be changed from free-trial to read-only
     */
    function test_main_freeTrialToReadOnly()
    {
        // preparing datas.
        $this->_prepareTeamData([
            [
                'service_use_status'           => Team::SERVICE_USE_STATUS_FREE_TRIAL,
                'service_use_state_start_date' => '2017-01-01',
                'service_use_state_end_date'   => '2017-01-16',
            ],
            [
                'service_use_status'           => Team::SERVICE_USE_STATUS_FREE_TRIAL,
                'service_use_state_start_date' => '2017-01-02',
                'service_use_state_end_date'   => '2017-01-17',
            ],
            [
                'service_use_status'           => Team::SERVICE_USE_STATUS_FREE_TRIAL,
                'service_use_state_start_date' => '2017-01-03',
                'service_use_state_end_date'   => '2017-01-18',
            ],
        ]);

        $this->ChangeServiceStatusShell->params['targetExpireDate'] = '2017-01-17';
        $this->ChangeServiceStatusShell->main();

        $afterTeams = $this->Team->find('all');
        // comparing only service_use_status, service_use_state_start_date, service_use_state_end_date
        $statuses = Hash::extract($afterTeams, '{n}.Team.service_use_status');
        $startDates = Hash::extract($afterTeams, '{n}.Team.service_use_state_start_date');
        $endDates = Hash::extract($afterTeams, '{n}.Team.service_use_state_end_date');

        // It expected that 1st and 2nd team will be changed.
        // service_use_status
        $this->assertEquals(['2', '2', '0'], $statuses);
        // service_use_state_start_date
        $this->assertEquals(['2017-01-16', '2017-01-17', '2017-01-03'], $startDates);
        // service_use_state_end_date
        $this->assertEquals(['2017-02-15', '2017-02-16', '2017-01-18'], $endDates);
    }

    /**
     * Status will be changed from read-only to cannot-use-service
     */
    function test_main_readOnlyToCannotUseService()
    {
        // preparing datas.
        $this->_prepareTeamData([
            [
                'service_use_status'           => Team::SERVICE_USE_STATUS_READ_ONLY,
                'service_use_state_start_date' => '2017-01-01',
                'service_use_state_end_date'   => '2017-01-16',
            ],
            [
                'service_use_status'           => Team::SERVICE_USE_STATUS_READ_ONLY,
                'service_use_state_start_date' => '2017-01-02',
                'service_use_state_end_date'   => '2017-01-17',
            ],
            [
                'service_use_status'           => Team::SERVICE_USE_STATUS_READ_ONLY,
                'service_use_state_start_date' => '2017-01-03',
                'service_use_state_end_date'   => '2017-01-18',
            ],
        ]);

        $this->ChangeServiceStatusShell->params['targetExpireDate'] = '2017-01-17';
        $this->ChangeServiceStatusShell->main();

        $afterTeams = $this->Team->find('all');
        // comparing only service_use_status, service_use_state_start_date, service_use_state_end_date
        $statuses = Hash::extract($afterTeams, '{n}.Team.service_use_status');
        $startDates = Hash::extract($afterTeams, '{n}.Team.service_use_state_start_date');
        $endDates = Hash::extract($afterTeams, '{n}.Team.service_use_state_end_date');

        // It expected that 1st and 2nd team will be changed.
        // service_use_status
        $this->assertEquals(['3', '3', '2'], $statuses);
        // service_use_state_start_date
        $this->assertEquals(['2017-01-16', '2017-01-17', '2017-01-03'], $startDates);
        // service_use_state_end_date
        $this->assertEquals(['2017-04-16', '2017-04-17', '2017-01-18'], $endDates);
    }

    /**
     * Cannot-use-service status will be deleted
     */
    function test_main_CannotUseServiceWillBeDeleted()
    {
        // preparing datas.
        $this->_prepareTeamData([
            [
                'service_use_status'           => Team::SERVICE_USE_STATUS_CANNOT_USE,
                'service_use_state_start_date' => '2017-01-01',
                'service_use_state_end_date'   => '2017-01-16',
            ],
            [
                'service_use_status'           => Team::SERVICE_USE_STATUS_CANNOT_USE,
                'service_use_state_start_date' => '2017-01-02',
                'service_use_state_end_date'   => '2017-01-17',
            ],
            [
                'service_use_status'           => Team::SERVICE_USE_STATUS_CANNOT_USE,
                'service_use_state_start_date' => '2017-01-03',
                'service_use_state_end_date'   => '2017-01-18',
            ]
        ]);

        $this->ChangeServiceStatusShell->params['targetExpireDate'] = '2017-01-17';
        $this->ChangeServiceStatusShell->main();

        $afterTeams = $this->Team->find('all');
        // comparing only service_use_status, service_use_state_start_date, service_use_state_end_date
        $statuses = Hash::extract($afterTeams, '{n}.Team.service_use_status');
        $startDates = Hash::extract($afterTeams, '{n}.Team.service_use_state_start_date');
        $endDates = Hash::extract($afterTeams, '{n}.Team.service_use_state_end_date');

        // It expected that 1st and 2nd team will be changed.
        // service_use_status
        $this->assertEquals(['3'], $statuses);
        // service_use_state_start_date
        $this->assertEquals(['2017-01-03'], $startDates);
        // service_use_state_end_date
        $this->assertEquals(['2017-01-18'], $endDates);
    }

    function _prepareTeamData($data)
    {
        $this->deleteAllTeam();
        foreach ($data as $v) {
            $this->createTeam($v);
        }
    }
}
