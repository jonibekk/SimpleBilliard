<?php /**
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @var                    $title_for_layout string
 * @var CodeCompletionView $this
 */
if (!isset($without_footer)) {
    $without_footer = false;
}
if (!isset($with_header_menu)) {
    $with_header_menu = true;
}
?>
<!-- START app/View/Layouts/one_column.ctp -->
<!DOCTYPE html>
<!--suppress ALL -->
<html lang="ja">
<?= $this->element('head') ?>
<body class="<?= $is_mb_app ? 'mb-app-body' : 'body' ?>">
<?php if (extension_loaded('newrelic')) {
    /** @noinspection PhpUndefinedFunctionInspection */
    echo newrelic_get_browser_timing_header();
} ?>
<?= $this->element('google_tag_manager', ['page_type' => 'app']) ?>
<?php if ($this->Session->read('Auth.User.id') && $with_header_menu) {
    echo $this->element('header_logged_in');
} else {
    echo $this->element('header_not_logged_in');
}
?>
<div id="container" class="container">
    <?= $this->Session->flash(); ?>
    <?= $this->fetch('content'); ?>
    <?php if ($this->App->needDisplayFooter()): ?>
        <?= $this->element('footer') ?>
    <?php endif; ?>
</div>
<?= $this->element('common_modules') ?>
<?= $this->element('modals') ?>
<!-- START fetch modal -->
<?= $this->fetch('modal') ?>
<!-- END fetch modal -->
<?= $this->element('gl_common_js') ?>

<!-- START import react code for setup -->
<?php if (viaIsSet($this->request->params['controller']) === 'setup'): ?>
    <?= $this->Html->script('/compiled_assets/js/react_app.min') ?>
<?php endif; ?>
<!-- END import react code for setup -->

<!-- START import react code for signup -->
<?php if(viaIsSet($this->request->params['controller']) === 'signup' && viaIsSet($this->request->params['action']) !== 'email'): ?>
<?= $this->Html->script('/compiled_assets/js/react_signup_app.min')?>
<?php endif; ?>
<!-- END import react code for signup -->

<!-- START import react code for goal create -->
<?php if(viaIsSet($this->request->params['controller']) === 'goals' && viaIsSet($this->request->params['action']) === 'create'): ?>
<?= $this->Html->script('/compiled_assets/js/react_goal_create_app.min')?>
<?php endif; ?>
<!-- END import react code for signup -->

<!-- START import react code for goal edit -->
<?php if(viaIsSet($this->request->params['controller']) === 'goals' && viaIsSet($this->request->params['action']) === 'edit'): ?>
<?= $this->Html->script('/compiled_assets/js/react_goal_edit_app.min')?>
<?php endif; ?>
<!-- END import react code for signup -->

<!-- START import react code for goal approval -->
<?php if(viaIsSet($this->request->params['controller']) === 'goals' && viaIsSet($this->request->params['action']) === 'approval'): ?>
<?= $this->Html->script('/compiled_assets/js/react_goal_approval_app.min')?>
<?php endif; ?>
<!-- END import react code for signup -->

<!-- START fetch script -->
<?= $this->fetch('script') ?>
<!-- END fetch script -->
<?php if (extension_loaded('newrelic')) {
    /** @noinspection PhpUndefinedFunctionInspection */
    echo newrelic_get_browser_timing_footer();
} ?>
</body>
</html>
<!-- END app/View/Layouts/one_column.ctp -->
