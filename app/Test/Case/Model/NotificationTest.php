<?php
App::uses('Notification', 'Model');

/**
 * Notification Test Case
 *
 * @property  Notification $Notification
 */
class NotificationTest extends CakeTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.notification',
        'app.member_type',
        'app.user', 'app.notify_setting',
        'app.team',
        'app.team_member',
        'app.notify_from_user',
        'app.notify_to_user',
        'app.group',
        'app.job_category',
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->Notification = ClassRegistry::init('Notification');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Notification);

        parent::tearDown();
    }

    function testGetTitle()
    {
        $from_user_names = ['aaa', 'bbb'];
        $count_num = 1;
        $item_name = json_encode(['ccc', 'ddd']);
        $this->Notification->getTitle(Notification::TYPE_FEED_POST, $from_user_names, $count_num, $item_name);
        $this->Notification->getTitle(Notification::TYPE_FEED_COMMENTED_ON_MY_COMMENTED_POST, $from_user_names,
                                      $count_num, $item_name);
        $this->Notification->getTitle(Notification::TYPE_FEED_COMMENTED_ON_MY_POST, $from_user_names, $count_num,
                                      $item_name);
        $this->Notification->getTitle(Notification::TYPE_CIRCLE_CHANGED_PRIVACY_SETTING, $from_user_names, $count_num,
                                      $item_name);
        $this->Notification->getTitle(Notification::TYPE_CIRCLE_USER_JOIN, $from_user_names, $count_num, $item_name);
        $this->Notification->getTitle(Notification::TYPE_CIRCLE_ADD_USER, $from_user_names, $count_num, $item_name);
        $this->Notification->getTitle(999, "abc", $count_num, $item_name);
    }
}
