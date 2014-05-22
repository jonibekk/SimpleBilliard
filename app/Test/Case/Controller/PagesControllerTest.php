<?php
App::uses('PagesController', 'Controller');

/**
 * PagesController Test Case
 * @method testAction($url = '', $options = array()) ControllerTestCase::_testAction
 *
 * @property User $User
 */
class PagesControllerTest extends ControllerTestCase
{
    public $autoFixtures = false;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.user',
        'app.image',
        'app.badge',
        'app.team',
        'app.comment_like',
        'app.comment',
        'app.post',
        'app.comment_mention',
        'app.given_badge',
        'app.post_like',
        'app.post_mention',
        'app.post_read',
        'app.images_post',
        'app.comment_read',
        'app.group',
        'app.team_member',
        'app.job_category',
        'app.invite',
        'app.notification',
        'app.thread',
        'app.message',
        'app.email',
        'app.oauth_token'
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->User = ClassRegistry::init('User');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->User);
        parent::tearDown();
    }

    /**
     * testHomepage method
     *
     * @return void
     */
    public function testHomepage()
    {
        $this->testAction('/', ['return' => 'contents']);
        $this->assertTextContains("Let's start Goalous!", $this->view, "ブラウザが日本語以外の場合、英語表記される");
        Configure::write('Config.language', 'ja');
        $this->testAction('/', ['return' => 'contents']);
        $this->assertTextContains("Goalousをはじめよう！", $this->view, "ブラウザが日本語の場合、日本語表記される");
        $this->testAction('/en/', ['return' => 'contents']);
        $this->assertTextContains("Let's start Goalous!", $this->view, "ブラウザが日本語の場合でも、言語で英語を指定した場合は英語表記される");
        Configure::write('Config.language', 'en');
        $this->testAction('/ja/', ['return' => 'contents']);
        $this->assertTextContains("Goalousをはじめよう！", $this->view, "ブラウザが英語の場合でも、言語で日本語を指定した場合は日本語表記される");
    }

    /**
     * testFeaturesPage method
     *
     * @return void
     */
    public function testFeaturesPage()
    {
        $this->User->useDbConfig = 'test';
        $this->loadFixtures('User');

        $this->testAction('/features', ['return' => 'contents']);
        $this->assertTextContains("Set up a goal", $this->view, "ブラウザが日本語以外の場合、英語表記される");
        Configure::write('Config.language', 'ja');
        $this->testAction('/features', ['return' => 'contents']);
        $this->assertTextContains("ゴールを作成する", $this->view, "ブラウザが日本語の場合、日本語表記される");
        $this->testAction('/en/features', ['return' => 'contents']);
        $this->assertTextContains("Set up a goal", $this->view, "ブラウザが日本語の場合でも、言語で英語を指定した場合は英語表記される");
        Configure::write('Config.language', 'en');
        $this->testAction('/ja/features', ['return' => 'contents']);
        $this->assertTextContains("ゴールを作成する", $this->view, "ブラウザが英語の場合でも、言語で日本語を指定した場合は日本語表記される");
    }

}
