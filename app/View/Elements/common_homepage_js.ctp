<?php
/**
 * @var CodeCompletionView $this
 */
?>
<!-- START app/View/Elements/common_homepage_js.ctp -->
<?= $this->Html->script('vendor/jquery-1.11.1.min') ?>
<?= $this->Html->script('vendor/bootstrap.min') ?>
<?= $this->Html->script('vendor/pnotify.custom.min') ?>
<?= $this->Html->script('homepage/jquery-migrate-1.2.1.min') ?>
<?= $this->Html->script('homepage/bootstrap-hover-dropdown.min') ?>
<?= $this->Html->script('homepage/jquery.inview.min') ?>
<?= $this->Html->script('homepage/isMobile') ?>
<?= $this->Html->script('homepage/back-to-top') ?>
<?= $this->Html->script('homepage/jquery.placeholder') ?>
<?= $this->Html->script('homepage/jquery.fitvids') ?>
<?= $this->Html->script('homepage/jquery.flexslider-min') ?>
<?= $this->Html->script('homepage/marked.min') ?>
<?= $this->Html->script('homepage/main') ?>
<?= $this->Html->script('homepage/froogaloop2.min') ?>
<?= $this->Html->script('homepage/vimeo') ?>

<!--[if !IE]>-->
<?= $this->Html->script('homepage/animations') ?>
<!--<![endif]-->

<script>
    document.getElementById('law-mark').innerHTML = marked('#できてない');
</script>

<!-- END app/View/Elements/common_homepage_js.ctp -->
