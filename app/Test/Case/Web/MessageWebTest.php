<?php

App::uses('GoalousWebTestCase', 'Test');

/**
 * メッセージ関連の操作をテスト
 *
 * @package GoalousWebTest
 * @version 2016/03/11
 *
 */
class MessageWebTest extends GoalousWebTestCase
{
    /**
     * MessageWebTest constructor.
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

    protected function logout()
    {
        $this->byXPath('//a[@id=\'header-cog-dropdown\']/i')->click();
        sleep(2);
        $this->byXPath('//a[@class=\'header-nav-function-contents-logout\']')->click();
        sleep(3);
    }

    /**
     * #### メッセージの通知
     * - 通知が表示されているか
     */
    public function testMessageIcon()
    {
        $this->url($this->url.'/users/login');
        if (strpos($this->url(), '/users/login') === false) {
            $this->logout();
        }
        $this->sendMessage($this->send_email, 'D');
        sleep(5);
        $this->logout();

        $this->waitUntil(function() {
            $this->login($this->login_url, $this->email, $this->password);
            return true;
        }, 30000);

        sleep(5);

        $this->byXPath("//a[@id='click-header-message']/i")->click();
        sleep(3);
        $message = $this->byCssSelector('ul#message-dropdown');
        $messages = $message->elements($this->using('css selector')->value('li.notify-card-list.notify-card-unread.notify-dropdown-card'));
        $this->assertTrue(0 < count($messages));
        $this->saveSceenshot('testMessageIcon');
    }

    /**
     * #### メッセージの通知からメッセージに移動する
     * - 遷移元のメッセージタイトルと遷移先のメッセージタイトルが同一か
     */
    public function testMessageIconMoveToPost()
    {
        $message_drop_down = $this->byCssSelector('ul#message-dropdown');
        $heads = $message_drop_down->elements($this->using('css selector')->value('span.notify-card-head-target'));
        $heads_text = $heads[0]->text();
        $heads[0]->click();
        sleep(5);

        $title = $this->byXPath('//div[@id=\'app-webroot-template-message-detail\']/div/div[1]/div/span[1]');
        $this->assertEquals($title->text(), $heads_text);
        $this->saveSceenshot('testMessageIconMoveToPost');
    }

    /**
     * #### メッセージにいいね！する
     * - いいね！アイコン付きのコメントができているか
     */
    public function testMessageLiked()
    {
        $liked_button = $this->byXPath('//button[@id=\'like\']');
        $liked_button->click();
        sleep(2);

        $message_box = $this->byCssSelector('div#message_box');
        $comments = $message_box->elements($this->using('css selector')->value('div.comment-text'));
        $latest_comment = array_pop($comments);
        $icon = $latest_comment->elements($this->using('css selector')->value('i.fa.fa-thumbs-o-up.font_brownRed'));
        $this->assertTrue(0 < count($icon));
        $this->saveSceenshot('testMessageLiked');
    }

    /**
     * #### メッセージにコメントする
     * - 入力したメッセージと完了後のコメントが同一か
     */
    public function testMessageComment()
    {
        $message = 'メッセージテスト';
        $input = $this->byId('message_text_input');
        $input->click();
        $input->value($message);

        $submit = $this->byCssSelector('input.message-detail-reply-btn.btn.btn-primary.submit-btn');
        $submit->click();
        sleep(5);

        $message_box = $this->byId('message_box');
        $comments = $message_box->elements($this->using('css selector')->value('div.comment-text'));
        $latest_comment = array_pop($comments);
        $this->assertEquals($message, $latest_comment->text());
        $this->saveSceenshot('testMessageComment');
    }

