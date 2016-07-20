<?php
/**
 * CakeTestCase file
 * CakePHP(tm) Tests <http://book.cakephp.org/2.0/en/development/testing.html>
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://book.cakephp.org/2.0/en/development/testing.html CakePHP(tm) Tests
 * @package       Cake.TestSuite
 * @since         CakePHP(tm) v 1.2.0.4667
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('CakeTestCase', 'Test/Case/Web/Vendor');
App::uses('CakeFixtureManager', 'TestSuite/Fixture');
App::uses('CakeTestFixture', 'TestSuite/Fixture');

/**
 * GoalousWebTestCase class
 * PHPUnit_Extensions_Selenium2TestCaseを継承したCakeTestCaseを継承
 *
 * @package GoalousWebTest
 * @version 2016/03/11
 */
abstract class GoalousWebTestCase extends CakeTestCase
{
    /**
     * SeleniumNode(仮想環境)は192.168.x.1:4444の情報を持参していてHub(SeleniumServer)に接続を試みる
     * そのためHubのHOSTを192.168.50.1に設定する
     * 上記は仮想環境でSelenium実行時に別の環境に接続する場合のみ必要でローカル環境で全て完結するのであれば'localhost'(127.0.0.1)で問題ない
     *
     * @link http://gongo.hatenablog.com/entry/2014/10/29/105755
     */
    const HOST = '192.168.50.1';
    const SCREENSHOT_PATH = '/screenshot';

    protected $url = 'http://192.168.50.4';
    protected $login_url = '/users/login';
    protected $email = 'goalous.test03@gmail.com';
    protected $email2 = 'goalous.test04@gmail.com';
    protected $email3 = 'goalous.test05@gmail.com';
    protected $send_email = 'goalous.test02@gmail.com';
    protected $password = '12345678';

    public static $browsers = [
//        ['browser' => '*firefox', 'browserName' => 'firefox', 'sessionStrategy' => 'shared'],
['browser' => '*chrome', 'browserName' => 'chrome', 'sessionStrategy' => 'shared'],
//        ['browser' => '*iexplorer', 'browserName' => 'iexplorer', 'sessionStrategy' => 'shared'],
    ];

    public function __construct()
    {
        $this->setHost(self::HOST);
        $this->setPort(4444);
        $this->shareSession(true);
        $this->setSeleniumServerRequestsTimeout(60);
        $firefox_profile = '/Case/Web/firefox-profile/profile.zip.b64';
        $this->setDesiredCapabilities([
            'firefox_profile' => file_get_contents(__DIR__ . $firefox_profile),
        ]);
    }

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        Cache::config('team_info', ['prefix' => 'test_cache_team_info:']);
        Cache::config('user_data', ['prefix' => 'test_cache_user_data:']);
    }

    /**
     * setUp()処理後に実行される
     */
    public function setUpPage()
    {
        $this->login($this->url . $this->login_url, $this->email, $this->password);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        Cache::clear(false, 'team_info');
        Cache::clear(false, 'user_data');
        parent::tearDown();
    }

    public function login($url, $id, $pass)
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
        sleep(5);
    }

    public function logout()
    {
        $this->byXPath('//a[@id=\'header-cog-dropdown\']/i')->click();
        sleep(2);
        $this->byXPath('//a[@class=\'header-nav-function-contents-logout\']')->click();
        sleep(3);
    }

    /**
     * スクリーンショットを保存する
     *
     * @param string $file_name
     */
    public function saveSceenshot($file_name = '')
    {
        $dir_path = __DIR__ . '/Case/Web/screenshot/' . date('Ymd') . '/' . get_class($this);
        $result = $this->makeDirectory($dir_path);
        if ($result === false) {
            return;
        }
        $screenshot = $this->currentScreenshot();
        $file_path = $dir_path . '/' . $file_name . '_' . date('YmdHis') . '.png';
        file_put_contents($file_path, $screenshot);
    }

    /**
     * 投稿が5件未満の際に投稿を追加する
     * もっと読み込むテストのために利用する
     *
     * @param int $num
     */
    public function setUpPost($num = 5)
    {
        $post_list = $this->byCssSelector('div#app-view-elements-feed-posts');
        $panels = $post_list->elements($this->using('css selector')->value('div.panel.panel-default'));
        if (count($panels) > 5) {
            return;
        }
        for ($i = 0; $i <= $num; $i++) {
            $this->addPost();
        }
    }

    /**
     * テスト失敗時にスクリーンショットを取得するため、onNotSuccessfulTest()をオーバーライド
     *
     * @param Exception $e
     */
    public function onNotSuccessfulTest(Exception $e)
    {
        if ($e instanceof PHPUnit_Framework_AssertionFailedError) {
            $dir_path = __DIR__ . '/Case/Web/failure-screenshot/' . date('Ymd') . '/' . get_class($this);
            $result = $this->makeDirectory($dir_path);
            if ($result === true) {
                $listener = new PHPUnit_Extensions_Selenium2TestCase_ScreenshotListener(
                    $dir_path // 保存先のフォルダ
                );
            }
            $listener->addFailure($this, $e, null);
        }

        parent::onNotSuccessfulTest($e);
    }

    protected function makeDirectory($path)
    {
        if (file_exists($path) === true) {
            return true;
        }

        return mkdir($path, 0777, true);
    }
}
