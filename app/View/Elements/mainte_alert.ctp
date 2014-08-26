<!-- START app/View/Elements/mainte_alert.ctp -->
<? if (!empty($maintenance) && isset($maintenance['Maintenance'])): ?>
    <div class="alert alert-error">
        <strong><?= h($maintenance['Maintenance']['title']) ?></strong>
        <?= h($maintenance['Maintenance']['body']) ?>
    </div>
<? endif; ?>
<!-- END app/View/Elements/mainte_alert.ctp -->
