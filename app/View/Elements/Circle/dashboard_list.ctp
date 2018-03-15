<?= $this->App->viewStartComment() ?>
<?= $this->element('Circle/dashboard_list_element', ['circle' => $defaultCircle, 'isHamburger' => false]) ?>
<?php foreach ($circles as $circle): ?>
  <?php if(isset($circle['order'])): ?>
    <?= $this->element('Circle/dashboard_list_element', ['circle' => $circle, 'isHamburger' => false]) ?>
  <?php elseif(!isset($circle['order'])): ?>
    <?= $this->element('Circle/dashboard_list_element', ['circle' => $circle, 'isHamburger' => false]) ?>
  <?php endif; ?>
<?php endforeach ?>
<?= $this->App->viewEndComment() ?>
