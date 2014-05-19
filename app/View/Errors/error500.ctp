<?
/**
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Errors
 * @since         CakePHP(tm) v 0.10.0.1076
 * @var $name string
 */
?>
<h2><?= $name; ?></h2>
<p class="error">
    <strong><?= __d('cake', 'Error'); ?>: </strong>
    <?= __d('cake', 'An Internal Error Has Occurred.'); ?>
</p>
<?
if (Configure::read('debug') > 0):
    echo $this->element('exception_stack_trace');
endif;
?>
