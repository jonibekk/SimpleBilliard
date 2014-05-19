<?
/**
 *
 *
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Errors
 * @since         CakePHP(tm) v 0.10.0.1076
 * @var $name string
 * @var $url string
 */
?>
<h2><?= $name; ?></h2>
<p class="error">
    <strong><?= __d('cake', 'Error'); ?>: </strong>
    <? printf(
        __d('cake', 'The requested address %s was not found on this server.'),
        "<strong>'{$url}'</strong>"
    ); ?>
</p>
<?
if (Configure::read('debug') > 0):
    echo $this->element('exception_stack_trace');
endif;
?>