    /**
     * #### メッセージに画像付きでコメントする
     */
    public function testMessageCommentWithImage()
    {
        $message = 'メッセージ画像添付テスト';
        $input = $this->byId('message_text_input');
        $input->click();
        $input->value($message);

        // ファイルアップロードリンクをクリックしてイベント発火。dropzoneのセットアップ処理を行う。
        $script = '$("div#messageReplyUploadFileButton").trigger(\'click\')';
        $this->execute(['script' => $script, 'args' => []]);

        // 画像ファイルをバイナリに変換
        $path = __DIR__.'/Files/150x150.png';
        $image = base64_encode(file_get_contents($path));
        $image_name = 'testfile.png';

        // JSを実行してDropzone経由でファイルアップロードする
        $script = "var base64Image = '" . $image . "';" .
            "function base64toBlob(r,e,n){e=e||\"\",n=n||512;for(var t=atob(r),a=[],o=0;o<t.length;o+=n){for(var l=t.slice(o,o+n),h=new Array(l.length),b=0;b<l.length;b++)h[b]=l.charCodeAt(b);var v=new Uint8Array(h);a.push(v)}var c=new Blob(a,{type:e});return c}" .
            "var blob = base64toBlob(base64Image, 'image/png');" .
            "blob.name = '" . $image_name . "';";
        $script .= 'var form = $(document).data(\'uploadFileForm\'); form[0].dropzone.addFile(blob);';
        $this->execute(['script' => $script, 'args' => []]);
        $this->waitUntil(function() {
            if ($this->byCssSelector('div.dz-complete')) {
                return true;
            }

            return false;
        }, 50000);
        sleep(1);

        $submit = $this->byCssSelector('input.message-detail-reply-btn.btn.btn-primary.submit-btn');
        $submit->click();
        sleep(5);

        $message_box = $this->byId('message_box');
        $comments = $message_box->elements($this->using('css selector')->value('div.comment-text'));
        $latest_comment = array_pop($comments);
        $this->assertEquals($message, $latest_comment->text());
        $this->saveSceenshot('testMessageCommentWithImage');
    }

    /**
     * #### メッセージの通知から一覧に移動する
     * - 一覧のタイトルが「すべてのメッセージ」になっているか
     */
    public function testMessageIconMoveToPostList()
    {
        $message_num = $this->byId('messageNum');
        $num = $message_num->elements($this->using('css selector')->value('span'));
        // メッセージが１件も無い場合はクリックした時点で一覧ページを表示する
        if ($num[0]->text() === 0) {
            $this->byXPath("//a[@id='click-header-message']/i")->click();
        } else {
            $this->byXPath("//a[@id='click-header-message']/i")->click();
            sleep(3);
            $this->byLinkText("すべて見る")->click();
        }
        sleep(3);

        $this->assertEquals('すべてのメッセージ', $this->byCssSelector('div.panel-heading.message-list-panel-head')->text());
        $this->saveSceenshot('testMessageIconMoveToPostList');
    }

    /**
     * @param $email
     * @param $to_name
     *
     * @return bool
     */
    protected function sendMessage($email, $to_name)
    {
        $this->waitUntil(function() use ($email) {
            $this->login($this->login_url, $email, $this->password);
            if ($this->byXPath("//ul[@id='CommonFormTabs']//a[.='メッセージ']")) {
                return true;
            }

            return false;
        }, 30000);

        $this->byXPath("//ul[@id='CommonFormTabs']//a[.='メッセージ']")->click();
        $to = $this->byCssSelector('#s2id_autogen1');
        $to->value($to_name);
        $this->waitUntil(function() {
            if ($this->byId('select2-drop')) {
                return true;
            }

            return false;
        }, 30000);
        sleep(3);

        $drop_menu = $this->byId('select2-drop');
        $option = $drop_menu->elements($this->using('css selector')->value('span.select2-item-txt'));
        $option[0]->click();

        $message = 'メッセージテスト';
        $body = $this->byCssSelector('#CommonMessageBody');
        $body->value($message);
        $this->waitUntil(function() {
            if ($this->byCssSelector('#MessageSubmit')) {
                return true;
            }

            return false;
        }, 30000);

        $submit = $this->byCssSelector('#MessageSubmit');
        $submit->click();
        sleep(2);

        return true;
    }
}