<!DOCTYPE html>
<!--suppress ALL -->
<html lang="<?= $this->Lang->getLangCode() ?>">
<head>
<?php
echo $this->Html->css('vendors.min', ['fullBase' => true]);
echo $this->Html->css('common.min', ['fullBase' => true]);
echo $this->Html->css('receipt.min', ['fullBase' => true]);
?>
</head>
<body class="body">
  <div class="pdf-container">
      <?= $this->fetch('content'); ?>
  </div>
</body>
</html>
<?= $this->App->viewEndComment() ?>
