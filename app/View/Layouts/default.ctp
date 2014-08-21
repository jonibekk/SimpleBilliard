<?
/**
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @var $title_for_layout string
 * @var $this             View
 */
?>
<!-- START app/View/Layouts/default.ctp -->
<!DOCTYPE html>
<!--suppress ALL -->
<html lang="en">
<?= $this->element('head') ?>
<body>
<? if (extension_loaded('newrelic')) {
    /** @noinspection PhpUndefinedFunctionInspection */
    echo newrelic_get_browser_timing_header();
} ?>
<?
if ($this->Session->read('Auth.User.id')) {
    echo $this->element('header_logged_in');
}
else {
    echo $this->element('header_not_logged_in');
}
?>
<div id="container" class="container">
    <div class="row">
        <div class="col-md-2 col-sm-4 col-xs-4 hidden-xxs layout-sub">
            <div class="well layout-sub_padding design-mystatus">
                <p>test</p>

                <p>test</p>

                <p>test</p>

                <p>test</p>

                <p>test</p>

                <p>test</p>
            </div>
            <?= $this->element('circle_list') ?>

        </div>
        <div class="col-md-6 col-sm-8 col-xs-8 col-xxs-12 layout-main" role="main">
            <?= $this->Session->flash(); ?>
            <?= $this->fetch('content'); ?>
        </div>
        <div class="col-md-4 visible-md visible-lg layout-goal">
            <img src="../img/develop--goals-column.jpg" class="develop--goals-column" alt="ちゃんと消しますよ？">
            <!--
                        <div class="well layout-goal_padding design-goal">
                            <p>test</p>

                            <p>test</p>

                            <p>test</p>

                            <p>test</p>

                            <p>test</p>

                            <p>test</p>
                        </div>
            -->
        </div>
    </div>

    <?= $this->element('footer') ?>
</div>
<? if (extension_loaded('newrelic')) {
    /** @noinspection PhpUndefinedFunctionInspection */
    echo newrelic_get_browser_timing_footer();
} ?>
<?= $this->element('modals') ?>
<?= $this->element('gl_common_js') ?>
<?= $this->fetch('script') ?>
</body>
</html>
<!-- END app/View/Layouts/default.ctp -->
