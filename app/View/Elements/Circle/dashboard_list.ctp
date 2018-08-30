<?= $this->App->viewStartComment() ?>
<ul id="dashboard-defaultpin">
<?= $this->element('Circle/dashboard_list_element', ['circle' => $defaultCircle, 'isHamburger' => $isHamburger]) ?>
</ul>
<ul id="dashboard-pinned">
<?php foreach ($circles as $circle): ?>
  <?php if(isset($circle['order'])): ?>
    <?= $this->element('Circle/dashboard_list_element', ['circle' => $circle, 'isHamburger' => $isHamburger]) ?>
  <?php endif; ?>
<?php endforeach ?>
</ul>
<div class="dashboard-pin-circle-border"></div>
<ul id="dashboard-unpinned">
<?php foreach ($circles as $circle): ?>
  <?php if(!isset($circle['order'])): ?>
    <?= $this->element('Circle/dashboard_list_element', ['circle' => $circle, 'isHamburger' => $isHamburger]) ?>
  <?php endif; ?>
<?php endforeach ?>
</ul>
<?= $this->App->viewEndComment() ?>
