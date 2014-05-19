<?
/**
 * @var $users array
 * @var $this  View
 */
?>
<div class="users index">
    <h2><?= __('Users'); ?></h2>
    <table cellpadding="0" cellspacing="0">
        <tr>
            <th><?= $this->Paginator->sort('id'); ?></th>
            <th><?= $this->Paginator->sort('local_first_name'); ?></th>
            <th><?= $this->Paginator->sort('local_last_name'); ?></th>
            <th><?= $this->Paginator->sort('first_name'); ?></th>
            <th><?= $this->Paginator->sort('last_name'); ?></th>
            <th><?= $this->Paginator->sort('created'); ?></th>
            <th><?= $this->Paginator->sort('modified'); ?></th>
            <th class="actions"><?= __('Actions'); ?></th>
        </tr>
        <?
        foreach ($users as $user): ?>
            <tr>
                <td><?= h($user['User']['id']); ?>&nbsp;</td>
                <td><?= h($user['User']['local_first_name']); ?>&nbsp;</td>
                <td><?= h($user['User']['local_last_name']); ?>&nbsp;</td>
                <td><?= h($user['User']['first_name']); ?>&nbsp;</td>
                <td><?= h($user['User']['last_name']); ?>&nbsp;</td>
                <td><?= h($user['User']['created']); ?>&nbsp;</td>
                <td><?= h($user['User']['modified']); ?>&nbsp;</td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $user['User']['id']]); ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $user['User']['id']]); ?>
                    <?=
                    $this->Form->postLink(__('Delete'), ['action' => 'delete', $user['User']['id']],
                                          [], __('Are you sure you want to delete # %s?',
                                                 $user['User']['id'])); ?>
                </td>
            </tr>
        <? endforeach; ?>
    </table>
    <p>
        <?
        /** @noinspection PhpDeprecationInspection */
        echo $this->Paginator->counter(['format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')]);
        ?>    </p>

    <div class="paging">
        <?
        echo $this->Paginator->prev('< ' . __('previous'), [], null, ['class' => 'prev disabled']);
        echo $this->Paginator->numbers(['separator' => '']);
        echo $this->Paginator->next(__('next') . ' >', [], null, ['class' => 'next disabled']);
        ?>
    </div>
</div>
<div class="actions">
    <h3><?= __('Actions'); ?></h3>
    <ul>
        <li><?= $this->Html->link(__('New User'), ['action' => 'add']); ?></li>
    </ul>
</div>
