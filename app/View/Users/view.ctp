<?php
/**
 * @var $this View
 * @var $user array
 */
?>
<div class="users view">
    <h2><?php echo __('User'); ?></h2>
    <dl>
        <dt><?php echo __('Id'); ?></dt>
        <dd>
            <?php echo h($user['User']['id']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Password'); ?></dt>
        <dd>
            <?php echo h($user['User']['password']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Email'); ?></dt>
        <dd>
            <?php echo h($user['User']['email']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Local First Name'); ?></dt>
        <dd>
            <?php echo h($user['User']['local_first_name']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Local Last Name'); ?></dt>
        <dd>
            <?php echo h($user['User']['local_last_name']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('First Name'); ?></dt>
        <dd>
            <?php echo h($user['User']['first_name']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Last Name'); ?></dt>
        <dd>
            <?php echo h($user['User']['last_name']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Created'); ?></dt>
        <dd>
            <?php echo h($user['User']['created']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Modified'); ?></dt>
        <dd>
            <?php echo h($user['User']['modified']); ?>
            &nbsp;
        </dd>
    </dl>
</div>
<div class="actions">
    <h3><?php echo __('Actions'); ?></h3>
    <ul>
        <li><?php echo $this->Html->link(__('Edit User'), ['action' => 'edit', $user['User']['id']]); ?> </li>
        <li><?php echo $this->Form->postLink(__('Delete User'), ['action' => 'delete', $user['User']['id']], [],
                                             __('Are you sure you want to delete # %s?', $user['User']['id'])); ?> </li>
        <li><?php echo $this->Html->link(__('List Users'), ['action' => 'index']); ?> </li>
        <li><?php echo $this->Html->link(__('New User'), ['action' => 'add']); ?> </li>
    </ul>
</div>
