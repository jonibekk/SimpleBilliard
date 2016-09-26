<?php
/**
 * @var CodeCompletionView $this
 * @var                    $maintenance
 */
?>
<?= $this->App->viewStartComment()?>
<?php if (!empty($maintenance) && isset($maintenance['Maintenance'])): ?>
    <div class="alert alert-error">
        <strong><?= h($maintenance['Maintenance']['title']) ?></strong>
        <?= h($maintenance['Maintenance']['body']) ?>
    </div>
<?php endif; ?>
<?= $this->App->viewEndComment()?>
