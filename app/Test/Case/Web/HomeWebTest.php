<?php

App::uses('GoalousWebTestCase', 'Test');

/**
 * ホーム画面からの操作をテスト
 *
 * @package GoalousWebTest
 * @version 2016/03/11
 */
class HomeWebTest extends GoalousWebTestCase
{
    /**
     * HomeWebTest constructor.
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

    /**
     * setUp()処理後に実行される
     */
    public function setUpPage()
    {
        //ログイン回数の制限があるのでこのテスト用にユーザーを切り替える
        $this->email = $this->email2;
        parent::setUpPage();
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     * #### ホームにアクセスする
     *  - アクセスできるか
     *  - ニュースフィードが表示されているか
     */
    public function testAccessHome()
    {
        $this->addPost();
        $this->byCssSelector('img.header-logo-img')->click();
        sleep(3);

        $feeds = $this->byId('app-view-elements-feed-posts');
        $posts = $feeds->elements($this->using('css selector')->value('div.panel.panel-default'));
        $this->assertTrue(0 < count($posts));
        $this->saveSceenshot('testAccessHome');
    }

    /**
     * #### 投稿する
     *  - ホームから記事を投稿できるか
     */
    public function testPost()
    {
        $text = 'テスト';

        $this->byLinkText("投稿")->click();
        $this->assertFalse($this->byId('PostSubmit')->enabled());

        $this->byId("CommonPostBody")->click();
        $this->byId("PostShare")->click();

        $element = $this->byId("CommonPostBody");
        $element->click();
        $element->clear();
        $element->value("テスト");
        $this->assertTrue($this->byId('PostSubmit')->enabled());

        $button = $this->byClassName('btn-primary');
        $this->moveto($button);
        $this->byId("PostSubmit")->click();
        sleep(3);

        $post = $this->byXPath('//div[2]/div[2]/div[4]/div[1]/div[1]/div[2]');
        $this->assertEquals($text, $post->text());
        $this->saveSceenshot('testPost');
    }

    /**
     * #### いいね！ボタンをクリックする
     *  - いいね！ボタンをスタイルが変更しているか
     */
    public function testClickLikedButton()
    {
        $post_buttons = $this->byCssSelector('div.feeds-post-btns-wrap-left');
        $modal_link = $post_buttons->elements($this->using('css selector')->value('a'));
        $like_count_id = $modal_link[0]->attribute('like_count_id');
        $liked_counter = $this->byId($like_count_id);
        $before_liked_count = $liked_counter->text();
        $like = $this->elements($this->using('css selector')->value('a.click-like.feeds-post-like-btn'));
        $like[0]->click();

        $classes = $like[0]->attribute('class');
        $this->assertTrue(strpos($classes, 'liked') !== false);

        $after_liked_count = $liked_counter->text();
        $this->assertEquals($before_liked_count + 1, $after_liked_count);
        $this->saveSceenshot('testClickLikedButton');
    }

    /**
     * #### ホームフィードを下にスクロールして、もっと見るの読み込みを行う
     * - 「もっと見る」の読み込みがされているか
     */
    public function testMoreRead()
    {
        $this->setUpPost();

        $before_panels = $this->elements($this->using('css selector')->value('div.panel.panel-default'));
        $this->execute([
            'script' => 'window.scrollTo(0, document.documentElement.scrollHeight)',
            'args'   => [],
        ]);
        $this->waitUntil(function () use ($before_panels) {
            $panels = $this->elements($this->using('css selector')->value('div.panel.panel-default'));
            if (count($before_panels) < count($panels)) {
                $this->assertTrue(count($before_panels) < count($panels));
                $this->saveSceenshot('testMoreRead');
                return true;
            }

            return false;
        }, 3000);
        $this->execute([
            'script' => 'window.scrollTo(0, 0)',
            'args'   => [],
        ]);
        sleep(2);
    }

    /**
     * #### ベルマークの通知
     * - 通知が表示されているか
     */
    public function testBellMark()
    {
        $this->setUpNotification($this->send_email, $this->password);

        $this->byXPath("//a[@id='click-header-bell']/i")->click();
        sleep(2);
        $notify = $this->byCssSelector('ul.header-nav-notify-contents.notify-dropdown-cards');
        $notifies = $notify->elements($this->using('css selector')->value('li.notify-card-list.notify-dropdown-card'));
        $this->assertTrue(0 < count($notifies));
        $this->saveSceenshot('testBellMark');
    }

    /**
     * #### ベルマークの通知から1ポストに移動する
     * - 遷移元のポストタイトルと遷移先のポストタイトルが同一か
     */
    public function testBellMarkMoveToPost()
    {
        $this->setUpNotification($this->send_email, $this->password);

        $this->byXPath("//a[@id='click-header-bell']/i")->click();
        sleep(3);

        $notify = $this->byCssSelector('ul.header-nav-notify-contents.notify-dropdown-cards');
        $notifies = $notify->elements($this->using('css selector')->value('li.notify-card-list'));
        $heads = $notifies[0]->elements($this->using('css selector')->value('span.notify-card-head-target'));
        $heads_text = $heads[0]->text();
        $notifies[0]->click();
        sleep(5);

        $panel = $this->byCssSelector('div.posts-panel-body');
        $panel_head = $panel->elements($this->using('css selector')->value('span.font_14px.font_bold.font_verydark'));
        $this->assertEquals($panel_head[0]->text(), $heads_text);
        $this->saveSceenshot('testBellMarkMoveToPost');
    }

    /**
     * #### ベルマークの通知から一覧に移動する
     * - 一覧のタイトルが「すべてのお知らせ」になっているか
     */
    public function testBellMarkMoveToPostList()
    {
        sleep(1);
        $this->setUpNotification($this->send_email, $this->password);

        $this->byXPath("//a[@id='click-header-bell']/i")->click();
        $this->byLinkText("すべて見る")->click();
        sleep(5);

        $this->assertEquals('すべてのお知らせ', $this->byCssSelector('div.panel-heading')->text());
        $this->saveSceenshot('testBellMarkMoveToPostList');
    }

    /**
     * #### 一覧からポストに移動する
     * - 遷移元のポストタイトルと遷移先のポストタイトルが同一か
     */
    public function testPostListMoveToPost()
    {
        sleep(1);
        $this->setUpNotification($this->send_email, $this->password);

        $this->byXPath("//a[@id='click-header-bell']/i")->click();
        $this->byLinkText("すべて見る")->click();
        sleep(5);

        $post_card = $this->byCssSelector('ul.notify-page-cards');
        $posts = $post_card->elements($this->using('css selector')->value('li.notify-card-list'));
        $post_header = $posts[0]->elements($this->using('css selector')->value('span.notify-card-head-target'));

        $header_text = $post_header[0]->text();
        $posts[0]->click();
        $this->waitUntil(function () {
            if ($this->byXPath("//div[@id='container']/div[2]/div[1]/div[1]/div[1]/a/span")) {
                return true;
            }

            return false;
        }, 30000);

        $this->assertEquals($this->byXPath("//div[@id='container']/div[2]/div[1]/div[1]/div[1]/a/span")->text(),
            $header_text);
        $this->saveSceenshot('testPostListMoveToPost');
    }

    /**
     * #### ポストにいいね！する
     * - クリック後、 likedクラスが付与されているか
     * - いいね！カウンターが1増加しているか
     */
    public function testPostLiked()
    {
        $liked_button = $this->byCssSelector('div.feeds-post-btns-wrap-right');
        $liked_link = $liked_button->elements($this->using('css selector')
                                                   ->value('a.modal-ajax-get.feeds-post-btn-numbers-like'));
        $liked_counter = $liked_link[0]->element($this->using('css selector')->value('span'));
        $like = $this->byCssSelector('a.click-like.feeds-post-like-btn');
        $classes = $like->attribute('class');
        if (strpos($classes, 'liked') !== false) {
            $like->click();
            sleep(1);
        }
        $before_liked_count = $liked_counter->text();
        $like->click();

        $classes = $like->attribute('class');
        $this->assertTrue(strpos($classes, 'liked') !== false);

        $after_liked_count = $liked_counter->text();
        $this->assertEquals($before_liked_count + 1, $after_liked_count);
        $this->saveSceenshot('testPostLiked');
    }

    /**
     * #### 投稿する
     *  - ホームから記事を投稿できるか
     */
    public function testPostWithImage()
    {
        $this->url($this->url);
        sleep(3);
        $text = 'テスト';

        $this->byLinkText("投稿")->click();
        $this->assertFalse($this->byId('PostSubmit')->enabled());

        $this->byId("CommonPostBody")->click();
        $this->byId("PostShare")->click();

        $element = $this->byId("CommonPostBody");
        $element->click();
        $element->clear();
        $element->value("テスト");
        $this->assertTrue($this->byId('PostSubmit')->enabled());

        // ファイルアップロードリンクをクリックしてイベント発火。dropzoneのセットアップ処理を行う。
        $script = 'var $icon = $("a#PostUploadFileButton").find(\'i\');$icon.trigger(\'click\')';
        $this->execute(['script' => $script, 'args' => []]);

        // 画像ファイルをバイナリに変換
        $path = __DIR__ . '/Files/150x150.png';
        $image = base64_encode(file_get_contents($path));
        $image_name = 'testfile.png';

        // JSを実行してDropzone経由でファイルアップロードする
        $script = "var base64Image = '" . $image . "';" .
            "function base64toBlob(r,e,n){e=e||\"\",n=n||512;for(var t=atob(r),a=[],o=0;o<t.length;o+=n){for(var l=t.slice(o,o+n),h=new Array(l.length),b=0;b<l.length;b++)h[b]=l.charCodeAt(b);var v=new Uint8Array(h);a.push(v)}var c=new Blob(a,{type:e});return c}" .
            "var blob = base64toBlob(base64Image, 'image/png');" .
            "blob.name = '" . $image_name . "';";
        $script .= 'var form = $(document).data(\'uploadFileForm\'); form[0].dropzone.addFile(blob);';
        $this->execute(['script' => $script, 'args' => []]);
        sleep(3);

        $this->waitUntil(function () {
            if ($this->byCssSelector('div.dz-complete')) {
                return true;
            }

            return false;
        }, 5000);
        sleep(1);

        $button = $this->byClassName('btn-primary');
        $this->moveto($button);
        $this->byId("PostSubmit")->click();
        sleep(3);

        $post = $this->byXPath('//div[2]/div[2]/div[4]/div[1]/div[1]/div[2]');
        $this->assertEquals($text, $post->text());
        $this->saveSceenshot('testPost');
    }

    /**
     * 通知のテストのために別のユーザーが投稿する
     *
     * @param $id
     * @param $password
     */
    protected function setUpNotification($id, $password)
    {
        if ($this->isSetUpNotification() === true) {
            $this->changeLoginUser($id, $password);
            $this->addPost();
            sleep(5);
            $this->logout();
            sleep(3);
            $this->login($this->url . $this->login_url, $this->email, $this->password);
        }
    }

    /**
     * 投稿を追加する
     */
    protected function addPost()
    {
        $text = 'テスト';

        $this->byLinkText("投稿")->click();

        $element = $this->byId("CommonPostBody");
        $element->click();
        $element->clear();
        $element->value($text);

        $button = $this->byClassName('btn-primary');
        $this->moveto($button);
        $this->byId("PostSubmit")->click();
        sleep(3);
    }

    /**
     * ログインユーザーを変更する
     *
     * @param $id
     * @param $password
     */
    protected function changeLoginUser($id, $password)
    {
        $this->logout();

        $this->waitUntil(function () use ($id, $password) {
            $this->login($this->login_url, $id, $password);
            if ($this->byXPath("//ul[@id='CommonFormTabs']//a[.='投稿']")) {
                return true;
            }

            return false;
        }, 30000);
    }

    /**
     * 通知テストのセットアップをするか判断
     *
     * @return bool
     */
    protected function isSetUpNotification()
    {
        if ($this->isNotificationCountExists() === true) {
            return false;
        }

        if ($this->isNotificationCardExists() === true) {
            return false;
        }

        return true;
    }

    /**
     * 通知のカウントがあるかどうかを判断する
     *
     * @return bool
     */
    protected function isNotificationCountExists()
    {
        $bell_num = $this->byCssSelector('div#bellNum');
        $num = $bell_num->elements($this->using('css selector')->value('span'));

        if ($num[0]->text() === 0) {
            return false;
        }

        if ($num[0]->text() === '') {
            return false;
        }

        return true;
    }

    /**
     * 通知リストが存在するか
     * li.notify-card-list要素は通知リストが無い場合でも1つあるため1より大きい場合に存在すると判断
     *
     * @return bool
     */
    protected function isNotificationCardExists()
    {
        $this->byXPath("//a[@id='click-header-bell']/i")->click();
        sleep(2);
        $notify = $this->byCssSelector('ul.header-nav-notify-contents.notify-dropdown-cards');
        $notifies = $notify->elements($this->using('css selector')->value('li.notify-card-list'));
        // 再度クリックしてドロップダウンを閉じる
        $this->byXPath("//a[@id='click-header-bell']/i")->click();

        return count($notifies) > 1;
    }
}
