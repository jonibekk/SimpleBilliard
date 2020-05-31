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
<section class="<?= $is_mb_app && isset($_GET['backBtn']) ? 'hide' : null ?>">
    <div class="cnt_container_footer">
        <dl class="footerLink">
            <div class="aboutus">
                <dt class="first"><?= __('About Us') ?></dt>
                <dd><?= __('Colorkrew Inc., An IT corporation located in Akihabara, Tokyo.') ?><br>
                    <?= __('Our mid-term vision to be “a visionary company that brings joy to work around the world”,') ?>
                    <br>
                    <?= __('introduced the first Super Flat model to Japan. No management, No hierarchy, but infinite team strength.') ?>
                </dd>
                <dd><a href="https://www.colorkrew.com/" class="arrow textlink" target="_blank"><?= __('Check our website') ?></a></dd>
            </div>
            <div class="otherlinks">
                <dt><?= __('Other Links') ?></dt>
                <dd><a href="<?= $this->Html->url([
                        'controller' => 'pages',
                        'action'     => 'lp',
                        'pagename'   => 'terms',
                        'lang'       => $top_lang,
                    ]) ?>"><?= __('Terms of service') ?></a></dd>
                <dd><a href="<?= $this->Html->url([
                        'controller' => 'pages',
                        'action'     => 'lp',
                        'pagename'   => 'privacy_policy',
                        'lang'       => $top_lang,
                    ]) ?>"><?= __('Privacy Policy') ?></a></dd>
                <dd><a href="<?= $this->Html->url([
                        'controller' => 'pages',
                        'action'     => 'lp',
                        'pagename'   => 'law',
                        'lang'       => $top_lang,
                    ]) ?>"><?= __('Inscription by Law') ?></a></dd>
            </div>
            <div class="getintouch">
                <dt><?= __('Get in touch') ?></dt>
                <dd><a
                        href="<?= $this->Html->url([
                            'controller' => 'pages',
                            'action'     => 'contact',
                            'lang'       => $top_lang
                        ]) ?>"><?= __('Contact us') ?></a></dd>
            </div>
        </dl>
        <div class="footerCopy">
            <p>© 2020 Colorkrew</p>
            <ul class="language">
                <li><a href="<?= $this->Html->url('/en/') ?>">English(US)</a></li>
                <li><a href="<?= $this->Html->url('/ja/') ?>">日本語</a></li>
            </ul>
            <ul class="sns">
                <li><a href="https://twitter.com/goalous" target="_blank"><img src="/img/homepage/top/sns_icon_twitter.png" height="40px" alt="公式Twitter"></a></li>
                <li><a href="https://www.facebook.com/goalous/" target="_blank"><img src="/img/homepage/top/sns_icon_facebook.png" height="40px" alt="公式Facebook"></a>
                </li>
                <li><a href="https://www.youtube.com/user/Goalous" target="_blank"><img src="/img/homepage/top/sns_icon_youtube.png" height="40px" alt="公式Youtube"></a></li>
            </ul>
        </div>
    </div>
</section>

<?= $this->App->viewEndComment() ?>
