<?
/**
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @var $page_title string
 * @var $url        string
 * @var $pause      string
 * @var $message    string
 */
?>
<!DOCTYPE html>
<html>
<head>
    <?= $this->Html->charset(); ?>
    <title><?= $page_title; ?></title>

    <? if (Configure::read('debug') == 0): ?>
        <meta http-equiv="Refresh" content="<?= $pause; ?>;url=<?= $url; ?>"/>
    <? endif ?>
    <style><!--
        P {
            text-align: center;
            font: bold 1.1em sans-serif
        }

        A {
            color: #444;
            text-decoration: none
        }

        A:HOVER {
            text-decoration: underline;
            color: #44E
        }

        --></style>
</head>
<body>
<p><a href="<?= $url; ?>"><?= $message; ?></a></p>
</body>
</html>
