<?php if (!empty($maintenance) && isset($maintenance['Maintenance'])): ?>
    <div class="alert alert-error">
        <strong><?php echo h($maintenance['Maintenance']['title']) ?></strong>
        <?php echo h($maintenance['Maintenance']['body']) ?>
    </div>
<?php endif; ?>