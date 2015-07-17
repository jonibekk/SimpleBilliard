<?php
/**
 * @var $key_results
 */
?>
<?php if ($key_results): ?>
    <?php foreach ($key_results as $kr): ?>
        <div style="border: 3px solid #ccc; padding:3px; margin:3px;">
            <div class="pull-right">
                <?php if ($kr['KeyResult']['completed']): ?>
                    <?= __d('gl', 'クリア') ?><br>
                <?php endif ?>

                <i class="fa fa-check-circle"></i>
                <?= h($kr['KeyResult']['action_result_count']) ?>
            </div>

            <div>
                <?= __d('gl', '内容') ?>: <?= $this->Html->link($kr['KeyResult']['name'],
                                          ['controller'    => 'goals',
                                           'action'        => 'ajax_get_edit_key_result_modal',
                                           'key_result_id' => $kr['KeyResult']['id']],
                                          ['class' => 'modal-ajax-get-add-key-result']) ?><br>
                <?= __d('gl', '期限') ?>: <?= $this->Time->format('Y/m/d', $kr['KeyResult']['end_date']) ?>
            </div>
        </div>
    <?php endforeach ?>
<?php endif ?>