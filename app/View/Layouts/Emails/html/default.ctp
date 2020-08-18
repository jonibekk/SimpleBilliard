<?php /**
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Layouts.Email.html
 * @since         CakePHP(tm) v 0.10.0.1076
 * @var $title_for_layout string
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html>
<head>
    <title><?= $title_for_layout; ?></title>
</head>
<body>
<?= $this->fetch('content'); ?>

<p>This email was sent using the <a href="http://cakephp.org">CakePHP Framework</a></p>
</body>
</html>