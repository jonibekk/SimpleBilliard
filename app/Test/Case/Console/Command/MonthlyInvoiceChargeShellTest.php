<?php
App::uses('GoalousTestCase', 'Test');
App::uses('ConsoleOutput', 'Console');
App::uses('ShellDispatcher', 'Console');
App::uses('Shell', 'Console');
App::uses('Folder', 'Utility');
App::uses('AppShell', 'Console/Command');
App::uses('MonthlyInvoiceChargeShell', 'Console/Command/Batch/Payment/Console/Command');

/**
 * Class MonthlyInvoiceChargeShellTest
 *
 * @property MonthlyInvoiceChargeShell $MonthlyInvoiceChargeShell
 */
class MonthlyInvoiceChargeShellTest extends GoalousTestCase
{
    public $MonthlyInvoiceChargeShell;
    public $PaymentService;
    public $ChargeHistory;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.user',
        'app.team',
        'app.team_member',
        'app.payment_setting',
        'app.invoice',
        'app.charge_history',
        'app.campaign_team',
        'app.price_plan_purchase_team',
        'app.mst_price_plan',
        'app.view_price_plan',
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

        $this->PaymentService = ClassRegistry::init('PaymentService');
        $this->ChargeHistory = ClassRegistry::init('ChargeHistory');

        $this->MonthlyInvoiceChargeShell = new MonthlyInvoiceChargeShell($output, $error, $in);
        $this->MonthlyInvoiceChargeShell->initialize();
        $this->MonthlyInvoiceChargeShell->startup();
    }

    function tearDown()
    {

    }

    function test_construct()
    {
        $this->assertEquals('MonthlyInvoiceCharge', $this->MonthlyInvoiceChargeShell->name);
    }

    function test_main()
    {
        $this->Team->deleteAll(['del_flg' => false]);
        $team = ['timezone' => 0];
        $usersCount = 10;
        $this->Team->current_team_id = $teamId;

        // Execute batch
        $this->MonthlyInvoiceChargeShell->main();
    }
}