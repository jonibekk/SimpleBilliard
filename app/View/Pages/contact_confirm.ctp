<?php /**
* PHP 5
* CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
* Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
* Licensed under The MIT License
* Redistributions of files must retain the above copyright notice.
*
* @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
* @link          http://cakephp.org CakePHP(tm) Project
* @package       Cake.View.Pages
* @since         CakePHP(tm) v 0.10.0.1076
* @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
* @var CodeCompletionView $this
* @var                    $user_count
* @var                    $top_lang
*/
?>
<?php $this->append('meta') ?>
<link rel="alternate" hreflang="ja" href="<?= $this->Html->url('/ja/contact_confirm') ?>"/>
<link rel="alternate" hreflang="en" href="<?= $this->Html->url('/en/contact_confirm') ?>"/>
<link rel="alternate" hreflang="x-default" href="<?= $this->Html->url('/contact_confirm') ?>"/>
<?php $this->end() ?>
<!-- START app/View/Pages/contact_confirm.ctp -->
<!-- ******CONTACT MAIN****** -->
<section id="contact-main" class="contact-main section">
    <div class="container text-center">
        <h2 class="title"><?= __d('lp', 'お問い合わせ内容確認') ?></h2>
        <p class="intro"><?= __d('lp', '内容をご確認のうえ、問題なければ送信をクリックしてください。') ?></p>
    </div><!--//container-->
</section>

<section class="container contact-form-section">
    <div class="row">
        <div class="col-md-8 col-md-offset-2 text-left">
            <form class="form-horizontal">
                <div class="form-group">
                    <label class="col-sm-4 control-label"><?= __d('lp', 'ご希望') ?></label>
                    <div class="col-sm-8">
                        <p class="form-control-static"><?= __d('lp', '訪問したい') ?></p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label"><?= __d('lp', '会社名') ?></label>
                    <div class="col-sm-8">
                        <p class="form-control-static"><?= __d('lp', '株式会社ISAO') ?></p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label"><?= __d('lp', 'お名前') ?></label>
                    <div class="col-sm-8">
                        <p class="form-control-static"><?= __d('lp', '中嶋あいみ') ?></p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label"><?= __d('lp', 'メールアドレス') ?></label>
                    <div class="col-sm-8">
                        <p class="form-control-static"><?= __d('lp', 'nakajimaa@isao.co.jp') ?></p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label"><?= __d('lp', 'お問い合わせ内容') ?></label>
                    <div class="col-sm-8">
                        <p class="form-control-static"><?= __d('lp', 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.') ?></p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label"><?= __d('lp', 'ご希望の営業担当者') ?></label>
                    <div class="col-sm-8">
                        <p class="form-control-static"><?= __d('lp', '湯川、吉岡') ?></p>
                    </div>
                </div>

                <button type="submit" class="btn btn-block btn-cta-secondary"><?= __d('lp', '戻る') ?></button>
                <button type="submit" class="btn btn-block btn-cta-primary"><?= __d('lp', '送信する') ?></button>
            </form><!--//form-->
        </div>
    </div><!--//row-->
</section><!--//contact--><!-- END app/View/Pages/contact_confirm.ctp -->
