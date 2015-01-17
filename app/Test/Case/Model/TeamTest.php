<?php
App::uses('Team', 'Model');

/**
 * Team Test Case
 *
 * @property Team $Team
 */
class TeamTest extends CakeTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.local_name',
        'app.team',
        'app.image',
        'app.user', 'app.notify_setting',
        'app.badge',
        'app.post',
        //'app.goal',
        'app.comment_mention',
        'app.comment',
        'app.comment_like',
        'app.comment_read',
        'app.given_badge',
        'app.post_like',
        'app.post_mention',
        'app.post_read',
        'app.images_post',
        'app.group',
        'app.team_member',
        'app.job_category',
        'app.invite',
        'app.notification',
        'app.thread'
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

    function testGetTermStartDate()
    {
        $this->setDefault();
        $this->Team->current_term_start_date = strtotime('2014-04-01 00:00:00');
        $this->Team->getTermStartDate();
    }

    function testGetTermEndDate()
    {
        $this->setDefault();
        $this->Team->current_term_end_date = strtotime('2014-09-31 00:00:00');
        $this->Team->getTermEndDate();
    }

    function testSetCurrentTermStartEnd()
    {
        $this->setDefault();

        $this->Team->setCurrentTermStartEnd();

        $this->Team->current_term_start_date = strtotime('2014-04-01 00:00:00');
        $this->Team->current_term_end_date = strtotime('2014-09-31 00:00:00');
        $this->Team->setCurrentTermStartEnd();

        $this->Team->current_term_start_date = null;
        $this->Team->current_term_end_date = null;
        $team = [
            'Team' => [
                'start_term_month' => 4,
                'border_months'    => 6,
            ]
        ];
        $this->Team->current_team = $team;

        $this->Team->setCurrentTermStartEnd();

        $this->Team->current_term_start_date = strtotime('2014-04-01 00:00:00');
        $this->Team->current_term_end_date = null;
        $this->Team->setCurrentTermStartEnd();

        $this->Team->current_term_start_date = null;
        $this->Team->current_term_end_date = strtotime('2014-09-31 00:00:00');
        $this->Team->setCurrentTermStartEnd();

        //期間内の場合
        $this->Team->current_term_start_date = null;
        $this->Team->current_term_end_date = null;
        $team = [
            'Team' => [
                'start_term_month' => 1,
                'border_months'    => 12,
            ]
        ];
        $this->Team->current_team = $team;
        $this->Team->setCurrentTermStartEnd();

        //期間の開始より現在が前の場合
        $this->Team->current_term_start_date = null;
        $this->Team->current_term_end_date = null;
        $team = [
            'Team' => [
                'start_term_month' => date('n', strtotime('+1 month')),
                'border_months'    => 1,
            ]
        ];
        $this->Team->current_team = $team;
        $this->Team->setCurrentTermStartEnd();
    }

    function testSetCurrentTermStartEndFromParam()
    {
        $this->setDefault();
        $time_offset = $this->Team->me['timezone'] * 60 * 60;

        //no target_date
        $this->Team->current_term_start_date = null;
        $this->Team->current_term_end_date = null;
        $this->Team->setCurrentTermStartEndFromParam(1, 6);

        ////期間半年
        $this->Team->current_term_start_date = null;
        $this->Team->current_term_end_date = null;
        $this->Team->setCurrentTermStartEndFromParam(1, 6, strtotime('2014/1/1') - $time_offset);
        $this->assertEquals('2014/01/01 00:00:00',
                            date('Y/m/d H:i:s', $this->Team->current_term_start_date + $time_offset));
        $this->assertEquals('2014/07/01 00:00:00',
                            date('Y/m/d H:i:s', $this->Team->current_term_end_date + $time_offset));

        $this->Team->current_term_start_date = null;
        $this->Team->current_term_end_date = null;
        $this->Team->setCurrentTermStartEndFromParam(12, 6, strtotime('2014/1/1') - $time_offset);
        $this->assertEquals('2013/12/01 00:00:00',
                            date('Y/m/d H:i:s', $this->Team->current_term_start_date + $time_offset));
        $this->assertEquals('2014/06/01 00:00:00',
                            date('Y/m/d H:i:s', $this->Team->current_term_end_date + $time_offset));

        $this->Team->current_term_start_date = null;
        $this->Team->current_term_end_date = null;
        $this->Team->setCurrentTermStartEndFromParam(1, 6, strtotime('2014/12/31') - $time_offset);
        $this->assertEquals('2014/07/01 00:00:00',
                            date('Y/m/d H:i:s', $this->Team->current_term_start_date + $time_offset));
        $this->assertEquals('2015/01/01 00:00:00',
                            date('Y/m/d H:i:s', $this->Team->current_term_end_date + $time_offset));

        $this->Team->current_term_start_date = null;
        $this->Team->current_term_end_date = null;
        $this->Team->setCurrentTermStartEndFromParam(12, 6, strtotime('2014/12/31') - $time_offset);
        $this->assertEquals('2014/12/01 00:00:00',
                            date('Y/m/d H:i:s', $this->Team->current_term_start_date + $time_offset));
        $this->assertEquals('2015/06/01 00:00:00',
                            date('Y/m/d H:i:s', $this->Team->current_term_end_date + $time_offset));

        $this->Team->current_term_start_date = null;
        $this->Team->current_term_end_date = null;
        $this->Team->setCurrentTermStartEndFromParam(1, 6, strtotime('2016/01/01') - $time_offset);
        $this->assertEquals('2016/01/01 00:00:00',
                            date('Y/m/d H:i:s', $this->Team->current_term_start_date + $time_offset));
        $this->assertEquals('2016/07/01 00:00:00',
                            date('Y/m/d H:i:s', $this->Team->current_term_end_date + $time_offset));

        $this->Team->current_term_start_date = null;
        $this->Team->current_term_end_date = null;
        $this->Team->setCurrentTermStartEndFromParam(1, 6, strtotime('2016/12/31') - $time_offset);
        $this->assertEquals('2016/07/01 00:00:00',
                            date('Y/m/d H:i:s', $this->Team->current_term_start_date + $time_offset));
        $this->assertEquals('2017/01/01 00:00:00',
                            date('Y/m/d H:i:s', $this->Team->current_term_end_date + $time_offset));

        $this->Team->current_term_start_date = null;
        $this->Team->current_term_end_date = null;
        $this->Team->setCurrentTermStartEndFromParam(3, 6, strtotime('2016/2/29') - $time_offset);
        $this->assertEquals('2015/09/01 00:00:00',
                            date('Y/m/d H:i:s', $this->Team->current_term_start_date + $time_offset));
        $this->assertEquals('2016/03/01 00:00:00',
                            date('Y/m/d H:i:s', $this->Team->current_term_end_date + $time_offset));

        $this->Team->current_term_start_date = null;
        $this->Team->current_term_end_date = null;
        $this->Team->setCurrentTermStartEndFromParam(3, 6, strtotime('2016/2/28') - $time_offset);
        $this->assertEquals('2015/09/01 00:00:00',
                            date('Y/m/d H:i:s', $this->Team->current_term_start_date + $time_offset));
        $this->assertEquals('2016/03/01 00:00:00',
                            date('Y/m/d H:i:s', $this->Team->current_term_end_date + $time_offset));

        $this->Team->current_term_start_date = null;
        $this->Team->current_term_end_date = null;
        $this->Team->setCurrentTermStartEndFromParam(3, 6, strtotime('2016/3/1') - $time_offset);
        $this->assertEquals('2016/03/01 00:00:00',
                            date('Y/m/d H:i:s', $this->Team->current_term_start_date + $time_offset));
        $this->assertEquals('2016/09/01 00:00:00',
                            date('Y/m/d H:i:s', $this->Team->current_term_end_date + $time_offset));

        ////期間四半期
        $this->Team->current_term_start_date = null;
        $this->Team->current_term_end_date = null;
        $this->Team->setCurrentTermStartEndFromParam(1, 3, strtotime('2014/1/1') - $time_offset);
        $this->assertEquals('2014/01/01 00:00:00',
                            date('Y/m/d H:i:s', $this->Team->current_term_start_date + $time_offset));
        $this->assertEquals('2014/04/01 00:00:00',
                            date('Y/m/d H:i:s', $this->Team->current_term_end_date + $time_offset));

        $this->Team->current_term_start_date = null;
        $this->Team->current_term_end_date = null;
        $this->Team->setCurrentTermStartEndFromParam(12, 3, strtotime('2014/1/1') - $time_offset);
        $this->assertEquals('2013/12/01 00:00:00',
                            date('Y/m/d H:i:s', $this->Team->current_term_start_date + $time_offset));
        $this->assertEquals('2014/03/01 00:00:00',
                            date('Y/m/d H:i:s', $this->Team->current_term_end_date + $time_offset));

        $this->Team->current_term_start_date = null;
        $this->Team->current_term_end_date = null;
        $this->Team->setCurrentTermStartEndFromParam(1, 3, strtotime('2014/12/31') - $time_offset);
        $this->assertEquals('2014/10/01 00:00:00',
                            date('Y/m/d H:i:s', $this->Team->current_term_start_date + $time_offset));
        $this->assertEquals('2015/01/01 00:00:00',
                            date('Y/m/d H:i:s', $this->Team->current_term_end_date + $time_offset));

        $this->Team->current_term_start_date = null;
        $this->Team->current_term_end_date = null;
        $this->Team->setCurrentTermStartEndFromParam(12, 3, strtotime('2014/12/31') - $time_offset);
        $this->assertEquals('2014/12/01 00:00:00',
                            date('Y/m/d H:i:s', $this->Team->current_term_start_date + $time_offset));
        $this->assertEquals('2015/03/01 00:00:00',
                            date('Y/m/d H:i:s', $this->Team->current_term_end_date + $time_offset));

        $this->Team->current_term_start_date = null;
        $this->Team->current_term_end_date = null;
        $this->Team->setCurrentTermStartEndFromParam(1, 3, strtotime('2016/01/01') - $time_offset);
        $this->assertEquals('2016/01/01 00:00:00',
                            date('Y/m/d H:i:s', $this->Team->current_term_start_date + $time_offset));
        $this->assertEquals('2016/04/01 00:00:00',
                            date('Y/m/d H:i:s', $this->Team->current_term_end_date + $time_offset));

        $this->Team->current_term_start_date = null;
        $this->Team->current_term_end_date = null;
        $this->Team->setCurrentTermStartEndFromParam(1, 3, strtotime('2016/12/31') - $time_offset);
        $this->assertEquals('2016/10/01 00:00:00',
                            date('Y/m/d H:i:s', $this->Team->current_term_start_date + $time_offset));
        $this->assertEquals('2017/01/01 00:00:00',
                            date('Y/m/d H:i:s', $this->Team->current_term_end_date + $time_offset));

        $this->Team->current_term_start_date = null;
        $this->Team->current_term_end_date = null;
        $this->Team->setCurrentTermStartEndFromParam(3, 3, strtotime('2016/2/29') - $time_offset);
        $this->assertEquals('2015/12/01 00:00:00',
                            date('Y/m/d H:i:s', $this->Team->current_term_start_date + $time_offset));
        $this->assertEquals('2016/03/01 00:00:00',
                            date('Y/m/d H:i:s', $this->Team->current_term_end_date + $time_offset));

        $this->Team->current_term_start_date = null;
        $this->Team->current_term_end_date = null;
        $this->Team->setCurrentTermStartEndFromParam(3, 3, strtotime('2016/2/28') - $time_offset);
        $this->assertEquals('2015/12/01 00:00:00',
                            date('Y/m/d H:i:s', $this->Team->current_term_start_date + $time_offset));
        $this->assertEquals('2016/03/01 00:00:00',
                            date('Y/m/d H:i:s', $this->Team->current_term_end_date + $time_offset));

        $this->Team->current_term_start_date = null;
        $this->Team->current_term_end_date = null;
        $this->Team->setCurrentTermStartEndFromParam(3, 3, strtotime('2016/3/1') - $time_offset);
        $this->assertEquals('2016/03/01 00:00:00',
                            date('Y/m/d H:i:s', $this->Team->current_term_start_date + $time_offset));
        $this->assertEquals('2016/06/01 00:00:00',
                            date('Y/m/d H:i:s', $this->Team->current_term_end_date + $time_offset));
        ////期間１年
        $this->Team->current_term_start_date = null;
        $this->Team->current_term_end_date = null;
        $this->Team->setCurrentTermStartEndFromParam(1, 12, strtotime('2014/1/1') - $time_offset);
        $this->assertEquals('2014/01/01 00:00:00',
                            date('Y/m/d H:i:s', $this->Team->current_term_start_date + $time_offset));
        $this->assertEquals('2015/01/01 00:00:00',
                            date('Y/m/d H:i:s', $this->Team->current_term_end_date + $time_offset));

        $this->Team->current_term_start_date = null;
        $this->Team->current_term_end_date = null;
        $this->Team->setCurrentTermStartEndFromParam(12, 12, strtotime('2014/1/1') - $time_offset);
        $this->assertEquals('2013/12/01 00:00:00',
                            date('Y/m/d H:i:s', $this->Team->current_term_start_date + $time_offset));
        $this->assertEquals('2014/12/01 00:00:00',
                            date('Y/m/d H:i:s', $this->Team->current_term_end_date + $time_offset));

        $this->Team->current_term_start_date = null;
        $this->Team->current_term_end_date = null;
        $this->Team->setCurrentTermStartEndFromParam(1, 12, strtotime('2014/12/31') - $time_offset);
        $this->assertEquals('2014/01/01 00:00:00',
                            date('Y/m/d H:i:s', $this->Team->current_term_start_date + $time_offset));
        $this->assertEquals('2015/01/01 00:00:00',
                            date('Y/m/d H:i:s', $this->Team->current_term_end_date + $time_offset));

        $this->Team->current_term_start_date = null;
        $this->Team->current_term_end_date = null;
        $this->Team->setCurrentTermStartEndFromParam(12, 12, strtotime('2014/12/31') - $time_offset);
        $this->assertEquals('2014/12/01 00:00:00',
                            date('Y/m/d H:i:s', $this->Team->current_term_start_date + $time_offset));
        $this->assertEquals('2015/12/01 00:00:00',
                            date('Y/m/d H:i:s', $this->Team->current_term_end_date + $time_offset));

        $this->Team->current_term_start_date = null;
        $this->Team->current_term_end_date = null;
        $this->Team->setCurrentTermStartEndFromParam(1, 12, strtotime('2016/01/01') - $time_offset);
        $this->assertEquals('2016/01/01 00:00:00',
                            date('Y/m/d H:i:s', $this->Team->current_term_start_date + $time_offset));
        $this->assertEquals('2017/01/01 00:00:00',
                            date('Y/m/d H:i:s', $this->Team->current_term_end_date + $time_offset));

        $this->Team->current_term_start_date = null;
        $this->Team->current_term_end_date = null;
        $this->Team->setCurrentTermStartEndFromParam(1, 12, strtotime('2016/12/31') - $time_offset);
        $this->assertEquals('2016/01/01 00:00:00',
                            date('Y/m/d H:i:s', $this->Team->current_term_start_date + $time_offset));
        $this->assertEquals('2017/01/01 00:00:00',
                            date('Y/m/d H:i:s', $this->Team->current_term_end_date + $time_offset));

        $this->Team->current_term_start_date = null;
        $this->Team->current_term_end_date = null;
        $this->Team->setCurrentTermStartEndFromParam(3, 12, strtotime('2016/2/29') - $time_offset);
        $this->assertEquals('2015/03/01 00:00:00',
                            date('Y/m/d H:i:s', $this->Team->current_term_start_date + $time_offset));
        $this->assertEquals('2016/03/01 00:00:00',
                            date('Y/m/d H:i:s', $this->Team->current_term_end_date + $time_offset));

        $this->Team->current_term_start_date = null;
        $this->Team->current_term_end_date = null;
        $this->Team->setCurrentTermStartEndFromParam(3, 12, strtotime('2016/2/28') - $time_offset);
        $this->assertEquals('2015/03/01 00:00:00',
                            date('Y/m/d H:i:s', $this->Team->current_term_start_date + $time_offset));
        $this->assertEquals('2016/03/01 00:00:00',
                            date('Y/m/d H:i:s', $this->Team->current_term_end_date + $time_offset));

        $this->Team->current_term_start_date = null;
        $this->Team->current_term_end_date = null;
        $this->Team->setCurrentTermStartEndFromParam(3, 12, strtotime('2016/3/1') - $time_offset);
        $this->assertEquals('2016/03/01 00:00:00',
                            date('Y/m/d H:i:s', $this->Team->current_term_start_date + $time_offset));
        $this->assertEquals('2017/03/01 00:00:00',
                            date('Y/m/d H:i:s', $this->Team->current_term_end_date + $time_offset));

        ////期間２年
        $this->Team->current_term_start_date = null;
        $this->Team->current_term_end_date = null;
        $this->Team->setCurrentTermStartEndFromParam(1, 24, strtotime('2014/1/1') - $time_offset);
        $this->assertEquals('2014/01/01 00:00:00',
                            date('Y/m/d H:i:s', $this->Team->current_term_start_date + $time_offset));
        $this->assertEquals('2016/01/01 00:00:00',
                            date('Y/m/d H:i:s', $this->Team->current_term_end_date + $time_offset));

        $this->Team->current_term_start_date = null;
        $this->Team->current_term_end_date = null;
        $this->Team->setCurrentTermStartEndFromParam(12, 24, strtotime('2014/1/1') - $time_offset);
        $this->assertEquals('2012/12/01 00:00:00',
                            date('Y/m/d H:i:s', $this->Team->current_term_start_date + $time_offset));
        $this->assertEquals('2014/12/01 00:00:00',
                            date('Y/m/d H:i:s', $this->Team->current_term_end_date + $time_offset));

        $this->Team->current_term_start_date = null;
        $this->Team->current_term_end_date = null;
        $this->Team->setCurrentTermStartEndFromParam(1, 24, strtotime('2014/12/31') - $time_offset);
        $this->assertEquals('2014/01/01 00:00:00',
                            date('Y/m/d H:i:s', $this->Team->current_term_start_date + $time_offset));
        $this->assertEquals('2016/01/01 00:00:00',
                            date('Y/m/d H:i:s', $this->Team->current_term_end_date + $time_offset));

        $this->Team->current_term_start_date = null;
        $this->Team->current_term_end_date = null;
        $this->Team->setCurrentTermStartEndFromParam(12, 24, strtotime('2014/12/31') - $time_offset);
        $this->assertEquals('2014/12/01 00:00:00',
                            date('Y/m/d H:i:s', $this->Team->current_term_start_date + $time_offset));
        $this->assertEquals('2016/12/01 00:00:00',
                            date('Y/m/d H:i:s', $this->Team->current_term_end_date + $time_offset));

        $this->Team->current_term_start_date = null;
        $this->Team->current_term_end_date = null;
        $this->Team->setCurrentTermStartEndFromParam(1, 24, strtotime('2016/01/01') - $time_offset);
        $this->assertEquals('2016/01/01 00:00:00',
                            date('Y/m/d H:i:s', $this->Team->current_term_start_date + $time_offset));
        $this->assertEquals('2018/01/01 00:00:00',
                            date('Y/m/d H:i:s', $this->Team->current_term_end_date + $time_offset));

        $this->Team->current_term_start_date = null;
        $this->Team->current_term_end_date = null;
        $this->Team->setCurrentTermStartEndFromParam(1, 24, strtotime('2016/12/31') - $time_offset);
        $this->assertEquals('2016/01/01 00:00:00',
                            date('Y/m/d H:i:s', $this->Team->current_term_start_date + $time_offset));
        $this->assertEquals('2018/01/01 00:00:00',
                            date('Y/m/d H:i:s', $this->Team->current_term_end_date + $time_offset));

        $this->Team->current_term_start_date = null;
        $this->Team->current_term_end_date = null;
        $this->Team->setCurrentTermStartEndFromParam(3, 24, strtotime('2016/2/29') - $time_offset);
        $this->assertEquals('2014/03/01 00:00:00',
                            date('Y/m/d H:i:s', $this->Team->current_term_start_date + $time_offset));
        $this->assertEquals('2016/03/01 00:00:00',
                            date('Y/m/d H:i:s', $this->Team->current_term_end_date + $time_offset));

        $this->Team->current_term_start_date = null;
        $this->Team->current_term_end_date = null;
        $this->Team->setCurrentTermStartEndFromParam(3, 24, strtotime('2016/2/28') - $time_offset);
        $this->assertEquals('2014/03/01 00:00:00',
                            date('Y/m/d H:i:s', $this->Team->current_term_start_date + $time_offset));
        $this->assertEquals('2016/03/01 00:00:00',
                            date('Y/m/d H:i:s', $this->Team->current_term_end_date + $time_offset));

        $this->Team->current_term_start_date = null;
        $this->Team->current_term_end_date = null;
        $this->Team->setCurrentTermStartEndFromParam(3, 24, strtotime('2016/3/1') - $time_offset);
        $this->assertEquals('2016/03/01 00:00:00',
                            date('Y/m/d H:i:s', $this->Team->current_term_start_date + $time_offset));
        $this->assertEquals('2018/03/01 00:00:00',
                            date('Y/m/d H:i:s', $this->Team->current_term_end_date + $time_offset));
    }

    function setDefault()
    {
        $this->Team->my_uid = 1;
        $this->Team->me['timezone'] = 9;
        $this->Team->current_team_id = 1;

    }

}
