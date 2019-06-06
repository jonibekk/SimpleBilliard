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
<?php
/*
Page毎に要素が変わるもの
- meta description
- og:description
- og:url
- title
*/
$meta_contact_confirm = [
    [
        "name"    => "description",
        "content"  => __('Goalous is one of the best team communication tools. Let your team open. Your action will be share with your collegues. %s',__("It is a confirmation of your inquiry.")),
    ],
    [
        "name"    => "keywords",
        "content" => __("goal management, achieve a goal, sns app, evaluation, mbo"),
    ],
    [
        "property" => "og:type",
        "content"  => "website",
    ],
    [
        "property" => "og:title",
        "content"  => __('Confirming | Goalous'),
    ],
    [
        "property" => "og:description",
        "content"  => __('Goalous is one of the best team communication tools. Let your team open. Your action will be share with your collegues. %s',__("It is a confirmation of your inquiry.")),
    ],
    [
        "property" => "og:url",
        "content"  => "https://www.goalous.com/contact_confirm/",
    ],
    [
        "property" => "og:image",
        "content"  => AppUtil::fullBaseUrl(ENV_NAME)."/img/homepage/promo.jpg",
    ],
    [
        "property" => "og:site_name",
        "content"  => __('Goalous │ Enterprise SNS the most ever open for Goal'),
    ],
    [
        "property" => "fb:app_id",
        "content"  => "966242223397117",
    ],
    [
        "name"    => "twitter_card",
        "content" => "summary",
    ],
    [
        "name"    => "twitter:site",
        "content" => "@goalous",
    ]
];
$num_ogp = count($meta_contact_confirm);
for ($i = 0; $i < $num_ogp; $i++) {
    echo $this->Html->meta($meta_contact_confirm[$i]);
}
?>
<title><?= __('Confirming | Goalous') ?></title>
<link rel="alternate" hreflang="ja" href="<?= $this->Html->url('/ja/contact_confirm') ?>"/>
<link rel="alternate" hreflang="en" href="<?= $this->Html->url('/en/contact_confirm') ?>"/>
<link rel="alternate" hreflang="x-default" href="<?= $this->Html->url('/contact_confirm') ?>"/>
<?php $this->end() ?>
<?= $this->App->viewStartComment()?>
<!-- ******CONTACT MAIN****** -->
<section id="contact-main" class="contact-main section">
    <div class="container text-center">
        <h2 class="title"><?= __('Confirm what you ask us') ?></h2>
        <p class="intro"><?= __('Click send, after checking your contents.') ?></p>
    </div><!--//container-->
</section>

<section class="container contact-form-section">
    <div class="row">
        <div class="col-md-8 col-md-offset-2 text-left">
            <form class="form-horizontal">
                <div class="form-group">
                    <label class="col-sm-4 control-label"><?= __('Last Name ') ?></label>
                    <div class="col-sm-8">
                        <p class="form-control-static"><?= h($data['name_last'] ?? '') ?></p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label"><?= __('First Name ') ?></label>
                    <div class="col-sm-8">
                        <p class="form-control-static"><?= h($data['name_first'] ?? '') ?></p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label"><?= __('Your Work Email Address') ?></label>
                    <div class="col-sm-8">
                        <p class="form-control-static"><?= h($data['email'] ?? '') ?></p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label"><?= __('Phone Number (Optional)') ?></label>
                    <div class="col-sm-8">
                        <p class="form-control-static"><?= h($data['phone'] ?? '') ?></p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label"><?= __('Company Name (Optional)') ?></label>
                    <div class="col-sm-8">
                        <p class="form-control-static"><?= h($data['company'] ?? '') ?></p>
                    </div>
                </div>

                <a href="<?= $this->Html->url([
                    'controller' => 'pages',
                    'action'     => 'contact_send',
                    'lang'       => $top_lang
                ]) ?>"
                   class="btn btn-block btn-cta-primary contact-confirm-send" id="SendContactLink"><?= __(
                        'Send') ?></a>
                <a href="<?= $this->Html->url([
                    'controller'   => 'pages',
                    'action'       => 'contact',
                    'lang'         => $top_lang,
                    'from_confirm' => true
                ]) ?>"
                   class="btn btn-block btn-cta-secondary"><?= __('Back') ?></a>
            </form><!--//form-->
        </div>
    </div><!--//row-->
</section><!--//contact--><?= $this->App->viewEndComment()?>
<?php $this->append('script'); ?>
<script type="text/javascript">
    $(function () {
        $('#SendContactLink').on('click', function () {
            if ($(this).hasClass('double_click')) {
                return false;
            }
            $(this).text("<?=__('Sending')?>");
            // 2重送信防止クラス
            $(this).addClass('double_click');
        });
    })
</script>
<?php $this->end(); ?>
