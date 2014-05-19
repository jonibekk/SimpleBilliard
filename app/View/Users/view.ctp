<?
/**
 * @var $this View
 * @var $user array
 */
?>
<div class="users view">
    <h2><?= __('User'); ?></h2>
    <dl>
        <dt><?= __('Id'); ?></dt>
        <dd>
            <?= h($user['User']['id']); ?>
            &nbsp;
        </dd>
        <dt><?= __('Password'); ?></dt>
        <dd>
            <?= h($user['User']['password']); ?>
            &nbsp;
        </dd>
        <dt><?= __('Email'); ?></dt>
        <dd>
            <?= h($user['User']['email']); ?>
            &nbsp;
        </dd>
        <dt><?= __('Local First Name'); ?></dt>
        <dd>
            <?= h($user['User']['local_first_name']); ?>
            &nbsp;
        </dd>
        <dt><?= __('Local Last Name'); ?></dt>
        <dd>
            <?= h($user['User']['local_last_name']); ?>
            &nbsp;
        </dd>
        <dt><?= __('First Name'); ?></dt>
        <dd>
            <?= h($user['User']['first_name']); ?>
            &nbsp;
        </dd>
        <dt><?= __('Last Name'); ?></dt>
        <dd>
            <?= h($user['User']['last_name']); ?>
            &nbsp;
        </dd>
        <dt><?= __('Created'); ?></dt>
        <dd>
            <?= h($user['User']['created']); ?>
            &nbsp;
        </dd>
        <dt><?= __('Modified'); ?></dt>
        <dd>
            <?= h($user['User']['modified']); ?>
            &nbsp;
        </dd>
    </dl>
</div>
<div class="actions">
    <h3><?= __('Actions'); ?></h3>
    <ul>
        <li><?= $this->Html->link(__('Edit User'), ['action' => 'edit', $user['User']['id']]); ?> </li>
        <li><?=
            $this->Form->postLink(__('Delete User'), ['action' => 'delete', $user['User']['id']], [],
                                  __('Are you sure you want to delete # %s?', $user['User']['id'])); ?> </li>
        <li><?= $this->Html->link(__('List Users'), ['action' => 'index']); ?> </li>
        <li><?= $this->Html->link(__('New User'), ['action' => 'add']); ?> </li>
    </ul>
</div>
