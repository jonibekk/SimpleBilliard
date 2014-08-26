<?
/**
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Errors
 * @since         CakePHP(tm) v 0.10.0.1076
 * @var $name string
 */
?>
<div class="jumbotron jumbotron-icon text-center">
    <i class="fa-warning fa fa-5"></i>

    <h1>500</h1>

    <p><?= $name; ?></p>
</div>
<?
if (Configure::read('debug') > 0):
    echo $this->element('exception_stack_trace');
endif;
?>
