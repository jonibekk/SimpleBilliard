<?php
App::uses('GoalousTestCase', 'Test');
App::uses('ConsoleOutput', 'Console');
App::uses('ShellDispatcher', 'Console');
App::uses('Shell', 'Console');
App::uses('Folder', 'Utility');
App::uses('AppController', 'Controller');
App::uses('ComponentCollection', 'Controller');
App::uses('SessionComponent', 'Controller/Component');
App::uses('NotifyBizComponent', 'Controller/Component');
App::uses('SetupGuideShell', 'Console/Command');
App::uses('GlRedis', 'Model');

class SetupGuideShellTest extends GoalousTestCase
{
    public $DataUpdateShell;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.team',
        'app.user',
        'app.email',
        'app.team_member',
        'app.member_type',
        'app.local_name',
        'notify_setting'
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $output = $this->getMock('ConsoleOutput', [], [], '', false);
        $error = $this->getMock('ConsoleOutput', [], [], '', false);
        $in = $this->getMock('ConsoleInput', [], [], '', false);
        $this->SetupGuideShell = new SetupGuideShell($output, $error, $in);
        $this->SetupGuideShell->startup();
        $this->User = ClassRegistry::init('User');
        $this->GlRedis = ClassRegistry::init('GlRedis');
        $this->GlRedis->changeDbSource('redis_test');
    }

    public function testConstruct()
    {
        $this->assertEquals('SetupGuide', $this->SetupGuideShell->name);
    }

    public function testMain()
    {
        $this->SetupGuideShell->main();
    }

    public function testIsNotifyDay()
    {
        $created = strtotime(AppUtil::dateYmdReformat('-10 day', "/"));
        $this->_saveUserRecords(['id' => $user_id = 1, 'setup_complete_flg' => false, 'created' => $created],
            $exec_delete_all = true);
        $notify_days = explode(",", SETUP_GUIDE_NOTIFY_DAYS);
        for ($i = 1; $i <= 10; $i++) {
            $this->GlRedis->deleteSetupGuideStatus($user_id);
            $this->GlRedis->saveSetupGuideStatus($user_id, [
                1                                     => 1,
                2                                     => 1,
                3                                     => 1,
                4                                     => 1,
                5                                     => 0,
                6                                     => 1,
                GlRedis::FIELD_SETUP_LAST_UPDATE_TIME => strtotime("-{$i} day")
            ]);
            // Case of notify days
            if (in_array($i, $notify_days)) {
                $this->assertTrue($this->SetupGuideShell->_isNotifyDay($user_id, $created));
                // Not notify days
            } else {
                $this->assertFalse($this->SetupGuideShell->_isNotifyDay($user_id, $created));
            }
        }
    }

    public function testIsNotifySendTime()
    {
        $notify_hour = SETUP_GUIDE_NOTIFY_HOUR;
        $this->assertTrue($this->SetupGuideShell->_isNotifySendTime($notify_hour));

        $not_notify_hour = SETUP_GUIDE_NOTIFY_HOUR - 2;
        $this->assertFalse($this->SetupGuideShell->_isNotifySendTime($not_notify_hour));

        $not_notify_hour = SETUP_GUIDE_NOTIFY_HOUR + 10;
        $this->assertFalse($this->SetupGuideShell->_isNotifySendTime($not_notify_hour));
    }

    public function testGetNotifyDataByTargetKey()
    {
        $data = $this->SetupGuideShell->_getNotifyDataByTargetKey(1);
        $this->assertTrue(strpos($data['messages']['mail'], 'Please input your profile to Goalous.') !== false);
        $this->assertEquals($data['messages']['push'], 'Please input your profile to Goalous.');
        $this->assertEquals($data['urls']['mail'], SETUP_GUIDE_NOTIFY_URL . '/setup/profile/image/?from=email');
        $this->assertEquals($data['urls']['push'], SETUP_GUIDE_NOTIFY_URL . '/setup/profile/image/?from=pushnotifi');

        $data = $this->SetupGuideShell->_getNotifyDataByTargetKey(2);
        $this->assertTrue(strpos($data['messages']['mail'],
                'Please install the Goalous mobile application and login.') !== false);
        $this->assertEquals($data['messages']['push'], 'Please install the Goalous mobile application and login.');
        $this->assertEquals($data['urls']['mail'], SETUP_GUIDE_NOTIFY_URL . '/setup/app/image/?from=email');
        $this->assertEquals($data['urls']['push'], SETUP_GUIDE_NOTIFY_URL . '/setup/app/image/?from=pushnotifi');

        $data = $this->SetupGuideShell->_getNotifyDataByTargetKey(3);
        $this->assertTrue(strpos($data['messages']['mail'], 'Please create your Goal.') !== false);
        $this->assertEquals($data['messages']['push'], 'Please create your Goal.');
        $this->assertEquals($data['urls']['mail'], SETUP_GUIDE_NOTIFY_URL . '/setup/goal/image/?from=email');
        $this->assertEquals($data['urls']['push'], SETUP_GUIDE_NOTIFY_URL . '/setup/goal/image/?from=pushnotifi');

        $data = $this->SetupGuideShell->_getNotifyDataByTargetKey(4);
        $this->assertTrue(strpos($data['messages']['mail'], 'Please action on Goalous.') !== false);
        $this->assertEquals($data['messages']['push'], 'Please action on Goalous.');
        $this->assertEquals($data['urls']['mail'], SETUP_GUIDE_NOTIFY_URL . '/setup/action/image/?from=email');
        $this->assertEquals($data['urls']['push'], SETUP_GUIDE_NOTIFY_URL . '/setup/action/image/?from=pushnotifi');

        $data = $this->SetupGuideShell->_getNotifyDataByTargetKey(5);
        $this->assertTrue(strpos($data['messages']['mail'], 'Please join a circle.') !== false);
        $this->assertEquals($data['messages']['push'], 'Please join a circle.');
        $this->assertEquals($data['urls']['mail'], SETUP_GUIDE_NOTIFY_URL . '/setup/circle/image/?from=email');
        $this->assertEquals($data['urls']['push'], SETUP_GUIDE_NOTIFY_URL . '/setup/circle/image/?from=pushnotifi');

        $data = $this->SetupGuideShell->_getNotifyDataByTargetKey(6);
        $this->assertTrue(strpos($data['messages']['mail'], 'Please post on Goalous.') !== false);
        $this->assertEquals($data['messages']['push'], 'Please post on Goalous.');
        $this->assertEquals($data['urls']['mail'], SETUP_GUIDE_NOTIFY_URL . '/setup/post/image/?from=email');
        $this->assertEquals($data['urls']['push'], SETUP_GUIDE_NOTIFY_URL . '/setup/post/image/?from=pushnotifi');
    }

    function _saveUserRecords($data, $exec_delete_all = false)
    {
        if ($exec_delete_all) {
            $this->User->deleteAll(['User.id >' => 0]);
        }
        $this->User->saveAll($data, ['validate' => false]);
        return;
    }

}
