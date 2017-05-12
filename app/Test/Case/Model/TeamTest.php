<?php App::uses('GoalousTestCase', 'Test');
App::uses('Team', 'Model');

/**
 * Team Test Case
 *
 * @property Team $Team
 */
class TeamTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.local_name',
        'app.team',
        'app.user',
        'app.circle',
        'app.circle_member',
        'app.team_member',
        'app.term',
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->Team = ClassRegistry::init('Team');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Team);

        parent::tearDown();
    }

    function testAddNoData()
    {
        $res = $this->Team->add(['Team' => ['name' => null]], "test");
        $this->assertFalse($res, "[異常]チーム追加 データ不正");
    }

    function testAddSuccess()
    {
        $postData = [
            'Team' => [
                'name' => "test",
                'type' => 1
            ]
        ];
        $uid = '1';
        $res = $this->Team->add($postData, $uid);
        $this->assertTrue($res, "[正常]チーム追加");

        // チーム全体サークルが追加されているか
        $this->Team->Circle->current_team_id = $this->Team->getLastInsertID();
        $teamAllCircle = $this->Team->Circle->getTeamAllCircle();
        $this->assertEquals($this->Team->Circle->current_team_id, $teamAllCircle["Circle"]["team_id"]);
    }

    function testEmailsValidation()
    {
        $emails = "";
        $emails .= "aaaaaa";
        $postData = ['Team' => ['emails' => $emails]];
        $this->Team->set($postData);
        $this->assertFalse($this->Team->validates(), "[異常]メールアドレスリスト:単独のメアド");

        $emails = "";
        $emails .= "aaaaaa@aaa.com";
        $postData = ['Team' => ['emails' => $emails]];
        $this->Team->set($postData);
        $this->assertTrue($this->Team->validates(), "[正常]メールアドレスリスト:単独のメアド");

        $emails = "";
        $emails .= "aaa@aaa.com,aaa.com";
        $postData = ['Team' => ['emails' => $emails]];
        $this->Team->set($postData);
        $this->assertFalse($this->Team->validates(), "[異常]メールアドレスリスト:カンマ区切り一行のメアド");

        $emails = "";
        $emails .= "aaa@aaa.com,aaa@aaa.com";
        $postData = ['Team' => ['emails' => $emails]];
        $this->Team->set($postData);
        $this->assertTrue($this->Team->validates(), "[正常]メールアドレスリスト:カンマ区切り一行のメアド");

        $emails = "";
        $emails .= "aaa@aaa.com,aaa@aaa.com" . "\n";
        $emails .= "aaa.com,aaa@aaa.com" . "\n";
        $postData = ['Team' => ['emails' => $emails]];
        $this->Team->set($postData);
        $this->assertFalse($this->Team->validates(), "[異常]メールアドレスリスト:カンマ区切り複数行のメアド");

        $emails = "";
        $emails .= "aaa@aaa.com,aaa@aaa.com" . "\n";
        $emails .= "aaa@aaa.com,aaa@aaa.com" . "\n";
        $postData = ['Team' => ['emails' => $emails]];
        $this->Team->set($postData);
        $this->assertTrue($this->Team->validates(), "[正常]メールアドレスリスト:カンマ区切り複数行のメアド");

        $emails = "";
        $emails .= "aaa@aaa.com,aaa@aaa.com" . "\n\n";
        $emails .= "aaa@aaa.com,aaa@aaa.com" . "\n";
        $postData = ['Team' => ['emails' => $emails]];
        $this->Team->set($postData);
        $this->assertTrue($this->Team->validates(), "[正常]メールアドレスリスト:カンマ区切り複数行のメアド(空行あり)");

        $emails = "";
        $emails .= "aaa@aaa.com, aaa@aaa.com" . "\n\n";
        $emails .= "aaa@aaa.com, aaa@aaa.com" . "\n";
        $postData = ['Team' => ['emails' => $emails]];
        $this->Team->set($postData);
        $this->assertTrue($this->Team->validates(), "[正常]メールアドレスリスト:カンマ区切り複数行のメアド(半角スペース混入)");

        $emails = "";
        $emails .= "aaa@aaa.com,　aaa@aaa.com" . "\n\n";
        $emails .= "aaa@aaa.com,　aaa@aaa.com" . "\n";
        $postData = ['Team' => ['emails' => $emails]];
        $this->Team->set($postData);
        $this->assertTrue($this->Team->validates(), "[正常]メールアドレスリスト:カンマ区切り複数行のメアド(全角スペース混入)");

        $emails = "";
        $emails .= ",,," . "\n\n";
        $emails .= ",,," . "\n";
        $postData = ['Team' => ['emails' => $emails]];
        $this->Team->set($postData);
        $this->assertFalse($this->Team->validates(), "[異常]メールアドレスリスト:カンマ区切り複数行のメアド(データ0件)");
    }

    function testGetEmailListFromPost()
    {
        $postData = [];
        $res = $this->Team->getEmailListFromPost($postData);
        $this->assertNull($res, "[異常]テキストからメアド抽出:データなし");

        $emails = "";
        $emails .= ",,," . "\n\n";
        $emails .= ",,," . "\n";
        $postData = ['Team' => ['emails' => $emails]];
        $res = $this->Team->getEmailListFromPost($postData);
        $this->assertNull($res, "[異常]テキストからメアド抽出:validationError");

        $emails = "";
        $emails .= "aaa@aaa.com, aaa@aaa.com" . "\n\n";
        $emails .= "aaa@aaa.com, aaa@aaa.com" . "\n";
        $postData = ['Team' => ['emails' => $emails]];
        $res = $this->Team->getEmailListFromPost($postData);
        $actual = ["aaa@aaa.com"];
        $this->assertEquals($res, $actual, "[正常]テキストからメアド抽出:ダブりメアドを除去");

        $emails = "";
        $emails .= ", ,,," . "\n\n";
        $emails .= "aaa@aaa.com, bbb@aaa.com" . "\n";
        $postData = ['Team' => ['emails' => $emails]];
        $res = $this->Team->getEmailListFromPost($postData);
        $actual = ["aaa@aaa.com", "bbb@aaa.com"];
        $this->assertEquals($res, $actual, "[正常]テキストからメアド抽出:空を除去");

    }

    function testGetCurrentTeam()
    {
        // current_team_id がセットされてない場合
        $this->assertEmpty($this->Team->getCurrentTeam());

        // current_team_id がセットされている場合
        $this->_setDefault();
        $current_team = $this->Team->getCurrentTeam();
        $this->assertEquals($this->Team->current_team_id, $current_team['Team']['id']);
    }

    function testGetBorderMonthsOptions()
    {
        $actual = $this->Team->getBorderMonthsOptions();
        $this->assertNotEmpty($actual);
        $this->assertCount(4, $actual);
    }

    function testGetMonths()
    {
        $actual = $this->Team->getMonths();
        $this->assertNotEmpty($actual);
        $this->assertCount(13, $actual);
    }

    function testDeleteTeam()
    {
        $this->_setDefault();

        $team = $this->Team->findById(1);
        $this->assertNotEmpty($team);

        $res = $this->Team->deleteTeam(1);
        $this->assertTrue($res);

        $team = $this->Team->findById(1);
        $this->assertEmpty($team);
    }

    function testGetList()
    {
        $this->_setDefault();
        $this->Team->deleteAll(['1' => '1']);
        $this->Team->saveAll(
            [
                [
                    'id'   => 100,
                    'name' => 'test1',
                ],
                [
                    'id'   => 200,
                    'name' => 'test2',
                ]
            ]
        );
        $actual = $this->Team->getListWithTeamId();
        $expected = [
            (int)100 => '100_test1',
            (int)200 => '200_test2'
        ];

        $this->assertEquals($expected, $actual);

    }

    function testSaveEditTerm()
    {
        $this->_setDefault();
        $this->assertFalse($this->Team->saveEditTerm(1, ['start_term_month' => 'aaaa']));
        $this->Team->create();
        $this->assertFalse($this->Team->saveEditTerm(1, ['Team' => ['name' => 'test']]));
        $this->Team->Term->addTermData(Term::TYPE_CURRENT);
        $this->Team->Term->addTermData(Term::TYPE_NEXT);
        $this->Team->Term->id = $this->Team->Term->getCurrentTermId();
        $this->Team->Term->save(['evaluate_status' => 1]);
        $this->assertFalse($this->Team->saveEditTerm(1,
            ['Team' => ['change_from' => 1, 'start_term_month' => 1, 'border_months' => 1, 'timezone' => 9]]));
        $this->Team->Term->id = $this->Team->Term->getCurrentTermId();
        $this->Team->Term->save(['evaluate_status' => 0]);
        $this->assertTrue($this->Team->saveEditTerm(1,
            ['Team' => ['change_from' => 1, 'start_term_month' => 1, 'border_months' => 1, 'timezone' => 9]]));

    }

    function test_updateTermSettings()
    {
        $teamId = $this->createTeam(['start_term_month' => 4, 'border_months' => 10]);
        $this->setDefaultTeamIdAndUid(1, $teamId);
        $this->Team->updateTermSettings(1, 4);
        $newTeam = $this->Team->getById($teamId);
        $this->assertEquals($newTeam['start_term_month'], 1);
        $this->assertEquals($newTeam['border_months'], 4);
    }

    function _setDefault()
    {
        $this->Team->my_uid = 1;
        $this->Team->me['timezone'] = 9;
        $this->Team->current_team_id = 1;
        $this->Team->Term->current_team_id = 1;
        $this->Team->Term->my_uid = 1;
    }

}
