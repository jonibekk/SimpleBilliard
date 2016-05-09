<?php

App::uses('GoalousWebTestCase', 'Test');

/**
 * コメント投稿に関するテスト
 *
 * @package GoalousWebTest
 * @version 2016/03/11
 *
 */
class CommentWebTest extends GoalousWebTestCase
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
        parent::setUpPage();
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     * #### コメントを投稿する
     * - 未入力の場合、コメントするボタンが無効化されている
     * - 入力した場合、コメントするボタンが有効化されている
     * - 入力したコメントと投稿後表示されたコメントが同一
     */
    public function testComment()
    {
        $this->waitUntil(function() {
            $this->login($this->login_url, $this->email, $this->password);
            return true;
        }, 50000);
        sleep(3);

        $comment_buttons = $this->elements($this->using('css selector')->value('a.feeds-post-comment-btn'));
        $comment_buttons[0]->click();
        sleep(1);

        $button = $this->byCssSelector('input.btn.btn-primary.submit-btn.comment-submit-button');
        $this->assertFalse($button->enabled());

        $comment = 'コメントテスト';
        $this->byName('data[Comment][body]')->value($comment);
        $this->assertTrue($button->enabled());
        $button->click();
        sleep(5);

        $comment_body = $this->elements($this->using('css selector')->value('div.comment-box'));
        $latest_comment = array_pop($comment_body);
        $comment_id = $latest_comment->attribute('comment-id');
        $post_comment = $this->byId('CommentTextBody_' . $comment_id);

        $this->assertEquals($comment, $post_comment->text());
        $this->saveSceenshot('testComment');
    }

    /**
     * #### コメント投稿のいいね！ボタンが作動するか
     * - クリック後、likedクラスが付与されているか
     * - いいね！カウンターが1増加しているか
     */
    public function testClickLikedButtonByComment()
    {
        $comment_buttons = $this->elements($this->using('css selector')->value('a.feeds-post-comment-btn'));
        $comment_buttons[0]->click();
        sleep(1);
        $button = $this->byCssSelector('input.btn.btn-primary.submit-btn.comment-submit-button');
        $comment = 'コメントテスト';
        $this->byName('data[Comment][body]')->value($comment);
        $button->click();
        sleep(5);

        $comment_body = $this->elements($this->using('css selector')->value('div.comment-box'));
        $latest_comment = array_pop($comment_body);

        $comment_id = $latest_comment->attribute('comment-id');
        $like = $latest_comment->element($this->using('css selector')->value('a.click-like'));
        $classes = $like->attribute('class');
        if (strpos($classes, 'liked') !== false) {
            $like->click();
            sleep(1);
        }
        $liked_counter = $this->byId('CommentLikeCount_' . $comment_id);
        $before_liked_count = $liked_counter->text();
        $like = $latest_comment->element($this->using('css selector')->value('a.click-like'));
        $like->click();
        sleep(3);
        $classes = $like->attribute('class');
        $this->assertTrue(strpos($classes, 'liked') !== false);
        $after_liked_count = $liked_counter->text();
        $this->assertEquals($before_liked_count + 1, $after_liked_count);
        $this->saveSceenshot('testClickLikedButtonByComment');
    }

    /**
     * #### ポストにコメントする
     * - 未入力の場合、コメントするボタンが無効化されている
     * - 入力した場合、コメントするボタンが有効化されている
     * - 入力したコメントと投稿後表示されたコメントが同一
     */
    public function testPostComment()
    {
        $this->moveToLatestNotify();

        $buttons = $this->byCssSelector('div.feeds-post-btns-wrap-left');
        $buttons->element($this->using('css selector')->value('a.feeds-post-comment-btn.trigger-click'))->click();
        sleep(1);

        $button = $this->byCssSelector('input.btn.btn-primary.submit-btn.comment-submit-button');
        $this->assertFalse($button->enabled());

        $comment = 'コメントテスト';
        $this->byName('data[Comment][body]')->value($comment);
        $this->assertTrue($button->enabled());
        $button->click();
        sleep(5);

        $comment_body = $this->elements($this->using('css selector')->value('div.comment-box'));
        $latest_comment = array_pop($comment_body);
        $comment_id = $latest_comment->attribute('comment-id');
        $post_comment = $this->byId('CommentTextBody_' . $comment_id);

        $this->assertEquals($comment, $post_comment->text());
        $this->saveSceenshot('testPostComment');
    }

    /**
     * #### ポストに画像付きコメントする
     */
    public function testPostCommentWithImage()
    {
        sleep(1);
        $this->moveToLatestNotify();

        $buttons = $this->byCssSelector('div.feeds-post-btns-wrap-left');
        $buttons->element($this->using('css selector')->value('a.feeds-post-comment-btn.trigger-click'))->click();
        sleep(1);

        $this->execute([
            'script' => 'window.scrollTo(0, document.documentElement.scrollHeight)',
            'args' => [],
        ]);
        sleep(1);

        $this->waitUntil(function() {
            if ($this->byCssSelector('input.btn.btn-primary.submit-btn.comment-submit-button')) {
                return true;
            }

            return false;
        }, 50000);
        sleep(1);

        $button = $this->byCssSelector('input.btn.btn-primary.submit-btn.comment-submit-button');
        $this->assertFalse($button->enabled());

        $comment = 'コメント画像添付テスト';
        $this->byName('data[Comment][body]')->value($comment);
        $this->assertTrue($button->enabled());

        // ファイルアップロードリンクをクリックしてイベント発火。dropzoneのセットアップ処理を行う。
        $script = '$("a.comment-file-attach-button").trigger(\'click\')';
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
        }, 30000);
        $button->click();
        sleep(5);

        $comment_body = $this->elements($this->using('css selector')->value('div.comment-box'));
        $latest_comment = array_pop($comment_body);
        $comment_id = $latest_comment->attribute('comment-id');
        $post_comment = $this->byId('CommentTextBody_' . $comment_id);
        $image_area = $latest_comment->elements($this->using('css selector')->value('div.file-info-wrap'));
        // アップロードした画像の表示方法が2通りある
//        if (count($image_area) > 0) {
//            $file_name = $image_area[0]->element($this->using('css selector')->value('a span'));
//            $this->assertEquals($image_name, $file_name->text());
//        } else {
            $thumbnail_area = $latest_comment->element($this->using('css selector')->value('div.feed_img_only_one'));
            $thumbnail = $thumbnail_area->elements($this->using('css selector')->value('a img'));
            $this->assertTrue(count($thumbnail) > 0);
//        }

        $this->assertEquals($comment, $post_comment->text());
        $this->saveSceenshot('testPostCommentWithImage');
    }

    protected function moveToLatestNotify()
    {
        $this->byXPath("//a[@id='click-header-bell']/i")->click();
        $this->byLinkText("すべて見る")->click();
        sleep(2);

        $post = $this->byXPath("//ul[@class='notify-page-cards']/li[1]/a/div/div[1]/span");
        $post->click();
        sleep(2);
    }
}