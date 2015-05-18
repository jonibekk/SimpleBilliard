<!-- START app/View/Elements/mainte_alert.ctp -->
<?php if (!empty($maintenance) && isset($maintenance['Maintenance'])): ?>
    <div class="alert alert-error">
        <strong><?= h($maintenance['Maintenance']['title']) ?></strong>
        <?= h($maintenance['Maintenance']['body']) ?>
    </div>
<?php endif; ?>
<!-- END app/View/Elements/mainte_alert.ctp -->
