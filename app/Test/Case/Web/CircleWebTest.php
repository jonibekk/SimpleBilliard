<?php

App::uses('GoalousWebTestCase', 'Test');
App::uses('PHPUnit_Extensions_Selenium2TestCase_Keys', 'Vendor/phpunit/phpunit-selenium/PHPUnit/Extensions/Selenium2TestCase');

/**
 * サークルのテスト
 *
 * @package GoalousWebTest
 * @version 2016/03/12
 *
 */
class CircleWebTest extends GoalousWebTestCase
{
    /**
     * CircleWebTest constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function setUp()
    {
        parent::setUp();
        $this->setBrowserUrl($this->url);
        $this->shareSession(true);
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    protected function login($url, $id, $pass)
    {
        $this->url($url);
        sleep(1);
        if (strpos($this->url(), '/users/login') === false) {
            return;
        }

        $email = $this->byName('data[User][email]');
        $email->clear();
        $email->value($id);

        $password = $this->byName('data[User][password]');
        $password->clear();
        $password->value($pass);

        $button = $this->byClassName('btn-primary');
        $this->moveto($button);
        $this->byId('UserLoginForm')->submit();
    }

    /**
     * #### サークルのモーダルを表示
     * @return int
     */
    public function testDispCircleModal()
    {
        $this->waitUntil(function() {
            $this->login($this->login_url, $this->email, $this->password);
            return true;
        }, 30000);

        sleep(2);

        $circles = $this->elements($this->using('css selector')->value('div.dashboard-circle-list-row-wrap'));
        $circle_count = count($circles);

        $link = $this->byXPath("//div[@class='dashboard-circle-list-footer']//a[.='サークルを見る']");
        $link->click();
        sleep(3);

        $this->assertRegExp('/\Aサークル \([0-9]+\)\Z/u', $this->byXPath("//div[10]/div/div/div[1]/h4")->text());
        $this->saveSceenshot('testDispCircleModal');

        return $circle_count;
    }

    /**
     * @depends testDispCircleModal
     *
     * @param $circle_count
     *
     * @return PHPUnit_Extensions_Selenium2TestCase_Element
     */
    public function testCircleModalEntryTab($circle_count)
    {
        $this->byXPath("//div[10]/div/div/ul/li[2]/a")->click();
        sleep(2);
        $modal_body = $this->byCssSelector('div.modal-body.modal-feed-body.tab-content');
        $tab = $modal_body->element($this->using('css selector')->value('div#tab2'));
        $classes = $tab->attribute('class');
        $this->assertTrue(strpos($classes, 'active') !== false);
        $circle_items = $tab->elements($this->using('css selector')->value('div.circle-item-row'));
        $this->assertEquals($circle_count, count($circle_items));
        $this->saveSceenshot('testCircleModalEntryTab');

        return $modal_body;
    }

    /**
     * @depends testCircleModalEntryTab
     *
     * @param $modal_body
     */
    public function testCircleModalUnEntryTab($modal_body)
    {
        $this->byXPath("//div[10]/div/div/ul/li[1]/a")->click();
        sleep(2);
        $tab = $modal_body->element($this->using('css selector')->value('div#tab1'));
        $classes = $tab->attribute('class');
        $this->assertTrue(strpos($classes, 'active') !== false);
        $this->saveSceenshot('testCircleModalUnEntryTab');

//        $circle_items = $tab->elements($this->using('css selector')->value('div.circle-item-row'));
//        $this->assertEquals($circle_count, count($circle_items));
        // 参加可能なサークルが無い
//        $this->assertEquals('参加していないサークルはありません。', $this->byCssSelector('#tab1')->text());
    }

    public function testCircleModalClose()
    {
        $this->byXPath("//div[10]/div/div/div[1]/button")->click();
        sleep(2);
        $this->assertFalse($this->byCssSelector('div.modal, .on, .fade')->displayed());
        $this->saveSceenshot('testCircleModalClose');
    }

    /**
     * #### サークル名をクリック
     * サークルの投稿一覧
     */
    public function testCirclePosts()
    {
        $circle = $this->byXPath("//div[2]/div[1]/div/div[2]/div[2]/div/div[1]/a[1]/p");
        $circle->click();
        $circle_name = $circle->text();
        sleep(5);

        $this->assertEquals($circle_name, $this->byCssSelector('span#circle-filter-menu-circle-name')->text());
        $this->assertRegExp('/circle_feed\/[0-9]+\Z/u', $this->url());
        $this->saveSceenshot('testCirclePosts');
    }

    /**
     * #### サークルフィードを下にスクロールして、もっと見るの読み込みを行う
     */
    public function testMoreRead()
    {
        $before_panels = $this->elements($this->using('css selector')->value('div.panel.panel-default'));
        $this->execute([
            'script' => 'window.scrollTo(0,document.body.scrollHeight);',
            'args' => [],
        ]);
        $this->waitUntil(function() use ($before_panels) {
            $panels = $this->elements($this->using('css selector')->value('div.panel.panel-default'));
            if (count($before_panels) < count($panels)) {
                $this->assertTrue(count($before_panels) < count($panels));
                $this->saveSceenshot('testMoreRead');
                return true;
            }
        }, 30000);
    }
}