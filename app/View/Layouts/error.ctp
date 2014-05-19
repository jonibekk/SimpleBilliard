<?
/**
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @var $title_for_layout string
 */

$cakeDescription = __d('cake_dev', 'CakePHP: the rapid development php framework');
?>
<!DOCTYPE html>
<html>
<head>
    <?= $this->Html->charset(); ?>
    <title>
        <?= $cakeDescription ?>:
        <?= $title_for_layout; ?>
    </title>
    <?
    echo $this->Html->meta('icon');

    echo $this->Html->css('cake.generic');

    echo $this->fetch('meta');
    echo $this->fetch('css');
    echo $this->fetch('script');
    ?>
</head>
<body>
<div id="container">
    <div id="header">
        <h1><?= $this->Html->link($cakeDescription, 'http://cakephp.org'); ?></h1>
    </div>
    <div id="content">

        <?= $this->Session->flash(); ?>

        <?= $this->fetch('content'); ?>
    </div>
    <div id="footer">
        <?=
        $this->Html->link(
                   $this->Html->image('cake.power.gif', array('alt' => $cakeDescription, 'border' => '0')),
                   'http://www.cakephp.org/',
                   array('target' => '_blank', 'escape' => false)
        );
        ?>
    </div>
</div>
<?= $this->element('sql_dump'); ?>
</body>
</html>
