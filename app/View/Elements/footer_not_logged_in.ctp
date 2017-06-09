<?php
/**
 * Created by PhpStorm.
 * User: naru0504
 * Date: 2/5/16
 * Time: 5:07 PM
 *
 * @var CodeCompletionView $this
 * @var                    $title_for_layout string
 * @var                    $this             View
 */
?>
<?php
if (!isset($top_lang)) {
    $top_lang = null;
}
?>
<?= $this->App->viewStartComment() ?>
<footer class="footer <?= $is_mb_app ? 'hide' : null ?>">
    <div class="footer-content">
        <div class="container">
            <div class="row">
                <div class="footer-col col-md-5 col-sm-7 col-sm-12 about">
                    <div class="footer-col-inner">
                        <h3 class="title"><?= __('About Us') ?></h3>
                        <p>
                            <?= __('ISAO Corporation, IT company located in Akihabara, Tokyo.') ?><br>
                            <?= __('"Visionary Company which make jobs joyful in the world" is our middle term vision.') ?>
                            <br>
                            <?= __('Bari Flat Model - No managing post, No hierarchy but infinite team strength - is adopted in Japan from 1 Oct 2015.') ?>
                        </p>
                        <p><a class="more" href="http://www.isao.co.jp/" target="_blank"><?= __('Check our website') ?>
                                <i
                                    class="fa fa-long-arrow-right"></i></a></p>
                    </div><!--//footer-col-inner-->
                </div><!--//foooter-col-->
                <div class="footer-col col-md-3 col-sm-4 col-md-offset-1 links">
                    <div class="footer-col-inner">
                        <h3 class="title"><?= __('Other Links') ?></h3>
                        <ul class="list-unstyled">
                            <li><a href="<?= $this->Html->url([
                                    'controller' => 'pages',
                                    'action'     => 'display',
                                    'pagename'   => 'terms',
                                    'lang'       => $top_lang,
                                ]) ?>"><i
                                        class="fa fa-caret-right"></i><?= __('Terms of service') ?></a></li>
                            <li><a href="<?= $this->Html->url([
                                    'controller' => 'pages',
                                    'action'     => 'display',
                                    'pagename'   => 'privacy_policy',
                                    'lang'       => $top_lang,
                                ]) ?>"><i
                                        class="fa fa-caret-right"></i><?= __('Privacy Policy') ?></a></li>
                            <li><a href="<?= $this->Html->url([
                                    'controller' => 'pages',
                                    'action'     => 'display',
                                    'pagename'   => 'law',
                                    'lang'       => $top_lang,
                                ]) ?>"><i
                                        class="fa fa-caret-right"></i><?= __('Inscription by Law') ?></a></li>
                        </ul>
                    </div><!--//footer-col-inner-->
                </div><!--//foooter-col-->
                <div class="footer-col col-md-3 col-sm-12 contact">
                    <div class="footer-col-inner">
                        <h3 class="title"><?= __('Get in touch') ?></h3>
                        <div class="row">
                            <p class="email col-md-12 col-sm-4"><i class="fa fa-envelope"></i><a
                                    href="<?= $this->Html->url([
                                        'controller' => 'pages',
                                        'action'     => 'contact',
                                        'lang'       => $top_lang
                                    ]) ?>"><?= __('Contact us') ?></a></p>
                            <p class="email col-md-12 col-sm-4"><i class="fa fa-microphone"></i><a
                                    href="<?= $this->Html->url([
                                        'controller' => 'pages',
                                        'action'     => 'contact',
                                        'lang'       => $top_lang,
                                        4
                                    ]) ?>"><?= __(
                                        'Contact for press') ?></a>
                            </p>
                            <p class="email col-md-12 col-sm-4"><i class="fa fa-heart"></i><a
                                    href="<?= $this->Html->url([
                                        'controller' => 'pages',
                                        'action'     => 'contact',
                                        'lang'       => $top_lang,
                                        3
                                    ]) ?>"><?= __('Contact for cooporation') ?></a>
                            </p>
                        </div>
                    </div>
                </div>
            </div><!--//footer-col-inner-->
        </div><!--//foooter-col-->
    </div><!--//row-->
    <div class="bottom-bar">
        <div class="container">
            <div class="row">
                <div class="pull-left footer-bottom-left">
                    <small class="copyright">© 2016 ISAO</small>
                    <ul class="footer-lang list-inline">
                        <li><a href="<?= $this->Html->url('/en/') ?>">English (US)</a></li>
                        <li><a href="<?= $this->Html->url('/ja/') ?>">日本語</a></li>
                    </ul>
                </div>
                <ul class="social list-inline pull-right">
                    <!-- <li><a href="http://instagram.com/goalous" target=" _blank"><i class="fa fa-instagram"></i></a></li> -->
                    <li><a href="https://twitter.com/goalous" target=" _blank"><i class="fa fa-twitter"></i></a></li>
                    <li class="last"><a href="https://www.youtube.com/user/Goalous" target=" _blank"><i
                                class="fa fa-youtube"></i></a></li>
                    <li><a href="https://www.facebook.com/goalous/" target=" _blank"><i class="fa fa-facebook"></i></a>
                    </li>
                </ul><!--//social-->
            </div><!--//row-->
        </div><!--//container-->
    </div><!--//bottom-bar-->
</footer>
<div id="layer-black"></div>
<?= $this->App->viewEndComment() ?>
